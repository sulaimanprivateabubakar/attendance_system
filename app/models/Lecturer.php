<?php
// app/models/Lecturer.php

class Lecturer
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findById(int $id): ?array
    {
        return $this->db->single(
            "SELECT l.*, u.name, u.email, u.is_active, d.name AS department_name
               FROM lecturers l
               JOIN users u ON u.id = l.user_id
               LEFT JOIN departments d ON d.id = l.department_id
              WHERE l.id = ?",
            [$id]
        );
    }

    public function findByUserId(int $userId): ?array
    {
        return $this->db->single(
            "SELECT l.*, u.name, u.email, d.name AS department_name
               FROM lecturers l
               JOIN users u ON u.id = l.user_id
               LEFT JOIN departments d ON d.id = l.department_id
              WHERE l.user_id = ?",
            [$userId]
        );
    }

    public function all(): array
    {
        return $this->db->all(
            "SELECT l.*, u.name, u.email, u.is_active, d.name AS department_name
               FROM lecturers l
               JOIN users u ON u.id = l.user_id
               LEFT JOIN departments d ON d.id = l.department_id
              ORDER BY u.name"
        );
    }

    public function getCourses(int $lecturerId): array
    {
        return $this->db->all(
            "SELECT c.*, COUNT(DISTINCT e.id) AS enrolled_count
               FROM courses c
               LEFT JOIN enrollments e ON e.course_id = c.id
              WHERE c.lecturer_id = ? AND c.is_active = 1
              GROUP BY c.id
              ORDER BY c.name",
            [$lecturerId]
        );
    }

    public function create(int $userId, string $staffNumber, ?int $deptId, ?string $phone): int|string
    {
        return $this->db->insert(
            "INSERT INTO lecturers (user_id, staff_number, department_id, phone) VALUES (?, ?, ?, ?)",
            [$userId, $staffNumber, $deptId, $phone]
        );
    }

    public function count(): int
    {
        return (int)$this->db->scalar("SELECT COUNT(*) FROM lecturers");
    }
}
