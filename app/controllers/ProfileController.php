<?php
// app/controllers/ProfileController.php

class ProfileController extends Controller
{
    // GET /profile
    public function edit(): void
    {
        $userId     = Auth::id();
        $userRecord = $this->db->single("SELECT * FROM users WHERE id = ?", [$userId]);
        $profile    = $this->getProfile($userId);

        $this->view('profile/edit', [
            'user'       => Auth::user(),
            'userRecord' => $userRecord,
            'profile'    => $profile,
            'csrf'       => Auth::generateCsrfToken(),
            'flash'      => $this->getFlash(),
        ]);
    }

    // POST /profile/update
    public function update(): void
    {
        $this->validateCsrf();
        $action = $this->post('action');

        if ($action === 'password') {
            $this->updatePassword();
        } else {
            $this->updateInfo();
        }
    }

    private function updateInfo(): void
    {
        $userId = Auth::id();
        $name   = $this->clean($this->post('name', ''));
        $email  = strtolower(trim($this->post('email', '')));
        $phone  = $this->clean($this->post('phone', ''));

        if (!$name || !$email) {
            $this->flash('error', 'Name and email are required.');
            $this->redirect('/profile');
        }

        $existing = $this->db->single(
            "SELECT id FROM users WHERE email = ? AND id != ?", [$email, $userId]
        );
        if ($existing) {
            $this->flash('error', 'That email is already used by another account.');
            $this->redirect('/profile');
        }

        $this->db->execute(
            "UPDATE users SET name = ?, email = ? WHERE id = ?",
            [$name, $email, $userId]
        );

        if (Auth::isStudent()) {
            $year = (int)$this->post('year_of_study', 1);
            $this->db->execute(
                "UPDATE students SET phone = ?, year_of_study = ? WHERE user_id = ?",
                [$phone, $year, $userId]
            );
        } elseif (Auth::isLecturer()) {
            $this->db->execute(
                "UPDATE lecturers SET phone = ? WHERE user_id = ?",
                [$phone, $userId]
            );
        }

        $_SESSION['auth_user']['name']  = $name;
        $_SESSION['auth_user']['email'] = $email;

        $this->flash('success', 'Profile updated successfully.');
        $this->redirect('/profile');
    }

    private function updatePassword(): void
    {
        $userId  = Auth::id();
        $current = $this->post('current_password', '');
        $new     = $this->post('new_password', '');
        $confirm = $this->post('confirm_password', '');

        if (strlen($new) < 8) {
            $this->flash('error', 'New password must be at least 8 characters.');
            $this->redirect('/profile');
        }

        if ($new !== $confirm) {
            $this->flash('error', 'New passwords do not match.');
            $this->redirect('/profile');
        }

        $user = $this->db->single("SELECT password FROM users WHERE id = ?", [$userId]);

        if (!password_verify($current, $user['password'])) {
            $this->flash('error', 'Current password is incorrect.');
            $this->redirect('/profile');
        }

        $this->db->execute(
            "UPDATE users SET password = ? WHERE id = ?",
            [password_hash($new, PASSWORD_BCRYPT), $userId]
        );

        $this->flash('success', 'Password changed successfully.');
        $this->redirect('/profile');
    }

    private function getProfile(int $userId): array
    {
        if (Auth::isStudent()) {
            return $this->db->single(
                "SELECT s.*, d.name AS department_name
                   FROM students s
                   LEFT JOIN departments d ON d.id = s.department_id
                  WHERE s.user_id = ?",
                [$userId]
            ) ?? [];
        }

        if (Auth::isLecturer()) {
            return $this->db->single(
                "SELECT l.*, d.name AS department_name
                   FROM lecturers l
                   LEFT JOIN departments d ON d.id = l.department_id
                  WHERE l.user_id = ?",
                [$userId]
            ) ?? [];
        }

        return [];
    }
}