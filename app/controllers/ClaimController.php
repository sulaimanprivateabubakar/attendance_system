<?php
// app/controllers/ClaimController.php

class ClaimController extends Controller
{
    private int $lecturerId;

    public function __construct()
    {
        parent::__construct();

        if (Auth::isLecturer()) {
            $l = $this->db->single(
                "SELECT id FROM lecturers WHERE user_id = ?", [Auth::id()]
            );
            $this->lecturerId = $l['id'] ?? 0;
        }
    }

    // ── LECTURER ─────────────────────────────────────────────────────────────

    // GET /lecturer/claims
    public function index(): void
    {
        $claims = $this->db->all(
            "SELECT pc.*, u.name AS lecturer_name
               FROM payment_claims pc
               JOIN lecturers l ON l.id = pc.lecturer_id
               JOIN users     u ON u.id = l.user_id
              WHERE pc.lecturer_id = ?
              ORDER BY pc.created_at DESC",
            [$this->lecturerId]
        );

        $this->view('lecturer/claims/index', [
            'user'   => Auth::user(),
            'claims' => $claims,
            'flash'  => $this->getFlash(),
        ]);
    }

    // GET /lecturer/claims/create
    public function createForm(): void
    {
        $lecturer = $this->db->single(
            "SELECT l.*, u.name, u.email, d.name AS dept_name
               FROM lecturers l
               JOIN users u ON u.id = l.user_id
               LEFT JOIN departments d ON d.id = l.department_id
              WHERE l.id = ?",
            [$this->lecturerId]
        );

        // Get courses with session data for current month
        $month     = $this->get('month', date('Y-m'));
        $startDate = $month . '-01';
        $endDate   = date('Y-m-t', strtotime($startDate));

        $courseSessions = $this->db->all(
            "SELECT c.id, c.code, c.name AS course_name,
                    COUNT(DISTINCT s.id)   AS session_count,
                    COUNT(DISTINCT e.id)   AS enrolled_count,
                    MIN(s.session_date)    AS first_date,
                    MAX(s.session_date)    AS last_date,
                    SUM(
                        TIMESTAMPDIFF(MINUTE,
                            CONCAT(s.session_date,' ',s.start_time),
                            CONCAT(s.session_date,' ',s.end_time)
                        ) / 60
                    ) AS total_hours
               FROM courses c
               JOIN sessions s ON s.course_id = c.id
               LEFT JOIN enrollments e ON e.course_id = c.id
              WHERE c.lecturer_id = ?
                AND s.status = 'closed'
                AND s.session_date BETWEEN ? AND ?
              GROUP BY c.id
              ORDER BY c.code",
            [$this->lecturerId, $startDate, $endDate]
        );

        $this->view('lecturer/claims/create', [
            'user'           => Auth::user(),
            'lecturer'       => $lecturer,
            'courseSessions' => $courseSessions,
            'month'          => $month,
            'startDate'      => $startDate,
            'endDate'        => $endDate,
            'csrf'           => Auth::generateCsrfToken(),
            'flash'          => $this->getFlash(),
        ]);
    }

    // POST /lecturer/claims/create
    public function create(): void
    {
        $this->validateCsrf();

        $month       = $this->post('month', date('Y-m'));
        $designation = $this->post('designation', 'part_time');
        $bankName    = $this->clean($this->post('bank_name', ''));
        $bankBranch  = $this->clean($this->post('bank_branch', ''));
        $accountNo   = $this->clean($this->post('account_number', ''));
        $telephone   = $this->clean($this->post('telephone', ''));
        $hourlyRate  = (float)$this->post('hourly_rate', 0);
        $notes       = $this->clean($this->post('notes', ''));
        $academicYear = $this->clean($this->post('academic_year', date('Y') . '/' . (date('Y') + 1)));

        // Check no duplicate claim for same month
        $exists = $this->db->single(
            "SELECT id FROM payment_claims
              WHERE lecturer_id = ? AND month = ? AND status != 'rejected'",
            [$this->lecturerId, $month]
        );

        if ($exists) {
            $this->flash('error', 'A claim for ' . $month . ' already exists.');
            $this->redirect('/lecturer/claims/create');
        }

        $id = $this->db->insert(
            "INSERT INTO payment_claims
                (lecturer_id, academic_year, month, designation, bank_name,
                 bank_branch, account_number, telephone, hourly_rate, notes)
             VALUES (?,?,?,?,?,?,?,?,?,?)",
            [$this->lecturerId, $academicYear, $month, $designation,
             $bankName, $bankBranch, $accountNo, $telephone, $hourlyRate, $notes]
        );

        $this->flash('success', 'Claim created. Review and submit when ready.');
        $this->redirect('/lecturer/claims/' . $id);
    }

    // GET /lecturer/claims/:id
   public function show(array $params): void
{
    $claim = $this->getClaim((int)$params['id']);
    $data  = $this->getClaimData($claim);

    $this->view('lecturer/claims/show', [
        'user'         => Auth::user(),
        'claim'        => $claim,
        'data'         => $data,
        'courses'      => $data['courses'],
        'totalHours'   => $data['totalHours'],
        'totalAmount'  => $data['totalAmount'],
        'totalStudents'=> $data['totalStudents'],
        'monthLabel'   => $data['monthLabel'],
        'csrf'         => Auth::generateCsrfToken(),
        'flash'        => $this->getFlash(),
    ]);
}

