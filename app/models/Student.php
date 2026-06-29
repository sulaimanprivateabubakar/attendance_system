<?php
// app/models/Student.php

class Student
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findById(int $id): ?array
    {
        return $this->db->single(
            "SELECT s.*, u.name, u.email, u.is_active, d.name AS department_name
               FROM students s
               JOIN users u ON u.id = s.user_id
               LEFT JOIN departments d ON d.id = s.department_id
              WHERE s.id = ?",
            [$id]
        );
    }

    public function findByUserId(int $userId): ?array
    {
        return $this->db->single(
            "SELECT s.*, u.name, u.email, d.name AS department_name
               FROM students s
               JOIN users u ON u.id = s.user_id
               LEFT JOIN departments d ON d.id = s.department_id
              WHERE s.user_id = ?",
            [$userId]
        );
    }

    public function findByStudentNumber(string $number): ?array
    {
        return $this->db->single(
            "SELECT s.*, u.name, u.email FROM students s
               JOIN users u ON u.id = s.user_id
              WHERE s.student_number = ?",
            [$number]
        );
    }

    public function all(): array
    {
        return $this->db->all(
            "SELECT s.*, u.name, u.email, u.is_active, d.name AS department_name
               FROM students s
               JOIN users u ON u.id = s.user_id
               LEFT JOIN departments d ON d.id = s.department_id
              ORDER BY u.name"
        );
    }

    public function allNotEnrolledIn(int $courseId): array
    {
        return $this->db->all(
            "SELECT s.id, s.student_number, u.name
               FROM students s
               JOIN users u ON u.id = s.user_id
              WHERE s.id NOT IN (
                SELECT student_id FROM enrollments WHERE course_id = ?
              )
              ORDER BY u.name",
            [$courseId]
        );
    }

    public function create(int $userId, string $studentNumber, ?int $deptId, ?int $year, ?string $phone): int|string
    {
        return $this->db->insert(
            "INSERT INTO students (user_id, student_number, department_id, year_of_study, phone)
             VALUES (?, ?, ?, ?, ?)",
            [$userId, $studentNumber, $deptId, $year, $phone]
        );
    }

    public function getEnrolledCourses(int $studentId): array
    {
        return $this->db->all(
            "SELECT c.*, u.name AS lecturer_name,
                    COUNT(DISTINCT sess.id) AS total_sessions,
                    COUNT(DISTINCT a.id)    AS attended
               FROM enrollments e
               JOIN courses   c    ON c.id  = e.course_id
               JOIN lecturers l    ON l.id  = c.lecturer_id
               JOIN users     u    ON u.id  = l.user_id
               LEFT JOIN sessions   sess ON sess.course_id = c.id AND sess.status = 'closed'
               LEFT JOIN attendance a    ON a.session_id  = sess.id AND a.student_id = ?
              WHERE e.student_id = ? AND c.is_active = 1
              GROUP BY c.id",
            [$studentId, $studentId]
        );
    }

    public function count(): int
    {
        return (int)$this->db->scalar("SELECT COUNT(*) FROM students");
    }
}
