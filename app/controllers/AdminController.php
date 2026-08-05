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
          ORDER BY u.role, u.name ASC"
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
    $u = $this->db->single("SELECT name, email, is_active FROM users WHERE id = ?", [(int)$params['id']]);
    $this->db->execute(
        "UPDATE users SET is_active = NOT is_active WHERE id = ? AND role != 'admin'",
        [(int)$params['id']]
    );
    $newState = $u['is_active'] ? 'disabled' : 'enabled';
    $this->audit('user.toggled', 'users',
        "User account $newState: {$u['name']} ({$u['email']})",
        ['is_active' => $u['is_active']],
        ['is_active' => !$u['is_active']]
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

        if (!in_array($role, ['lecturer','student','admin','hod','hoa','registrar','vc','accounts'])) {
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
    $staffNo = trim($this->post('staff_number', ''));
    if (empty($staffNo)) {
        $this->flash('error', 'Staff number is required.');
        $this->redirect('/admin/users/create');
    }
    $exists = $db->single("SELECT id FROM lecturers WHERE staff_number = ?", [$staffNo]);
    if ($exists) {
        $this->flash('error', 'That staff number is already registered.');
        $this->redirect('/admin/users/create');
    }
    $db->insert(
        "INSERT INTO lecturers (user_id, department_id, staff_number) VALUES (?,?,?)",
        [$userId, $deptId, $staffNo]
    );
                } elseif ($role === 'student') {
    $stuNo = trim($this->post('student_number', ''));
    if (empty($stuNo)) {
        $this->flash('error', 'Student registration number is required.');
        $this->redirect('/admin/users/create');
    }
    // Check uniqueness
    $exists = $db->single("SELECT id FROM students WHERE student_number = ?", [$stuNo]);
    if ($exists) {
        $this->flash('error', 'That registration number is already registered.');
        $this->redirect('/admin/users/create');
    }
    $db->insert(
        "INSERT INTO students (user_id, department_id, student_number) VALUES (?,?,?)",
        [$userId, $deptId, $stuNo]
    );
}
            });

            $this->flash('success', 'User created successfully.');

            $this->flash('success', 'User created successfully.');
            $this->audit('user.created', 'users',
            "Created $role account: $name ($email)");

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
    "INSERT INTO courses (code, name, department_id, lecturer_id, credit_hours, semester, year_of_study, academic_year)
     VALUES (?,?,?,?,?,?,?,?)",
    [
        strtoupper(trim($this->post('code',''))),
        $this->clean($this->post('name','')),
        $this->post('department_id') ?: null,
        $this->post('lecturer_id')   ?: null,
        (int)$this->post('credit_hours', 3),
        (int)$this->post('semester', 1),
        $this->post('year_of_study') ?: null,
        $this->clean($this->post('academic_year','')),
        
    ]
);

        $this->flash('success', 'Course created.');
        $this->redirect('/admin/courses');
        $this->audit('course.created', 'courses',
    'Created course: ' . strtoupper(trim($this->post('code',''))) . ' — ' . $this->post('name',''));
    }

    // POST /admin/courses/:id/toggle
