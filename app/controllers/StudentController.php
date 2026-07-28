<?php
// app/controllers/StudentController.php

class StudentController extends Controller
{
    private int $studentId;   // students.id (not users.id)

    public function __construct()
{
    parent::__construct();

    $student = $this->db->single(
        "SELECT id FROM students WHERE user_id = ?", [Auth::id()]
    );
    $this->studentId = $student['id'] ?? 0;

    error_log('StudentController: user_id=' . Auth::id() . ' student_id=' . $this->studentId);
}

    // GET /student/dashboard
public function dashboard(): void
{
    // Enrolled courses with stats
    $courses = $this->db->all(
        "SELECT c.id, c.code, c.name, c.semester, c.academic_year,
                u.name AS lecturer_name,
                COUNT(DISTINCT sess.id) AS total_sessions,
                COUNT(DISTINCT a.id)    AS attended,
                ROUND(COUNT(DISTINCT a.id) / NULLIF(COUNT(DISTINCT sess.id),0) * 100, 1) AS pct
           FROM enrollments e
           JOIN courses   c    ON c.id  = e.course_id
           JOIN lecturers l    ON l.id  = c.lecturer_id
           JOIN users     u    ON u.id  = l.user_id
           LEFT JOIN sessions   sess ON sess.course_id = c.id AND sess.status = 'closed'
           LEFT JOIN attendance a    ON a.session_id = sess.id AND a.student_id = ?
          WHERE e.student_id = ? AND c.is_active = 1
          GROUP BY c.id ORDER BY c.name",
        [$this->studentId, $this->studentId]
    );

    // Recent attendance
    $recent = $this->db->all(
        "SELECT c.code, c.name AS course_name,
                sess.session_date, sess.title,
                a.status, a.scanned_at
           FROM attendance a
           JOIN sessions sess ON sess.id = a.session_id
           JOIN courses  c    ON c.id    = sess.course_id
          WHERE a.student_id = ?
          ORDER BY a.scanned_at DESC LIMIT 10",
        [$this->studentId]
    );

    // Check if this student is a class rep for ANY course
    $isClassRep = $this->db->single(
        "SELECT e.id, e.course_id, c.name AS course_name, c.code AS course_code
           FROM enrollments e
           JOIN courses c ON c.id = e.course_id
          WHERE e.student_id = ? AND e.is_class_rep = 1
          LIMIT 1",
        [$this->studentId]
    );

    // Debug — log what we found
    error_log('Class rep check for student ' . $this->studentId . ': ' . json_encode($isClassRep));

    // Get pending manual confirmations if class rep
$pendingConfirmations = [];
if ($isClassRep) {
    // Get ALL pending manual attendance across all sessions
    // not just sessions matching course_id directly
    $pendingConfirmations = $this->db->all(
        "SELECT ma.id,
                ma.reg_number,
                ma.created_at,
                ma.status,
                u.name         AS student_name,
                s.student_number,
                sess.session_date,
                sess.id        AS session_id,
                c.name         AS course_name,
                c.code         AS course_code
           FROM manual_attendance ma
           JOIN students  s    ON s.id    = ma.student_id
           JOIN users     u    ON u.id    = s.user_id
           JOIN sessions  sess ON sess.id = ma.session_id
           JOIN courses   c    ON c.id    = sess.course_id
           JOIN enrollments e  ON e.course_id = sess.course_id
                               AND e.student_id = ?
                               AND e.is_class_rep = 1
          WHERE ma.status = 'pending'
          ORDER BY ma.created_at ASC",
        [$this->studentId]
    );
}

        error_log('Pending confirmations: ' . json_encode($pendingConfirmations));


    $this->view('student/dashboard', [
        'user'                 => Auth::user(),
        'courses'              => $courses,
        'recent'               => $recent,
        'isClassRep'           => $isClassRep,
        'pendingConfirmations' => $pendingConfirmations,
        'flash'                => $this->getFlash(),
    ]);
}

