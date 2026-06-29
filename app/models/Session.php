<?php
// app/models/Session.php

class Session
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findById(int $id): ?array
    {
        return $this->db->single(
            "SELECT s.*, c.name AS course_name, c.code AS course_code,
                    u.name AS lecturer_name,
                    COUNT(a.id) AS attendance_count
               FROM sessions s
               JOIN courses   c ON c.id = s.course_id
               JOIN lecturers l ON l.id = s.lecturer_id
               JOIN users     u ON u.id = l.user_id
               LEFT JOIN attendance a ON a.session_id = s.id
              WHERE s.id = ?
              GROUP BY s.id",
            [$id]
        );
    }

    public function findByToken(string $token): ?array
    {
        return $this->db->single(
            "SELECT * FROM sessions WHERE qr_token = ? AND status = 'active' LIMIT 1",
            [$token]
        );
    }

    public function allByLecturer(int $lecturerId, int $limit = 50): array
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

    public function allByCourse(int $courseId): array
    {
        return $this->db->all(
            "SELECT s.*, COUNT(a.id) AS attendance_count
               FROM sessions s
               LEFT JOIN attendance a ON a.session_id = s.id
              WHERE s.course_id = ?
              GROUP BY s.id
              ORDER BY s.session_date DESC",
            [$courseId]
        );
    }

    public function create(array $data): int|string
    {
        return $this->db->insert(
            "INSERT INTO sessions
                (course_id, lecturer_id, title, session_date, start_time,
                 end_time, qr_token, qr_expires_at, qr_image_path, status)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')",
            [
                $data['course_id'],
                $data['lecturer_id'],
                $data['title']         ?? null,
                $data['session_date'],
                $data['start_time'],
                $data['end_time'],
                $data['qr_token'],
                $data['qr_expires_at'],
                $data['qr_image_path'] ?? null,
            ]
        );
    }

    public function activate(int $id, int $lecturerId): bool
    {
        return $this->db->execute(
            "UPDATE sessions SET status = 'active'
              WHERE id = ? AND lecturer_id = ? AND status = 'pending'",
            [$id, $lecturerId]
        ) > 0;
    }

    public function close(int $id, int $lecturerId): bool
    {
        return $this->db->execute(
            "UPDATE sessions SET status = 'closed'
              WHERE id = ? AND lecturer_id = ? AND status = 'active'",
            [$id, $lecturerId]
        ) > 0;
    }

    public function updateQrPath(int $id, string $path): void
    {
        $this->db->execute("UPDATE sessions SET qr_image_path = ? WHERE id = ?", [$path, $id]);
    }

    public function autoCloseExpired(): int
    {
        return $this->db->execute(
            "UPDATE sessions SET status = 'closed'
              WHERE status = 'active' AND qr_expires_at < NOW()"
        );
    }

    public function count(): int
    {
        return (int)$this->db->scalar("SELECT COUNT(*) FROM sessions");
    }

    public function countToday(): int
    {
        return (int)$this->db->scalar(
            "SELECT COUNT(*) FROM sessions WHERE DATE(created_at) = CURDATE()"
        );
    }
}
