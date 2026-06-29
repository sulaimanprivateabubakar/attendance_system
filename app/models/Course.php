<?php
// app/models/Course.php

class Course
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findById(int $id): ?array
    {
        return $this->db->single(
            "SELECT c.*, d.name AS dept_name, u.name AS lecturer_name
               FROM courses c
               LEFT JOIN departments d ON d.id = c.department_id
               LEFT JOIN lecturers   l ON l.id = c.lecturer_id
               LEFT JOIN users       u ON u.id = l.user_id
              WHERE c.id = ?",
            [$id]
        );
    }

    public function findByCode(string $code): ?array
    {
        return $this->db->single("SELECT * FROM courses WHERE code = ?", [strtoupper($code)]);
    }

    public function all(bool $activeOnly = false): array
    {
        $where = $activeOnly ? 'WHERE c.is_active = 1' : '';
        return $this->db->all(
            "SELECT c.*, d.name AS dept_name, u.name AS lecturer_name,
                    COUNT(DISTINCT e.id) AS enrolled_count
               FROM courses c
               LEFT JOIN departments d ON d.id = c.department_id
               LEFT JOIN lecturers   l ON l.id = c.lecturer_id
               LEFT JOIN users       u ON u.id = l.user_id
               LEFT JOIN enrollments e ON e.course_id = c.id
              $where
              GROUP BY c.id
              ORDER BY c.name"
        );
    }

    public function create(array $data): int|string
    {
        return $this->db->insert(
            "INSERT INTO courses (code, name, department_id, lecturer_id, credit_hours, semester, academic_year)
             VALUES (?, ?, ?, ?, ?, ?, ?)",
            [
                strtoupper(trim($data['code'])),
                trim($data['name']),
                $data['department_id'] ?? null,
                $data['lecturer_id']   ?? null,
                $data['credit_hours']  ?? 3,
                $data['semester']      ?? 1,
                $data['academic_year'] ?? null,
            ]
        );
    }

    public function toggleActive(int $id): int
    {
        return $this->db->execute(
            "UPDATE courses SET is_active = NOT is_active WHERE id = ?", [$id]
        );
    }

    public function enrollStudent(int $courseId, int $studentId): bool
    {
        $exists = $this->db->single(
            "SELECT id FROM enrollments WHERE course_id = ? AND student_id = ?",
            [$courseId, $studentId]
        );
        if ($exists) return false;

        $this->db->insert(
            "INSERT INTO enrollments (course_id, student_id) VALUES (?, ?)",
            [$courseId, $studentId]
        );
        return true;
    }

    public function getEnrolledStudents(int $courseId): array
    {
        return $this->db->all(
            "SELECT s.id, s.student_number, u.name, u.email
               FROM enrollments e
               JOIN students s ON s.id = e.student_id
               JOIN users    u ON u.id = s.user_id
              WHERE e.course_id = ?
              ORDER BY u.name",
            [$courseId]
        );
    }

    public function count(bool $activeOnly = true): int
    {
        $where = $activeOnly ? 'WHERE is_active = 1' : '';
        return (int)$this->db->scalar("SELECT COUNT(*) FROM courses $where");
    }
}