    // GET /lecturer/claims/:id/print
    public function printForm(array $params): void
{
    $claim = $this->getClaim((int)$params['id']);
    $data  = $this->getClaimData($claim);

    $courses       = $data['courses'];
    $totalHours    = $data['totalHours'];
    $totalAmount   = $data['totalAmount'];
    $totalStudents = $data['totalStudents'];
    $monthLabel    = $data['monthLabel'];

    require APP_PATH . '/views/lecturer/claims/print.php';
    exit;
}

    // POST /lecturer/claims/:id/submit
    public function submit(array $params): void
    {
        $this->validateCsrf();
        $this->db->execute(
            "UPDATE payment_claims SET status = 'submitted', submitted_at = NOW()
              WHERE id = ? AND lecturer_id = ? AND status = 'draft'",
            [(int)$params['id'], $this->lecturerId]
        );
        $this->flash('success', 'Claim submitted successfully.');
        $this->redirect('/lecturer/claims/' . $params['id']);
    }

    // ── ADMIN ─────────────────────────────────────────────────────────────────

    // GET /admin/claims
    public function adminIndex(): void
    {
        $claims = $this->db->all(
            "SELECT pc.*, u.name AS lecturer_name, l.staff_number,
                    d.name AS dept_name
               FROM payment_claims pc
               JOIN lecturers    l ON l.id = pc.lecturer_id
               JOIN users        u ON u.id = l.user_id
               LEFT JOIN departments d ON d.id = l.department_id
              ORDER BY pc.submitted_at DESC, pc.created_at DESC"
        );

        $this->view('admin/claims', [
            'user'   => Auth::user(),
            'claims' => $claims,
            'flash'  => $this->getFlash(),
        ]);
    }

    // POST /admin/claims/:id/approve
    public function approve(array $params): void
    {
        $this->validateCsrf();
        $this->db->execute(
            "UPDATE payment_claims SET status = 'approved' WHERE id = ?",
            [(int)$params['id']]
        );
        $this->flash('success', 'Claim approved.');
        $this->redirect('/admin/claims');
    }

    // POST /admin/claims/:id/reject
    public function reject(array $params): void
    {
        $this->validateCsrf();
        $this->db->execute(
            "UPDATE payment_claims SET status = 'rejected' WHERE id = ?",
            [(int)$params['id']]
        );
        $this->flash('error', 'Claim rejected.');
        $this->redirect('/admin/claims');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function getClaim(int $id): array
    {
        $claim = $this->db->single(
            "SELECT pc.*, l.staff_number, l.phone, l.department_id,
                    u.name AS lecturer_name, u.email,
                    d.name AS dept_name
               FROM payment_claims pc
               JOIN lecturers    l ON l.id = pc.lecturer_id
               JOIN users        u ON u.id = l.user_id
               LEFT JOIN departments d ON d.id = l.department_id
              WHERE pc.id = ?",
            [$id]
        );

        if (!$claim) {
            $this->redirect(Auth::isAdmin() ? '/admin/claims' : '/lecturer/claims');
        }

        return $claim;
    }

    private function getClaimData(array $claim): array
    {
        $month     = $claim['month'];
        $startDate = $month . '-01';
        $endDate   = date('Y-m-t', strtotime($startDate));

        $lecturerId = $claim['lecturer_id'];

        $courses = $this->db->all(
            "SELECT c.id, c.code, c.name AS course_name,
                    c.credit_hours,
                    COUNT(DISTINCT s.id) AS session_count,
                    COUNT(DISTINCT e.id) AS enrolled_count,
                    MIN(s.session_date)  AS first_date,
                    MAX(s.session_date)  AS last_date,
                    ROUND(SUM(
                        TIMESTAMPDIFF(MINUTE,
                            CONCAT(s.session_date,' ',s.start_time),
                            CONCAT(s.session_date,' ',s.end_time)
                        ) / 60
                    ), 2) AS total_hours
               FROM courses c
               JOIN sessions s ON s.course_id = c.id
               LEFT JOIN enrollments e ON e.course_id = c.id
              WHERE c.lecturer_id = ?
                AND s.status = 'closed'
                AND s.session_date BETWEEN ? AND ?
              GROUP BY c.id
              ORDER BY c.code",
            [$lecturerId, $startDate, $endDate]
        );

        $totalHours   = array_sum(array_column($courses, 'total_hours'));
        $totalAmount  = $totalHours * (float)$claim['hourly_rate'];
        $totalStudents = $this->db->scalar(
            "SELECT COUNT(DISTINCT e.student_id)
               FROM enrollments e
               JOIN courses c ON c.id = e.course_id
              WHERE c.lecturer_id = ?",
            [$lecturerId]
        );

        return [
            'lecturer'      => $claim,
            'courses'       => $courses,
            'totalHours'    => $totalHours,
            'totalAmount'   => $totalAmount,
            'totalStudents' => $totalStudents,
            'month'         => $month,
            'monthLabel'    => date('F Y', strtotime($month . '-01')),
        ];
    }
}