<?php
// app/controllers/AuthController.php

require_once APP_PATH . '/services/AuthService.php';

class AuthController extends Controller
{
    private AuthService $authService;

    public function __construct()
    {
        parent::__construct();
        $this->authService = new AuthService();
    }

    // GET /login
    public function showLogin(): void
    {
        if (Auth::check()) {
            $this->redirectByRole();
        }
        $this->view('auth/login', ['csrf' => Auth::generateCsrfToken()]);
    }

    // POST /login
    public function login(): void
    {
        $this->validateCsrf();

        $email    = $this->post('email', '');
        $password = $this->post('password', '');

        $user = $this->authService->attemptLogin($email, $password);

        if (!$user) {
            $this->flash('error', 'Invalid email or password.');
            $this->redirect('/login');
        }

        Auth::login($user);

        // Honour intended URL if set (strip BASE_URL prefix since redirect() re-adds it)
        $intended = $_SESSION['intended_url'] ?? null;
        unset($_SESSION['intended_url']);

        if ($intended) {
            if (BASE_URL !== '' && str_starts_with($intended, BASE_URL)) {
                $intended = substr($intended, strlen(BASE_URL));
            }
            $this->redirect($intended);
        }

        $this->redirectByRole();
    }

    // GET /register
    public function showRegister(): void
    {
        if (Auth::check()) {
            $this->redirectByRole();
        }

        $departments = $this->db->all("SELECT id, name FROM departments ORDER BY name");
        $this->view('auth/register', [
            'csrf'        => Auth::generateCsrfToken(),
            'departments' => $departments,
        ]);
    }

    // POST /register
    public function register(): void
    {
        $this->validateCsrf();

        $result = $this->authService->registerStudent([
            'name'           => $this->post('name'),
            'email'          => $this->post('email'),
            'password'       => $this->post('password'),
            'student_number' => $this->post('student_number'),
            'year_of_study'  => $this->post('year_of_study'),
            'department_id'  => $this->post('department_id'),
            'phone'          => $this->post('phone'),
        ]);

        if (!$result['success']) {
            $this->flash('error', $result['error']);
            $this->redirect('/register');
        }

        $this->flash('success', 'Registration successful! Please log in.');
        $this->redirect('/login');
    }

 // GET /logout
    public function logout(): void
    {
        Auth::logout();
        $this->redirect('/login');
    }

    // GET /forgot-password
    public function showForgotPassword(): void
    {
        $this->view('auth/forgot_password', [
            'csrf' => Auth::generateCsrfToken(),
        ]);
    }

    // POST /forgot-password
    public function forgotPassword(): void
    {
        $this->validateCsrf();
        $email = strtolower(trim($this->post('email', '')));

        $user = $this->db->single(
            "SELECT id FROM users WHERE email = ? AND is_active = 1", [$email]
        );

        if ($user) {
            $token   = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', time() + 3600);

            $this->db->execute(
                "UPDATE password_resets SET used = 1 WHERE user_id = ?",
                [$user['id']]
            );
            $this->db->insert(
                "INSERT INTO password_resets (user_id, token, expires_at) VALUES (?,?,?)",
                [$user['id'], $token, $expires]
            );

            $resetUrl = BASE_URL . '/reset-password?token=' . $token;
            $this->flash('success', 'Reset link generated. Copy this link: ' . $resetUrl);
        } else {
            $this->flash('success', 'If that email exists, a reset link has been generated.');
        }

        $this->redirect('/forgot-password');
    }

    // GET /reset-password?token=
    public function showResetPassword(): void
    {
        $token = $this->get('token', '');

        $valid = $this->db->single(
            "SELECT id FROM password_resets
              WHERE token = ? AND used = 0 AND expires_at > NOW()",
            [$token]
        );

        if (!$valid) {
            $this->flash('error', 'This reset link is invalid or has expired.');
            $this->redirect('/forgot-password');
        }

        $this->view('auth/reset_password', [
            'csrf'  => Auth::generateCsrfToken(),
            'token' => $token,
        ]);
    }

    // POST /reset-password
    public function resetPassword(): void
    {
        $this->validateCsrf();

        $token    = $this->post('token', '');
        $password = $this->post('password', '');
        $confirm  = $this->post('password_confirm', '');

        if (strlen($password) < 8) {
            $this->flash('error', 'Password must be at least 8 characters.');
            $this->redirect('/reset-password?token=' . $token);
        }

        if ($password !== $confirm) {
            $this->flash('error', 'Passwords do not match.');
            $this->redirect('/reset-password?token=' . $token);
        }

        $reset = $this->db->single(
            "SELECT user_id FROM password_resets
              WHERE token = ? AND used = 0 AND expires_at > NOW()",
            [$token]
        );

        if (!$reset) {
            $this->flash('error', 'Reset link is invalid or has expired.');
            $this->redirect('/forgot-password');
        }

        $this->db->transaction(function($db) use ($reset, $password, $token) {
            $db->execute(
                "UPDATE users SET password = ? WHERE id = ?",
                [password_hash($password, PASSWORD_BCRYPT), $reset['user_id']]
            );
            $db->execute(
                "UPDATE password_resets SET used = 1 WHERE token = ?",
                [$token]
            );
        });

        $this->flash('success', 'Password reset successfully. Please log in.');
        $this->redirect('/login');
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function redirectByRole(): void
    {
        match (Auth::role()) {
            'admin'    => $this->redirect('/admin/dashboard'),
            'lecturer' => $this->redirect('/lecturer/dashboard'),
            'student'  => $this->redirect('/student/dashboard'),
            default    => $this->redirect('/login'),
        };
    }
}
