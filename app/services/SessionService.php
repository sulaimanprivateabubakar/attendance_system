<?php
// app/services/SessionService.php

class SessionService
{
    private Database $db;
    private string   $qrSecret;
    private int      $qrExpiryMinutes;

    public function __construct()
    {
        $this->db              = Database::getInstance();
        $this->qrSecret        = $_ENV['QR_SECRET']          ?? 'default_secret_change_me';
        $this->qrExpiryMinutes = (int)($_ENV['QR_EXPIRY_MINUTES'] ?? 15);
    }

    // ── Create a session ─────────────────────────────────────────────────────

    public function createSession(int $lecturerId, array $data): array
    {
        // Validate
        foreach (['course_id','session_date','start_time','end_time'] as $f) {
            if (empty($data[$f])) {
                return ['success' => false, 'error' => "Field '{$f}' is required."];
            }
        }

        // Verify lecturer owns this course
        $course = $this->db->single(
            "SELECT id FROM courses WHERE id = ? AND lecturer_id = ? AND is_active = 1",
            [$data['course_id'], $lecturerId]
        );
        if (!$course) {
            return ['success' => false, 'error' => 'Course not found or not assigned to you.'];
        }

        $token     = $this->generateToken();
        $expiresAt = date('Y-m-d H:i:s',
            strtotime($data['session_date'] . ' ' . $data['end_time'])
            + ($this->qrExpiryMinutes * 60)
        );

        $sessionId = $this->db->insert(
            "INSERT INTO sessions
                (course_id, lecturer_id, title, session_date, start_time, end_time,
                 qr_token, qr_expires_at, status)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')",
            [
                $data['course_id'],
                $lecturerId,
                $data['title'] ?? null,
                $data['session_date'],
                $data['start_time'],
                $data['end_time'],
                $token,
                $expiresAt,
            ]
        );

        // Generate QR image and store path
        $qrPath = $this->generateQrImage($sessionId, $token);
        if ($qrPath) {
            $this->db->execute(
                "UPDATE sessions SET qr_image_path = ? WHERE id = ?",
                [$qrPath, $sessionId]
            );
        }

        return ['success' => true, 'session_id' => $sessionId, 'token' => $token];
    }

    // ── Activate / Close ─────────────────────────────────────────────────────

    public function activateSession(int $sessionId, int $lecturerId): bool
    {
        $affected = $this->db->execute(
            "UPDATE sessions SET status = 'active'
              WHERE id = ? AND lecturer_id = ? AND status = 'pending'",
            [$sessionId, $lecturerId]
        );
        return $affected > 0;
    }

    public function closeSession(int $sessionId, int $lecturerId): bool
    {
        $affected = $this->db->execute(
            "UPDATE sessions SET status = 'closed'
              WHERE id = ? AND lecturer_id = ? AND status = 'active'",
            [$sessionId, $lecturerId]
        );
        return $affected > 0;
    }

    // ── Getters ──────────────────────────────────────────────────────────────

    public function getSessionsByLecturer(int $lecturerId, int $limit = 50): array
    {
        return $this->db->all(
            "SELECT s.*, c.name AS course_name, c.code AS course_code,
                    COUNT(a.id) AS attendance_count
               FROM sessions s
               JOIN courses c ON c.id = s.course_id
               LEFT JOIN attendance a ON a.session_id = s.id
              WHERE s.lecturer_id = ?
              GROUP BY s.id
              ORDER BY s.session_date DESC, s.start_time DESC
              LIMIT ?",
            [$lecturerId, $limit]
        );
    }

    public function getSessionWithDetails(int $sessionId): ?array
    {
        return $this->db->single(
            "SELECT s.*,
                    c.name AS course_name, c.code AS course_code,
                    u.name AS lecturer_name,
                    COUNT(a.id) AS attendance_count
               FROM sessions s
               JOIN courses c   ON c.id = s.course_id
               JOIN lecturers l ON l.id = s.lecturer_id
               JOIN users u     ON u.id = l.user_id
               LEFT JOIN attendance a ON a.session_id = s.id
              WHERE s.id = ?
              GROUP BY s.id",
            [$sessionId]
        );
    }

    public function getSessionByToken(string $token): ?array
    {
        return $this->db->single(
            "SELECT * FROM sessions WHERE qr_token = ? AND status = 'active' LIMIT 1",
            [$token]
        );
    }

    // ── QR helpers ───────────────────────────────────────────────────────────

    /**
     * Build the URL that gets encoded inside the QR image.
     * The URL hits the attendance scan endpoint with a signed token.
     */
    public function buildScanUrl(string $token): string
    {
        $appUrl = rtrim($_ENV['APP_URL'] ?? 'http://localhost', '/');
        return $appUrl . '/attend/' . urlencode($token);
    }

    /**
     * Generate a QR image using the endroid/qr-code library (via Composer).
     * Falls back to a Google Chart API URL if the library isn't installed.
     * Returns the relative storage path, or null on failure.
     */
    private function generateQrImage(int $sessionId, string $token): ?string
    {
        $scanUrl  = $this->buildScanUrl($token);
        $filename = "session_{$sessionId}_{$token}.png";
        $dir      = ROOT_PATH . '/storage/qr_codes/';
        $fullPath = $dir . $filename;

        // Use endroid/qr-code if available
        if (class_exists('Endroid\QrCode\QrCode')) {
            try {
                $qrCode = \Endroid\QrCode\QrCode::create($scanUrl)
                    ->setSize(300)
                    ->setMargin(10);

                $writer = new \Endroid\QrCode\Writer\PngWriter();
                $result = $writer->write($qrCode);
                $result->saveToFile($fullPath);
                return 'storage/qr_codes/' . $filename;
            } catch (Throwable $e) {
                error_log('QR generation failed: ' . $e->getMessage());
                return null;
            }
        }

        // Fallback: store the Google Chart URL as a text file reference
        // (Replace with real library in production)
        $fallbackUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data='
                       . urlencode($scanUrl);
        file_put_contents($dir . "session_{$sessionId}.url", $fallbackUrl);
        return null; // real path not saved; controller uses fallback URL
    }

    // ── Token generation ─────────────────────────────────────────────────────

    private function generateToken(): string
    {
        return hash_hmac('sha256', uniqid('', true) . random_bytes(16), $this->qrSecret);
    }
}
