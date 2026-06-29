<?php
// app/controllers/AdminController.php

class AdminController extends Controller
{
    // GET /admin/dashboard
    public function dashboard(): void
    {
        $stats = [
            'students'  => $this->db->scalar("SELECT COUNT(*) FROM students"),
            'lecturers' => $this->db->scalar("SELECT COUNT(*) FROM lecturers"),
            'courses'   => $this->db->scalar("SELECT COUNT(*) FROM courses WHERE is_active=1"),
            'sessions'  => $this->db->scalar("SELECT COUNT(*) FROM sessions"),
            'today'     => $this->db->scalar(
                "SELECT COUNT(*) FROM attendance WHERE DATE(scanned_at) = CURDATE()"
            ),
        ];

        $recentSessions = $this->db->all(
            "SELECT s.*, c.name AS course_name, c.code AS course_code,
                    u.name AS lecturer_name, COUNT(a.id) AS att_count
               FROM sessions s
               JOIN courses   c ON c.id = s.course_id
               JOIN lecturers l ON l.id = s.lecturer_id
               JOIN users     u ON u.id = l.user_id
               LEFT JOIN attendance a ON a.session_id = s.id
              GROUP BY s.id
              ORDER BY s.created_at DESC LIMIT 10"
        );

        $this->view('admin/dashboard', [
            'user'           => Auth::user(),
            'stats'          => $stats,
            'recentSessions' => $recentSessions,
            'flash'          => $this->getFlash(),
        ]);
    }

    // GET /admin/users
    public function users(): void
    {
        $users = $this->db->all(
            "SELECT u.*,
                    COALESCE(s.student_number, l.staff_number) AS identifier,
                    d.name AS department
               FROM users u
               LEFT JOIN students    s ON s.user_id = u.id
               LEFT JOIN lecturers   l ON l.user_id = u.id
               LEFT JOIN departments d ON d.id = COALESCE(s.department_id, l.department_id)
              ORDER BY u.created_at DESC"
        );

        $this->view('admin/users', [
            'user'  => Auth::user(),
            'users' => $users,
            'flash' => $this->getFlash(),
        ]);
    }

    // POST /admin/users/:id/toggle
    public function toggleUser(array $params): void
    {
        $this->validateCsrf();
        $this->db->execute(
            "UPDATE users SET is_active = NOT is_active WHERE id = ? AND role != 'admin'",
            [(int)$params['id']]
        );
        $this->flash('success', 'User status updated.');
        $this->redirect('/admin/users');
    }

    // GET /admin/users/create
    public function createUserForm(): void
    {
        $departments = $this->db->all("SELECT id, name FROM departments ORDER BY name");
        $this->view('admin/create_user', [
            'user'        => Auth::user(),
            'departments' => $departments,
            'csrf'        => Auth::generateCsrfToken(),
        ]);
    }

    // POST /admin/users/create
    public function createUser(): void
    {
        $this->validateCsrf();

        $role   = $this->post('role');
        $name   = $this->clean($this->post('name', ''));
        $email  = strtolower(trim($this->post('email', '')));
        $pass   = $this->post('password', '');
        $deptId = $this->post('department_id') ?: null;

        if (!in_array($role, ['lecturer','student','admin'])) {
            $this->flash('error', 'Invalid role.');
            $this->redirect('/admin/users/create');
        }

        $exists = $this->db->single("SELECT id FROM users WHERE email = ?", [$email]);
        if ($exists) {
            $this->flash('error', 'Email already exists.');
            $this->redirect('/admin/users/create');
        }

        $hash = password_hash($pass, PASSWORD_BCRYPT);

        try {
            $this->db->transaction(function (Database $db) use ($name, $email, $hash, $role, $deptId) {
                $userId = $db->insert(
                    "INSERT INTO users (name, email, password, role) VALUES (?,?,?,?)",
                    [$name, $email, $hash, $role]
                );

                if ($role === 'lecturer') {
                    $staffNo = 'STF-' . strtoupper(substr(md5($email), 0, 6));
                    $db->insert(
                        "INSERT INTO lecturers (user_id, department_id, staff_number) VALUES (?,?,?)",
                        [$userId, $deptId, $staffNo]
                    );
                } elseif ($role === 'student') {
                    $stuNo = 'STU-' . strtoupper(substr(md5($email), 0, 8));
                    $db->insert(
                        "INSERT INTO students (user_id, department_id, student_number) VALUES (?,?,?)",
                        [$userId, $deptId, $stuNo]
                    );
                }
            });

            $this->flash('success', 'User created successfully.');
        } catch (Throwable $e) {
            error_log($e->getMessage());
            $this->flash('error', 'Failed to create user.');
        }

        $this->redirect('/admin/users');
    }

