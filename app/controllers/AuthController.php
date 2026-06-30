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
