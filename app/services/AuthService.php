<?php
// app/services/AuthService.php

class AuthService
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // ── Login ────────────────────────────────────────────────────────────────

    /**
     * Attempt login. Returns the user array on success, null on failure.
     */
    public function attemptLogin(string $email, string $password): ?array
    {
        $user = $this->db->single(
            "SELECT id, name, email, password, role, is_active
               FROM users
              WHERE email = ?
              LIMIT 1",
            [strtolower(trim($email))]
        );

        if (!$user) {
            return null;
        }

        if (!$user['is_active']) {
            return null; // account disabled
        }

        if (!password_verify($password, $user['password'])) {
            return null;
        }

        return $user;
    }

    // ── Register ─────────────────────────────────────────────────────────────

    /**
     * Register a new student account.
     * Returns ['success'=>true, 'user_id'=>int] or ['success'=>false, 'error'=>string].
     */
    public function registerStudent(array $data): array
    {
        // Validate required fields
        foreach (['name','email','password','student_number','year_of_study'] as $field) {
            if (empty($data[$field])) {
                return ['success' => false, 'error' => "Field '{$field}' is required."];
            }
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'error' => 'Invalid email address.'];
        }

        if (strlen($data['password']) < 8) {
            return ['success' => false, 'error' => 'Password must be at least 8 characters.'];
        }

        // Check email uniqueness
        $existing = $this->db->single(
            "SELECT id FROM users WHERE email = ?",
            [strtolower(trim($data['email']))]
        );
        if ($existing) {
            return ['success' => false, 'error' => 'Email already registered.'];
        }

        // Check student number uniqueness
        $existing = $this->db->single(
            "SELECT id FROM students WHERE student_number = ?",
            [trim($data['student_number'])]
        );
        if ($existing) {
            return ['success' => false, 'error' => 'Student number already registered.'];
        }

        try {
            $userId = $this->db->transaction(function (Database $db) use ($data) {
                // Insert into users
                $userId = $db->insert(
                    "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'student')",
                    [
                        trim($data['name']),
                        strtolower(trim($data['email'])),
                        password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]),
                    ]
                );

                // Insert into students
                $db->insert(
                    "INSERT INTO students (user_id, student_number, year_of_study, department_id, phone)
                     VALUES (?, ?, ?, ?, ?)",
                    [
                        $userId,
                        trim($data['student_number']),
                        (int)$data['year_of_study'],
                        $data['department_id'] ?? null,
                        $data['phone'] ?? null,
                    ]
                );

                return $userId;
            });

            return ['success' => true, 'user_id' => $userId];

        } catch (Throwable $e) {
            error_log('Registration failed: ' . $e->getMessage());
            return ['success' => false, 'error' => 'Registration failed. Please try again.'];
        }
    }

    // ── Password reset ───────────────────────────────────────────────────────

    public function generatePasswordResetToken(string $email): ?string
    {
        $user = $this->db->single(
            "SELECT id FROM users WHERE email = ? AND is_active = 1",
            [strtolower(trim($email))]
        );

        if (!$user) {
            return null;
        }

        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', time() + 3600); // 1 hour

        // Invalidate old tokens
        $this->db->execute(
            "UPDATE password_resets SET used = 1 WHERE user_id = ?",
            [$user['id']]
        );

        $this->db->insert(
            "INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)",
            [$user['id'], $token, $expires]
        );

        return $token;
    }

    public function resetPassword(string $token, string $newPassword): bool
    {
        $reset = $this->db->single(
            "SELECT user_id FROM password_resets
              WHERE token = ?
                AND used = 0
                AND expires_at > NOW()
              LIMIT 1",
            [$token]
        );

        if (!$reset) {
            return false;
        }

        $hash = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);

        $this->db->transaction(function (Database $db) use ($reset, $hash, $token) {
            $db->execute(
                "UPDATE users SET password = ? WHERE id = ?",
                [$hash, $reset['user_id']]
            );
            $db->execute(
                "UPDATE password_resets SET used = 1 WHERE token = ?",
                [$token]
            );
        });

        return true;
    }
}