    // GET /admin/courses
    public function courses(): void
    {
        $courses = $this->db->all(
            "SELECT c.*, d.name AS dept_name, u.name AS lecturer_name,
                    COUNT(DISTINCT e.id) AS enrolled
               FROM courses c
               LEFT JOIN departments d ON d.id = c.department_id
               LEFT JOIN lecturers   l ON l.id = c.lecturer_id
               LEFT JOIN users       u ON u.id = l.user_id
               LEFT JOIN enrollments e ON e.course_id = c.id
              GROUP BY c.id ORDER BY c.created_at DESC"
        );

        $this->view('admin/courses', [
            'user'    => Auth::user(),
            'courses' => $courses,
            'flash'   => $this->getFlash(),
        ]);
    }

    // GET /admin/courses/create
    public function createCourseForm(): void
    {
        $departments = $this->db->all("SELECT id, name FROM departments ORDER BY name");
        $lecturers   = $this->db->all(
            "SELECT l.id, u.name, l.staff_number FROM lecturers l
               JOIN users u ON u.id = l.user_id ORDER BY u.name"
        );

        $this->view('admin/create_course', [
            'user'        => Auth::user(),
            'departments' => $departments,
            'lecturers'   => $lecturers,
            'csrf'        => Auth::generateCsrfToken(),
        ]);
    }

    // POST /admin/courses/create
    public function createCourse(): void
    {
        $this->validateCsrf();

        $this->db->insert(
            "INSERT INTO courses (code, name, department_id, lecturer_id, credit_hours, semester, academic_year)
             VALUES (?,?,?,?,?,?,?)",
            [
                strtoupper(trim($this->post('code',''))),
                $this->clean($this->post('name','')),
                $this->post('department_id') ?: null,
                $this->post('lecturer_id')   ?: null,
                (int)$this->post('credit_hours', 3),
                (int)$this->post('semester', 1),
                $this->clean($this->post('academic_year','')),
            ]
        );

        $this->flash('success', 'Course created.');
        $this->redirect('/admin/courses');
    }

    // POST /admin/courses/:id/toggle
    public function toggleCourse(array $params): void
    {
        $this->validateCsrf();
        $this->db->execute(
            "UPDATE courses SET is_active = NOT is_active WHERE id = ?",
            [(int)$params['id']]
        );
        $this->flash('success', 'Course status updated.');
        $this->redirect('/admin/courses');
    }

    // GET /admin/departments
    public function departments(): void
    {
        $departments = $this->db->all(
            "SELECT d.*,
                    COUNT(DISTINCT s.id) AS student_count,
                    COUNT(DISTINCT l.id) AS lecturer_count,
                    COUNT(DISTINCT c.id) AS course_count
               FROM departments d
               LEFT JOIN students  s ON s.department_id = d.id
               LEFT JOIN lecturers l ON l.department_id = d.id
               LEFT JOIN courses   c ON c.department_id = d.id AND c.is_active = 1
              GROUP BY d.id ORDER BY d.name"
        );

        $this->view('admin/departments', [
            'user'        => Auth::user(),
            'departments' => $departments,
            'csrf'        => Auth::generateCsrfToken(),
            'flash'       => $this->getFlash(),
        ]);
    }

    // POST /admin/departments/create
    public function createDepartment(): void
    {
        $this->validateCsrf();
        $name = $this->clean($this->post('name',''));
        $code = strtoupper(trim($this->post('code','')));

        if (!$name || !$code) {
            $this->flash('error', 'Name and code are required.');
            $this->redirect('/admin/departments');
        }

        $this->db->insert(
            "INSERT INTO departments (name, code) VALUES (?,?)",
            [$name, $code]
        );
        $this->flash('success', 'Department created.');
        $this->redirect('/admin/departments');
    }

    // GET /admin/reports
    public function reports(): void
    {
        $courses = $this->db->all(
            "SELECT id, code, name FROM courses WHERE is_active = 1 ORDER BY name"
        );

        $this->view('admin/reports', [
            'user'    => Auth::user(),
            'courses' => $courses,
            'flash'   => $this->getFlash(),
        ]);
    }

    // GET /admin/reports/export
    public function exportReport(): void
    {
        $courseId = (int)$this->get('course_id');
        $format   = $this->get('format', 'csv');

        if (!$courseId) {
            $this->flash('error', 'Please select a course.');
            $this->redirect('/admin/reports');
        }

        require_once APP_PATH . '/services/ReportService.php';
        $reportService = new ReportService();

        if ($format === 'csv') {
            $reportService->exportCsv($courseId);
        } else {
            $reportService->exportPdf($courseId);
        }
    }
}