public function toggleCourse(array $params): void
{
    $this->validateCsrf();
    $c = $this->db->single("SELECT code, name, is_active FROM courses WHERE id = ?", [(int)$params['id']]);
    $this->db->execute(
        "UPDATE courses SET is_active = NOT is_active WHERE id = ?", [(int)$params['id']]
    );
    $state = $c['is_active'] ? 'deactivated' : 'activated';
    $this->audit('course.toggled', 'courses',
        "Course $state: {$c['code']} — {$c['name']}");
    $this->flash('success', 'Course status updated.');
    $this->redirect('/admin/courses');
}

    // GET /admin/courses/:id/enrollment
    public function enrollmentView(array $params): void
    {
        $courseId = (int)$params['id'];

        $course = $this->db->single(
            "SELECT c.*, u.name AS lecturer_name
               FROM courses c
               LEFT JOIN lecturers l ON l.id = c.lecturer_id
               LEFT JOIN users     u ON u.id = l.user_id
              WHERE c.id = ?",
            [$courseId]
        );

        if (!$course) {
            $this->flash('error', 'Course not found.');
            $this->redirect('/admin/courses');
        }

        $enrolled = $this->db->all(
    "SELECT s.id, s.student_number, u.name, u.email,
            e.enrolled_at,
            COALESCE(e.is_class_rep, 0) AS is_class_rep
       FROM enrollments e
       JOIN students s ON s.id = e.student_id
       JOIN users    u ON u.id = s.user_id
      WHERE e.course_id = ?
      ORDER BY e.is_class_rep DESC, u.name ASC",
    [$courseId]
);

        $this->view('admin/enrollment', [
            'user'     => Auth::user(),
            'course'   => $course,
            'enrolled' => $enrolled,
            'csrf'     => Auth::generateCsrfToken(),
        ]);

        $enrolled = $this->db->all(
    "SELECT s.id, s.student_number, u.name, u.email, e.enrolled_at, e.is_class_rep
       FROM enrollments e
       JOIN students s ON s.id = e.student_id
       JOIN users    u ON u.id = s.user_id
      WHERE e.course_id = ?
      ORDER BY e.is_class_rep DESC, u.name ASC",
    [$courseId]
);
    }

    // POST /admin/courses/:id/enroll  (enroll a student)
    public function enrollStudent(array $params): void
    {
        $this->validateCsrf();
        $courseId  = (int)$params['id'];
        $studentId = (int)$this->post('student_id');

        $exists = $this->db->single(
            "SELECT id FROM enrollments WHERE student_id=? AND course_id=?",
            [$studentId, $courseId]
        );
        if (!$exists) {
            $this->db->insert(
                "INSERT INTO enrollments (student_id, course_id) VALUES (?,?)",
                [$studentId, $courseId]
            );
            $this->flash('success', 'Student enrolled.');
        } else {
            $this->flash('error', 'Student already enrolled.');
        }
        $this->redirect('/admin/courses/' . $courseId . '/enrollment');
        $this->audit('enrollment.created', 'enrollments',
        "Student enrolled in course_id={$courseId} student_id={$studentId}");
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
        $this->audit('department.created', 'departments',
        "Created department: $name ($code)");
        $this->redirect('/admin/departments');
    }

    // GET /admin/reports
    public function reports(): void
{
    // Filters
    $dateFrom   = $this->get('date_from', date('Y-m-01'));
    $dateTo     = $this->get('date_to',   date('Y-m-d'));
    $courseId   = (int)$this->get('course_id', 0);
    $deptId     = (int)$this->get('dept_id', 0);
    $semester   = (int)$this->get('semester', 0);
    $yearStudy  = (int)$this->get('year_of_study', 0);
    $groupBy    = $this->get('group_by', 'course');

    // Build WHERE conditions
    $conditions = ["s.session_date BETWEEN ? AND ?"];
    $params     = [$dateFrom, $dateTo];

    if ($courseId) {
        $conditions[] = "s.course_id = ?";
        $params[]     = $courseId;
    }

    if ($deptId) {
        $conditions[] = "c.department_id = ?";
        $params[]     = $deptId;
    }

    if ($semester) {
        $conditions[] = "c.semester = ?";
        $params[]     = $semester;
    }

    if ($yearStudy) {
        $conditions[] = "c.year_of_study = ?";
        $params[]     = $yearStudy;
    }

    $where = implode(' AND ', $conditions);

    // Overall stats
   // Overall stats
$stats = $this->db->single(
    "SELECT
        COUNT(DISTINCT s.id)      AS total_sessions,
        COUNT(DISTINCT a.id)      AS total_scans,
        SUM(a.status = 'present') AS present_count,
        SUM(a.status = 'late')    AS late_count
       FROM sessions s
       JOIN courses c ON c.id = s.course_id
       LEFT JOIN attendance a ON a.session_id = s.id
      WHERE s.status = 'closed' AND $where",
    $params
);

// Avg rate separately — simpler query
$avgRate = $this->db->scalar(
    "SELECT ROUND(
        COUNT(DISTINCT a.id) /
        NULLIF(
            (SELECT COUNT(DISTINCT s2.id) * COUNT(DISTINCT e2.student_id)
               FROM sessions s2
               JOIN courses c2 ON c2.id = s2.course_id
               LEFT JOIN enrollments e2 ON e2.course_id = s2.course_id
              WHERE s2.status = 'closed'
                AND s2.session_date BETWEEN ? AND ?
            ), 0
        ) * 100
    , 1)
       FROM sessions s
       JOIN courses c ON c.id = s.course_id
       LEFT JOIN attendance a ON a.session_id = s.id
      WHERE s.status = 'closed' AND $where",
    array_merge([$dateFrom, $dateTo], $params)
);

    // Course stats for charts and table
    // Course stats
