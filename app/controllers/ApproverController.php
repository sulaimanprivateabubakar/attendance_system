<?php
// app/controllers/ApproverController.php

class ApproverController extends Controller
{
    private string $role;
    private array  $stageMap = [
        'hod'      => ['label' => 'Head of Department',  'next' => 'hoa',       'col' => 'hod'],
        'hoa'      => ['label' => 'Head of Academics',   'next' => 'registrar', 'col' => 'hoa'],
        'registrar'=> ['label' => 'Registrar',           'next' => 'vc',        'col' => 'registrar'],
        'vc'       => ['label' => 'Vice Chancellor',     'next' => 'accounts',  'col' => 'vc'],
        'accounts' => ['label' => 'Accounts Office',     'next' => 'completed', 'col' => 'accounts'],
    ];

    public function __construct()
    {
        parent::__construct();
        $this->role = Auth::role();

        // Only approver roles can access
        if (!in_array($this->role, ['hod','hoa','registrar','vc','accounts'])) {
            $this->redirect('/login');
        }
    }

    // GET /approver/dashboard
    public function dashboard(): void
    {
        $stage  = $this->role;
        $info   = $this->stageMap[$stage];

        // Claims waiting for this approver
        $pending = $this->getPendingClaims();

        // Recently processed
        $processed = $this->db->all(
            "SELECT pc.*, u.name AS lecturer_name, l.staff_number, d.name AS dept_name
               FROM payment_claims pc
               JOIN lecturers    l ON l.id = pc.lecturer_id
               JOIN users        u ON u.id = l.user_id
               LEFT JOIN departments d ON d.id = l.department_id
              WHERE pc.{$info['col']}_approved IS NOT NULL
                AND pc.{$info['col']}_approved_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
              ORDER BY pc.{$info['col']}_approved_at DESC
              LIMIT 10"
        );

        $stats = [
            'pending'   => count($pending),
            'approved'  => (int)$this->db->scalar(
                "SELECT COUNT(*) FROM payment_claims WHERE {$info['col']}_approved = 1"
            ),
            'rejected'  => (int)$this->db->scalar(
                "SELECT COUNT(*) FROM payment_claims WHERE {$info['col']}_approved = 0"
            ),
        ];

        $this->view('approver/dashboard', [
            'user'      => Auth::user(),
            'role'      => $this->role,
            'info'      => $info,
            'pending'   => $pending,
            'processed' => $processed,
            'stats'     => $stats,
            'flash'     => $this->getFlash(),
        ]);
    }

    // GET /approver/claims
    public function claims(): void
    {
        $info    = $this->stageMap[$this->role];
        $pending = $this->getPendingClaims();

        // All claims this approver has touched
        $all = $this->db->all(
            "SELECT pc.*, u.name AS lecturer_name, l.staff_number, d.name AS dept_name
               FROM payment_claims pc
               JOIN lecturers    l ON l.id = pc.lecturer_id
               JOIN users        u ON u.id = l.user_id
               LEFT JOIN departments d ON d.id = l.department_id
              WHERE pc.current_stage = ?
                 OR pc.{$info['col']}_approved IS NOT NULL
              ORDER BY pc.submitted_at DESC",
            [$this->role]
        );

        $this->view('approver/claims', [
            'user'    => Auth::user(),
            'role'    => $this->role,
            'info'    => $info,
            'pending' => $pending,
            'all'     => $all,
            'flash'   => $this->getFlash(),
        ]);
    }

    // GET /approver/claims/:id
    public function show(array $params): void
    {
        $claim = $this->db->single(
            "SELECT pc.*, u.name AS lecturer_name, l.staff_number,
                    d.name AS dept_name, l.phone AS lecturer_phone,
                    l.department_id
               FROM payment_claims pc
               JOIN lecturers    l ON l.id = pc.lecturer_id
               JOIN users        u ON u.id = l.user_id
               LEFT JOIN departments d ON d.id = l.department_id
              WHERE pc.id = ?",
            [(int)$params['id']]
        );

        if (!$claim) {
            $this->flash('error', 'Claim not found.');
            $this->redirect('/approver/claims');
        }

        $info = $this->stageMap[$this->role];

        // Get claim data (courses + totals)
        $month     = $claim['month'];
        $startDate = $month . '-01';
        $endDate   = date('Y-m-t', strtotime($startDate));

        $courses = $this->db->all(
            "SELECT c.id, c.code, c.name AS course_name, c.credit_hours,
                    COUNT(DISTINCT s.id) AS session_count,
                    COUNT(DISTINCT e.id) AS enrolled_count,
                    MIN(s.session_date)  AS first_date,
                    MAX(s.session_date)  AS last_date,
                    ROUND(SUM(TIMESTAMPDIFF(MINUTE,
                        CONCAT(s.session_date,' ',s.start_time),
                        CONCAT(s.session_date,' ',s.end_time)
                    ) / 60), 2) AS total_hours
               FROM courses c
               JOIN sessions s ON s.course_id = c.id
               LEFT JOIN enrollments e ON e.course_id = c.id
              WHERE c.lecturer_id = ?
                AND s.status = 'closed'
                AND s.session_date BETWEEN ? AND ?
              GROUP BY c.id ORDER BY c.code",
            [$claim['lecturer_id'], $startDate, $endDate]
        );

        $totalHours   = array_sum(array_column($courses, 'total_hours'));
        $totalAmount  = $totalHours * (float)$claim['hourly_rate'];
        $totalStudents = $this->db->scalar(
            "SELECT COUNT(DISTINCT e.student_id)
               FROM enrollments e
               JOIN courses c ON c.id = e.course_id
              WHERE c.lecturer_id = ?",
            [$claim['lecturer_id']]
        );
        $monthLabel = date('F Y', strtotime($month . '-01'));

        // Can this approver act on this claim?
        $canAct = $claim['current_stage'] === $this->role
               && $claim['status'] !== 'rejected';

        $this->view('approver/claim_view', [
            'user'          => Auth::user(),
            'role'          => $this->role,
            'info'          => $info,
            'claim'         => $claim,
            'courses'       => $courses,
            'totalHours'    => $totalHours,
            'totalAmount'   => $totalAmount,
            'totalStudents' => $totalStudents,
            'monthLabel'    => $monthLabel,
            'canAct'        => $canAct,
            'csrf'          => Auth::generateCsrfToken(),
            'flash'         => $this->getFlash(),
            'stageMap'      => $this->stageMap,
        ]);
    }

