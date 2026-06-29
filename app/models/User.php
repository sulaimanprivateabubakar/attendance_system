<?php
// app/models/User.php

class User
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findById(int $id): ?array
    {
        return $this->db->single("SELECT * FROM users WHERE id = ?", [$id]);
    }

    public function findByEmail(string $email): ?array
    {
        return $this->db->single("SELECT * FROM users WHERE email = ?", [strtolower(trim($email))]);
    }

    public function all(): array
    {
        return $this->db->all("SELECT * FROM users ORDER BY created_at DESC");
    }

    public function create(string $name, string $email, string $password, string $role): int|string
    {
        return $this->db->insert(
            "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)",
            [$name, strtolower(trim($email)), password_hash($password, PASSWORD_BCRYPT), $role]
        );
    }

    public function update(int $id, array $data): int
    {
        $fields = implode(', ', array_map(fn($k) => "$k = ?", array_keys($data)));
        $values = array_values($data);
        $values[] = $id;
        return $this->db->execute("UPDATE users SET $fields WHERE id = ?", $values);
    }

    public function toggleActive(int $id): int
    {
        return $this->db->execute(
            "UPDATE users SET is_active = NOT is_active WHERE id = ? AND role != 'admin'",
            [$id]
        );
    }

    public function countByRole(string $role): int
    {
        return (int)$this->db->scalar("SELECT COUNT(*) FROM users WHERE role = ?", [$role]);
    }
}