$courseWhere = "s.status = 'closed' AND s.session_date BETWEEN ? AND ?";
$courseParams = [$dateFrom, $dateTo];

if ($courseId) { $courseWhere .= " AND c.id = ?";              $courseParams[] = $courseId; }
if ($deptId)   { $courseWhere .= " AND c.department_id = ?";   $courseParams[] = $deptId; }
if ($semester) { $courseWhere .= " AND c.semester = ?";        $courseParams[] = $semester; }
if ($yearStudy){ $courseWhere .= " AND c.year_of_study = ?";   $courseParams[] = $yearStudy; }

$courseStats = $this->db->all(
    "SELECT c.id, c.code, c.name AS course_name, c.semester,
            c.year_of_study, d.name AS dept_name,
            u.name AS lecturer_name,
            COUNT(DISTINCT s.id)         AS session_count,
            COUNT(DISTINCT e.student_id) AS enrolled_count,
            COUNT(DISTINCT a.id)         AS total_attended,
            ROUND(
                COUNT(DISTINCT a.id) /
                NULLIF(COUNT(DISTINCT s.id) * COUNT(DISTINCT e.student_id), 0)
                * 100
            , 1) AS avg_rate
       FROM courses c
       JOIN sessions    s ON s.course_id = c.id
       LEFT JOIN attendance  a ON a.session_id = s.id
       LEFT JOIN enrollments e ON e.course_id  = c.id
       LEFT JOIN departments d ON d.id = c.department_id
       LEFT JOIN lecturers   l ON l.id = c.lecturer_id
       LEFT JOIN users       u ON u.id = l.user_id
      WHERE $courseWhere
      GROUP BY c.id
      ORDER BY avg_rate DESC",
    $courseParams
);

// Department stats
$deptWhere  = "s.session_date BETWEEN ? AND ? AND s.status = 'closed'";
$deptParams = [$dateFrom, $dateTo];
if ($deptId) { $deptWhere .= " AND d.id = ?"; $deptParams[] = $deptId; }

$deptStats = $this->db->all(
    "SELECT d.id, d.name AS dept_name, d.code AS dept_code,
            COUNT(DISTINCT c.id)         AS course_count,
            COUNT(DISTINCT s.id)         AS session_count,
            COUNT(DISTINCT e.student_id) AS student_count,
            COUNT(DISTINCT a.id)         AS total_attended,
            ROUND(
                COUNT(DISTINCT a.id) /
                NULLIF(COUNT(DISTINCT s.id) * COUNT(DISTINCT e.student_id), 0)
                * 100
            , 1) AS avg_rate
       FROM departments d
       LEFT JOIN courses     c ON c.department_id = d.id
       LEFT JOIN sessions    s ON s.course_id = c.id
                               AND $deptWhere
       LEFT JOIN enrollments e ON e.course_id  = c.id
       LEFT JOIN attendance  a ON a.session_id = s.id
      GROUP BY d.id
      ORDER BY avg_rate DESC",
    $deptParams
);

// Daily trend
$trendWhere  = "DATE(a.scanned_at) BETWEEN ? AND ?";
$trendParams = [$dateFrom, $dateTo];
if ($courseId) { $trendWhere .= " AND s.course_id = ?";      $trendParams[] = $courseId; }
if ($deptId)   { $trendWhere .= " AND c.department_id = ?";  $trendParams[] = $deptId; }