    // GET /student/courses/:id
    public function courseDetail(array $params): void
    {
        $courseId = (int)$params['id'];

        // Verify enrollment
        $enrolled = $this->db->single(
            "SELECT e.id FROM enrollments e WHERE e.student_id = ? AND e.course_id = ?",
            [$this->studentId, $courseId]
        );
        if (!$enrolled) { $this->redirect('/student/dashboard'); }

        $course  = $this->db->single("SELECT * FROM courses WHERE id = ?", [$courseId]);

        $history = $this->db->all(
            "SELECT sess.session_date, sess.start_time, sess.title,
                    COALESCE(a.status, 'absent') AS status,
                    a.scanned_at
               FROM sessions sess
               LEFT JOIN attendance a ON a.session_id = sess.id AND a.student_id = ?
              WHERE sess.course_id = ?
                AND sess.status = 'closed'
              ORDER BY sess.session_date DESC",
            [$this->studentId, $courseId]
        );

        $this->view('student/course_detail', [
            'user'    => Auth::user(),
            'course'  => $course,
            'history' => $history,
        ]);
    }

// POST /student/confirm-attendance/:id
public function confirmAttendance(array $params): void
{
    $this->validateCsrf();
    $id     = (int)$params['id'];
    $action = $this->post('action', 'confirm');

    // Verify this student is class rep for this session's course
    $manual = $this->db->single(
        "SELECT ma.*, sess.course_id FROM manual_attendance ma
           JOIN sessions sess ON sess.id = ma.session_id
          WHERE ma.id = ? AND ma.status = 'pending'",
        [$id]
    );

    if (!$manual) {
        $this->flash('error', 'Request not found or already processed.');
        $this->redirect('/student/dashboard');
    }

    $isRep = $this->db->single(
        "SELECT id FROM enrollments
          WHERE student_id = ? AND course_id = ? AND is_class_rep = 1",
        [$this->studentId, $manual['course_id']]
    );

    if (!$isRep) {
        $this->flash('error', 'You are not the class rep for this course.');
        require_once APP_PATH . '/services/AuditService.php';
        AuditService::record(
        'attendance.manual.' . $action,
        'attendance',
        "Class rep {$action}ed manual attendance request id=$id"
        );
        $this->redirect('/student/dashboard');
    }

    if ($action === 'confirm') {
        // Record actual attendance
        $this->db->insert(
            "INSERT IGNORE INTO attendance (session_id, student_id, status, ip_address, device_info)
             VALUES (?, ?, 'present', 'manual', 'Manual — confirmed by class rep')",
            [$manual['session_id'], $manual['student_id']]
        );
        $this->db->execute(
            "UPDATE manual_attendance SET status = 'confirmed' WHERE id = ?", [$id]
        );
        $this->flash('success', 'Attendance confirmed successfully.');
    } else {
        $this->db->execute(
            "UPDATE manual_attendance SET status = 'rejected' WHERE id = ?", [$id]
        );
        $this->flash('error', 'Attendance request rejected.');
    }

    $this->redirect('/student/dashboard');
}


// GET /student/rep-dashboard
public function repDashboard(): void
{
    // Verify is class rep
    $isClassRep = $this->db->single(
        "SELECT e.id, e.course_id, c.name AS course_name, c.code AS course_code
           FROM enrollments e
           JOIN courses c ON c.id = e.course_id
          WHERE e.student_id = ? AND e.is_class_rep = 1
          LIMIT 1",
        [$this->studentId]
    );

    if (!$isClassRep) {
        $this->flash('error', 'You are not assigned as a class rep.');
        $this->redirect('/student/dashboard');
    }

    // All pending
    $pending = $this->db->all(
        "SELECT ma.id,
                ma.reg_number,
                ma.created_at,
                ma.status,
                u.name         AS student_name,
                s.student_number,
                sess.session_date,
                sess.start_time,
                sess.id        AS session_id,
                c.name         AS course_name,
                c.code         AS course_code
           FROM manual_attendance ma
           JOIN students  s    ON s.id    = ma.student_id
           JOIN users     u    ON u.id    = s.user_id
           JOIN sessions  sess ON sess.id = ma.session_id
           JOIN courses   c    ON c.id    = sess.course_id
           JOIN enrollments e  ON e.course_id = sess.course_id
                               AND e.student_id = ?
                               AND e.is_class_rep = 1
          WHERE ma.status = 'pending'
          ORDER BY ma.created_at ASC",
        [$this->studentId]
    );

    // Confirmed today
    $confirmedToday = $this->db->all(
        "SELECT ma.id,
                ma.reg_number,
                ma.created_at,
                u.name AS student_name,
                s.student_number,
                c.code AS course_code,
                sess.session_date
           FROM manual_attendance ma
           JOIN students  s    ON s.id    = ma.student_id
           JOIN users     u    ON u.id    = s.user_id
           JOIN sessions  sess ON sess.id = ma.session_id
           JOIN courses   c    ON c.id    = sess.course_id
           JOIN enrollments e  ON e.course_id = sess.course_id
                               AND e.student_id = ?
                               AND e.is_class_rep = 1
          WHERE ma.status IN ('confirmed','rejected')
            AND DATE(ma.created_at) = CURDATE()
          ORDER BY ma.created_at DESC",
        [$this->studentId]
    );

    $this->view('student/rep_dashboard', [
        'user'           => Auth::user(),
        'isClassRep'     => $isClassRep,
        'pending'        => $pending,
        'confirmedToday' => $confirmedToday,
        'csrf'           => Auth::generateCsrfToken(),
        'flash'          => $this->getFlash(),
    ]);
}
}