    // POST /approver/claims/:id/approve
    public function approve(array $params): void
    {
        $token = $_POST['_csrf'] ?? '';
        if (!Auth::verifyCsrfToken($token)) {
            $this->flash('error', 'Session expired.');
            $this->redirect('/approver/claims/' . $params['id']);
        }

        $id    = (int)$params['id'];
        $info  = $this->stageMap[$this->role];
        $col   = $info['col'];
        $next  = $info['next'];
        $name  = $this->clean($_POST['signatory_name'] ?? Auth::user()['name']);
        $notes = $this->clean($_POST['notes'] ?? '');

        // Verify claim is at this stage
        $claim = $this->db->single(
            "SELECT * FROM payment_claims WHERE id = ? AND current_stage = ?",
            [$id, $this->role]
        );

        if (!$claim) {
            $this->flash('error', 'This claim is not at your approval stage.');
            $this->redirect('/approver/claims');
        }

        // Update this stage
        $newStatus = $next === 'completed' ? 'approved' : 'submitted';

        $this->db->execute(
            "UPDATE payment_claims
                SET {$col}_approved    = 1,
                    {$col}_approved_at = NOW(),
                    {$col}_name        = ?,
                    {$col}_notes       = ?,
                    current_stage      = ?,
                    status             = ?
              WHERE id = ?",
            [$name, $notes, $next, $newStatus, $id]
        );

        // Special handling for accounts (just mark as viewed)
        if ($this->role === 'accounts') {
            $this->db->execute(
                "UPDATE payment_claims
                    SET accounts_viewed    = 1,
                        accounts_viewed_at = NOW(),
                        current_stage      = 'completed',
                        status             = 'approved'
                  WHERE id = ?",
                [$id]
            );
        }

        require_once APP_PATH . '/services/AuditService.php';
        AuditService::record(
            "claim.{$col}.approved", 'claims',
            "Claim #$id approved by {$info['label']}: $name"
        );

        $nextLabel = $this->stageMap[$next]['label'] ?? 'Accounts Office';
        $this->flash('success',
            "Claim approved. Forwarded to $nextLabel.");
        $this->redirect('/approver/claims');
    }

    // POST /approver/claims/:id/reject
    public function reject(array $params): void
    {
        $token = $_POST['_csrf'] ?? '';
        if (!Auth::verifyCsrfToken($token)) {
            $this->flash('error', 'Session expired.');
            $this->redirect('/approver/claims/' . $params['id']);
        }

        $id     = (int)$params['id'];
        $info   = $this->stageMap[$this->role];
        $name   = $this->clean($_POST['signatory_name'] ?? Auth::user()['name']);
        $reason = $this->clean($_POST['rejection_reason'] ?? '');

        if (!$reason) {
            $this->flash('error', 'Please provide a reason for rejection.');
            $this->redirect('/approver/claims/' . $id);
        }

        $this->db->execute(
            "UPDATE payment_claims
                SET status           = 'rejected',
                    current_stage    = 'rejected',
                    rejected_by      = ?,
                    rejected_at      = NOW(),
                    rejection_reason = ?
              WHERE id = ? AND current_stage = ?",
            [$this->role . ':' . $name, $reason, $id, $this->role]
        );

        require_once APP_PATH . '/services/AuditService.php';
        AuditService::record(
            "claim.{$info['col']}.rejected", 'claims',
            "Claim #$id rejected by {$info['label']}: $name — Reason: $reason"
        );

        $this->flash('error', 'Claim rejected. Lecturer has been notified.');
        $this->redirect('/approver/claims');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function getPendingClaims(): array
    {
        $info = $this->stageMap[$this->role];

        // HOD can only see claims from their department
        if ($this->role === 'hod') {
            $user   = $this->db->single(
                "SELECT department_id FROM users WHERE id = ?", [Auth::id()]
            );
            $deptId = $user['department_id'] ?? 0;

            return $this->db->all(
                "SELECT pc.*, u.name AS lecturer_name, l.staff_number, d.name AS dept_name
                   FROM payment_claims pc
                   JOIN lecturers    l ON l.id = pc.lecturer_id
                   JOIN users        u ON u.id = l.user_id
                   LEFT JOIN departments d ON d.id = l.department_id
                  WHERE pc.current_stage = 'hod'
                    AND l.department_id  = ?
                    AND pc.status        != 'rejected'
                  ORDER BY pc.submitted_at ASC",
                [$deptId]
            );
        }

        // Other roles see all claims at their stage
        return $this->db->all(
            "SELECT pc.*, u.name AS lecturer_name, l.staff_number, d.name AS dept_name
               FROM payment_claims pc
               JOIN lecturers    l ON l.id = pc.lecturer_id
               JOIN users        u ON u.id = l.user_id
               LEFT JOIN departments d ON d.id = l.department_id
              WHERE pc.current_stage = ?
                AND pc.status        != 'rejected'
              ORDER BY pc.submitted_at ASC",
            [$this->role]
        );
    }
}