$dailyTrend = $this->db->all(
    "SELECT DATE(a.scanned_at) AS scan_date,
            COUNT(*)            AS scan_count
       FROM attendance a
       JOIN sessions s ON s.id  = a.session_id
       JOIN courses  c ON c.id  = s.course_id
      WHERE $trendWhere
      GROUP BY DATE(a.scanned_at)
      ORDER BY scan_date ASC",
    $trendParams
);

    // Dropdown data
    $courses     = $this->db->all(
        "SELECT id, code, name, semester, year_of_study FROM courses
          WHERE is_active = 1 ORDER BY code"
    );
    $departments = $this->db->all(
        "SELECT id, name, code FROM departments ORDER BY name"
    );

    $this->view('admin/reports', [
    'user'        => Auth::user(),
    'stats'       => array_merge(
                        $stats ?? [],
                        ['avg_rate' => $avgRate ?? 0]
                     ),
    'courseStats' => $courseStats,
    'deptStats'   => $deptStats,
    'dailyTrend'  => $dailyTrend,
    'courses'     => $courses,
    'departments' => $departments,
    'dateFrom'    => $dateFrom,
    'dateTo'      => $dateTo,
    'courseId'    => $courseId,
    'deptId'      => $deptId,
    'semester'    => $semester,
    'yearStudy'   => $yearStudy,
    'groupBy'     => $groupBy,
    'flash'       => $this->getFlash(),
]);
}

    // GET /admin/reports/export
    public function exportReport(): void
{
    $dateFrom  = $this->get('date_from', date('Y-m-01'));
    $dateTo    = $this->get('date_to',   date('Y-m-d'));
    $courseId  = (int)$this->get('course_id', 0);
    $deptId    = (int)$this->get('dept_id', 0);
    $semester  = (int)$this->get('semester', 0);
    $yearStudy = (int)$this->get('year_of_study', 0);
    $format    = $this->get('format', 'csv');

    $conditions = ["s.session_date BETWEEN ? AND ?", "s.status = 'closed'"];
    $params     = [$dateFrom, $dateTo];

    if ($courseId) { $conditions[] = "s.course_id = ?";       $params[] = $courseId; }
    if ($deptId)   { $conditions[] = "c.department_id = ?";   $params[] = $deptId; }
    if ($semester) { $conditions[] = "c.semester = ?";        $params[] = $semester; }
    if ($yearStudy){ $conditions[] = "c.year_of_study = ?";   $params[] = $yearStudy; }

    $where = implode(' AND ', $conditions);

    $rows = $this->db->all(
        "SELECT u.name AS student_name, st.student_number,
                c.code AS course_code, c.name AS course_name,
                c.semester, c.year_of_study,
                d.name AS dept_name,
                s.session_date, s.start_time, s.end_time,
                COALESCE(a.status, 'absent') AS status,
                a.scanned_at,
                lu.name AS lecturer_name
           FROM sessions s
           JOIN courses    c  ON c.id  = s.course_id
           JOIN enrollments e ON e.course_id = c.id
           JOIN students   st ON st.id = e.student_id
           JOIN users      u  ON u.id  = st.user_id
           LEFT JOIN attendance a ON a.session_id = s.id AND a.student_id = st.id
           LEFT JOIN departments d ON d.id = c.department_id
           LEFT JOIN lecturers   l ON l.id = c.lecturer_id
           LEFT JOIN users      lu ON lu.id = l.user_id
          WHERE $where
          ORDER BY s.session_date DESC, c.code, u.name",
        $params
    );

    $filename = 'attendance_report_' . $dateFrom . '_to_' . $dateTo;

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '.csv"');

    $out = fopen('php://output', 'w');
    fputcsv($out, [
        'Student Name', 'Reg Number', 'Department', 'Course Code',
        'Course Name', 'Semester', 'Year', 'Lecturer',
        'Session Date', 'Start Time', 'End Time',
        'Status', 'Scanned At'
    ]);

    foreach ($rows as $row) {
        fputcsv($out, [
            $row['student_name'],
            $row['student_number'],
            $row['dept_name'] ?? '',
            $row['course_code'],
            $row['course_name'],
            $row['semester'],
            $row['year_of_study'],
            $row['lecturer_name'] ?? '',
            $row['session_date'],
            substr($row['start_time'],0,5),
            substr($row['end_time'],0,5),
            $row['status'],
            $row['scanned_at'] ?? '',
        ]);
    }

    fclose($out);
    exit;
}

public function setClassRep(array $params): void
{
    $this->validateCsrf();
    $courseId  = (int)$params['id'];
    $studentId = (int)$this->post('student_id');

    // Remove existing rep for this course
    $this->db->execute(
        "UPDATE enrollments SET is_class_rep = 0 WHERE course_id = ?", [$courseId]
    );

    // Set new rep
    $this->db->execute(
        "UPDATE enrollments SET is_class_rep = 1
          WHERE course_id = ? AND student_id = ?",
        [$courseId, $studentId]
    );

    $this->flash('success', 'Class representative assigned successfully.');
    $this->audit('classrep.assigned', 'enrollments',
    "Class rep assigned for course_id={$courseId} student_id={$studentId}");
    $this->redirect('/admin/courses/' . $courseId . '/enrollment');
}

private function audit(string $action, string $module, string $desc, $old = null, $new = null): void
{
    require_once APP_PATH . '/services/AuditService.php';
    AuditService::record($action, $module, $desc, $old, $new);
}
}
