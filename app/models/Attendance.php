<?php
// app/models/Attendance.php

class Attendance
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findBySessionAndStudent(int $sessionId, int $studentId): ?array
    {
        return $this->db->single(
            "SELECT * FROM attendance WHERE session_id = ? AND student_id = ?",
            [$sessionId, $studentId]
        );
    }

    public function record(int $sessionId, int $studentId, string $status, string $ip, string $device): int|string
    {
        return $this->db->insert(
            "INSERT INTO attendance (session_id, student_id, status, ip_address, device_info)
             VALUES (?, ?, ?, ?, ?)",
            [$sessionId, $studentId, $status, $ip, substr($device, 0, 255)]
        );
    }

    public function getBySession(int $sessionId): array
    {
        return $this->db->all(
            "SELECT a.*, u.name, s.student_number,
                    DATE_FORMAT(a.scanned_at, '%H:%i') AS scanned_time
               FROM attendance a
               JOIN students s ON s.id = a.student_id
               JOIN users    u ON u.id = s.user_id
              WHERE a.session_id = ?
              ORDER BY a.scanned_at ASC",
            [$sessionId]
        );
    }

    public function getByStudent(int $studentId, ?int $courseId = null): array
    {
        $where  = 'WHERE a.student_id = ?';
        $params = [$studentId];

        if ($courseId) {
            $where  .= ' AND s.course_id = ?';
            $params[] = $courseId;
        }

        return $this->db->all(
            "SELECT a.*, sess.session_date, sess.start_time, sess.title,
                    c.name AS course_name, c.code AS course_code
               FROM attendance a
               JOIN sessions sess ON sess.id = a.session_id
               JOIN courses  c    ON c.id    = sess.course_id
              $where
              ORDER BY a.scanned_at DESC",
            $params
        );
    }

    public function getSummaryByCourse(int $courseId): array
    {
        return $this->db->all(
            "SELECT u.name, st.student_number,
                    COUNT(DISTINCT sess.id)                                       AS total_sessions,
                    SUM(CASE WHEN a.id IS NOT NULL THEN 1 ELSE 0 END)            AS attended,
                    ROUND(
                        SUM(CASE WHEN a.id IS NOT NULL THEN 1 ELSE 0 END)
                        / NULLIF(COUNT(DISTINCT sess.id), 0) * 100, 1
                    )                                                             AS pct
               FROM enrollments e
               JOIN students st   ON st.id = e.student_id
               JOIN users    u    ON u.id  = st.user_id
               JOIN sessions sess ON sess.course_id = e.course_id AND sess.status = 'closed'
               LEFT JOIN attendance a ON a.session_id = sess.id AND a.student_id = st.id
              WHERE e.course_id = ?
              GROUP BY st.id
              ORDER BY u.name",
            [$courseId]
        );
    }

    public function countToday(): int
    {
        return (int)$this->db->scalar(
            "SELECT COUNT(*) FROM attendance WHERE DATE(scanned_at) = CURDATE()"
        );
    }

    public function getTodayActivity(): array
    {
        return $this->db->all(
            "SELECT a.scanned_at, u.name AS student_name, s.student_number,
                    c.code AS course_code, c.name AS course_name
               FROM attendance a
               JOIN students st ON st.id = a.student_id
               JOIN users    u  ON u.id  = st.user_id
               JOIN sessions se ON se.id = a.session_id
               JOIN courses  c  ON c.id  = se.course_id
               JOIN students s  ON s.id  = a.student_id
              WHERE DATE(a.scanned_at) = CURDATE()
              ORDER BY a.scanned_at DESC
              LIMIT 20"
        );
    }
}
