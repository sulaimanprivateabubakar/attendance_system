<?php
// app/services/AttendanceService.php

class AttendanceService
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // ── Record a scan ────────────────────────────────────────────────────────

    /**
     * Validate and record a student's QR scan.
     *
     * @return array ['success'=>bool, 'message'=>string, 'status'=>'present'|'late']
     */
    public function recordScan(string $token, int $studentId, string $ip, string $userAgent): array
    {
        // 1. Find active session by token
        $session = $this->db->single(
    "SELECT * FROM sessions
      WHERE qr_token = ?
        AND status = 'active'
      LIMIT 1",
    [$token]
);

if (!$session) {
    return ['success' => false, 'message' => 'QR code is invalid or session is not active.'];
}

if (strtotime($session['qr_expires_at']) < time()) {
    return ['success' => false, 'message' => 'This QR code has expired. Ask your lecturer to extend the session.'];
}

        // 2. Check enrollment
        $enrolled = $this->db->single(
            "SELECT e.id FROM enrollments e
               JOIN students s ON s.id = e.student_id
              WHERE s.user_id = ?
                AND e.course_id = ?
              LIMIT 1",
            [$studentId, $session['course_id']]
        );

        if (!$enrolled) {
            return ['success' => false, 'message' => 'You are not enrolled in this course.'];
        }

        // Get student record
        $student = $this->db->single(
            "SELECT id FROM students WHERE user_id = ? LIMIT 1",
            [$studentId]
        );

        // 3. Check for duplicate scan
        $already = $this->db->single(
            "SELECT id FROM attendance WHERE session_id = ? AND student_id = ? LIMIT 1",
            [$session['id'], $student['id']]
        );

        if ($already) {
            return ['success' => false, 'message' => 'You have already marked attendance for this session.'];
        }

        // 4. Determine if on time or late
        $nowTime      = date('H:i:s');
        $graceMinutes = 15;
        $lateThreshold = date('H:i:s', strtotime($session['start_time']) + ($graceMinutes * 60));
        $status = ($nowTime > $lateThreshold) ? 'late' : 'present';

        // 5. Insert attendance record
        $this->db->insert(
            "INSERT INTO attendance (session_id, student_id, ip_address, device_info, status)
             VALUES (?, ?, ?, ?, ?)",
            [
                $session['id'],
                $student['id'],
                $ip,
                substr($userAgent, 0, 255),
                $status,
            ]
        );

        $message = $status === 'late'
            ? 'Attendance marked as LATE.'
            : 'Attendance marked successfully!';

        return ['success' => true, 'message' => $message, 'status' => $status];
    }

    // ── Reports ──────────────────────────────────────────────────────────────

    public function getSessionAttendance(int $sessionId): array
    {
        return $this->db->all(
            "SELECT u.name, s.student_number, a.scanned_at, a.status
               FROM attendance a
               JOIN students s ON s.id = a.student_id
               JOIN users u    ON u.id = s.user_id
              WHERE a.session_id = ?
              ORDER BY a.scanned_at ASC",
            [$sessionId]
        );
    }

    public function getStudentAttendance(int $studentUserId, int $courseId): array
    {
        return $this->db->all(
            "SELECT sess.session_date, sess.start_time, sess.title,
                    COALESCE(a.status, 'absent') AS status,
                    a.scanned_at
               FROM sessions sess
               LEFT JOIN students st ON st.user_id = ?
               LEFT JOIN attendance a ON a.session_id = sess.id AND a.student_id = st.id
              WHERE sess.course_id = ?
                AND sess.status = 'closed'
              ORDER BY sess.session_date DESC",
            [$studentUserId, $courseId]
        );
    }

    /**
     * Summary stats for a course: attendance rate per student.
     */
    public function getCourseAttendanceSummary(int $courseId): array
    {
        return $this->db->all(
            "SELECT u.name, st.student_number,
                    COUNT(sess.id)                                   AS total_sessions,
                    SUM(CASE WHEN a.id IS NOT NULL THEN 1 ELSE 0 END) AS attended,
                    ROUND(
                        SUM(CASE WHEN a.id IS NOT NULL THEN 1 ELSE 0 END)
                        / COUNT(sess.id) * 100, 1
                    ) AS attendance_pct
               FROM enrollments e
               JOIN students st  ON st.id  = e.student_id
               JOIN users u      ON u.id   = st.user_id
               JOIN sessions sess ON sess.course_id = e.course_id AND sess.status = 'closed'
               LEFT JOIN attendance a ON a.session_id = sess.id AND a.student_id = st.id
              WHERE e.course_id = ?
              GROUP BY st.id
              ORDER BY u.name",
            [$courseId]
        );
    }
}
