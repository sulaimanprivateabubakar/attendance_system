<?php
// app/controllers/LecturerController.php

require_once APP_PATH . '/services/SessionService.php';
require_once APP_PATH . '/services/AttendanceService.php';

class LecturerController extends Controller
{
    private SessionService    $sessionService;
    private AttendanceService $attendanceService;
    private int               $lecturerId;

    public function __construct()
    {
        parent::__construct();
        $this->sessionService    = new SessionService();
        $this->attendanceService = new AttendanceService();

        // Resolve lecturer profile ID from authenticated user
        $lecturer = $this->db->single(
            "SELECT id FROM lecturers WHERE user_id = ?",
            [Auth::id()]
        );
        $this->lecturerId = $lecturer['id'] ?? 0;
    }

    // GET /lecturer/dashboard
    public function dashboard(): void
    {
        $sessions = $this->sessionService->getSessionsByLecturer($this->lecturerId, 10);
        $courses  = $this->db->all(
            "SELECT c.*, COUNT(e.id) AS enrolled_count
               FROM courses c
               LEFT JOIN enrollments e ON e.course_id = c.id
              WHERE c.lecturer_id = ? AND c.is_active = 1
              GROUP BY c.id",
            [$this->lecturerId]
        );

        $this->view('lecturer/dashboard', [
            'user'     => Auth::user(),
            'sessions' => $sessions,
            'courses'  => $courses,
            'flash'    => $this->getFlash(),
        ]);
    }

    // GET /lecturer/sessions
    public function sessions(): void
    {
        $sessions = $this->sessionService->getSessionsByLecturer($this->lecturerId);
        $this->view('lecturer/sessions', [
            'user'     => Auth::user(),
            'sessions' => $sessions,
            'flash'    => $this->getFlash(),
        ]);
    }

    // GET /lecturer/sessions/create
    public function createSessionForm(): void
    {
        $courses = $this->db->all(
            "SELECT id, name, code FROM courses
              WHERE lecturer_id = ? AND is_active = 1
              ORDER BY name",
            [$this->lecturerId]
        );

        $this->view('lecturer/create_session', [
            'user'    => Auth::user(),
            'courses' => $courses,
            'csrf'    => Auth::generateCsrfToken(),
        ]);
    }

    // POST /lecturer/sessions/create
    public function createSession(): void
    {
        $this->validateCsrf();

        $result = $this->sessionService->createSession($this->lecturerId, [
            'course_id'    => (int)$this->post('course_id'),
            'title'        => $this->clean($this->post('title', '')),
            'session_date' => $this->post('session_date'),
            'start_time'   => $this->post('start_time'),
            'end_time'     => $this->post('end_time'),
        ]);

        if (!$result['success']) {
            $this->flash('error', $result['error']);
            $this->redirect('/lecturer/sessions/create');
        }

        $this->flash('success', 'Session created successfully.');
        $this->redirect('/lecturer/sessions/' . $result['session_id'] . '/scan');
    }

    // GET /lecturer/sessions/:id/scan
    public function scanView(array $params): void
    {
        $session = $this->sessionService->getSessionWithDetails((int)$params['id']);

        if (!$session || $session['lecturer_id'] != $this->lecturerId) {
            $this->redirect('/lecturer/sessions');
        }

        $scanUrl    = $this->sessionService->buildScanUrl($session['qr_token']);
        $attendance = $this->attendanceService->getSessionAttendance((int)$params['id']);

        $this->view('lecturer/scan', [
            'user'       => Auth::user(),
            'session'    => $session,
            'scanUrl'    => $scanUrl,
            'attendance' => $attendance,
            'csrf'       => Auth::generateCsrfToken(),
        ]);
    }

    // POST /lecturer/sessions/:id/activate
    public function activateSession(array $params): void
    {
        $this->validateCsrf();
        $this->sessionService->activateSession((int)$params['id'], $this->lecturerId);
        $this->redirect('/lecturer/sessions/' . $params['id'] . '/scan');
    }

    // POST /lecturer/sessions/:id/close
    public function closeSession(array $params): void
    {
        $this->validateCsrf();
        $this->sessionService->closeSession((int)$params['id'], $this->lecturerId);
        $this->flash('success', 'Session closed.');
        $this->redirect('/lecturer/sessions');
    }

    // GET /lecturer/sessions/:id/manual
public function manualAttendanceForm(array $params): void
{
    $session = $this->sessionService->getSessionWithDetails((int)$params['id']);
    if (!$session || $session['lecturer_id'] != $this->lecturerId) {
        $this->redirect('/lecturer/sessions');
    }

    // Get class rep for this course
    $classRep = $this->db->single(
        "SELECT s.id, u.name, s.student_number
           FROM enrollments e
           JOIN students s ON s.id = e.student_id
           JOIN users    u ON u.id = s.user_id
          WHERE e.course_id = ? AND e.is_class_rep = 1
          LIMIT 1",
        [$session['course_id']]
    );

    // Get pending manual requests
    $pending = $this->db->all(
        "SELECT ma.*, u.name AS student_name, s.student_number
           FROM manual_attendance ma
           JOIN students s ON s.id = ma.student_id
           JOIN users    u ON u.id = s.user_id
          WHERE ma.session_id = ? AND ma.status = 'pending'
          ORDER BY ma.created_at ASC",
        [(int)$params['id']]
    );

    $this->view('lecturer/manual_attendance', [
        'user'     => Auth::user(),
        'session'  => $session,
        'classRep' => $classRep,
        'pending'  => $pending,
        'csrf'     => Auth::generateCsrfToken(),
    ]);
}

// POST /lecturer/sessions/:id/manual
public function manualAttendance(array $params): void
{
    $this->validateCsrf();
    $sessionId = (int)$params['id'];
    $regNo     = trim($this->post('reg_number', ''));

    if (empty($regNo)) {
        $this->flash('error', 'Please enter a registration number.');
        $this->redirect('/lecturer/sessions/' . $sessionId . '/manual');
    }

    // Find student by reg number
    $student = $this->db->single(
        "SELECT s.id, u.name FROM students s
           JOIN users u ON u.id = s.user_id
          WHERE s.student_number = ? LIMIT 1",
        [$regNo]
    );

    if (!$student) {
        $this->flash('error', 'No student found with registration number: ' . htmlspecialchars($regNo));
        $this->redirect('/lecturer/sessions/' . $sessionId . '/manual');
    }

    // Check already attended
    $exists = $this->db->single(
        "SELECT id FROM attendance WHERE session_id = ? AND student_id = ?",
        [$sessionId, $student['id']]
    );
    if ($exists) {
        $this->flash('error', htmlspecialchars($student['name']) . ' has already been marked present.');
        $this->redirect('/lecturer/sessions/' . $sessionId . '/manual');
    }

    // Check if class rep exists — if yes, needs confirmation
    $session  = $this->sessionService->getSessionWithDetails($sessionId);
    $classRep = $this->db->single(
        "SELECT id FROM enrollments WHERE course_id = ? AND is_class_rep = 1",
        [$session['course_id']]
    );

    if ($classRep) {
        // Create pending manual request
        $this->db->insert(
            "INSERT INTO manual_attendance (session_id, student_id, reg_number, status, created_at)
             VALUES (?, ?, ?, 'pending', NOW())",
            [$sessionId, $student['id'], $regNo]
        );
        $this->flash('success', htmlspecialchars($student['name']) . ' — pending class rep confirmation.');
    } else {
        // No class rep — record directly
        $this->db->insert(
            "INSERT INTO attendance (session_id, student_id, status, ip_address, device_info)
             VALUES (?, ?, 'present', 'manual', 'Manual entry by lecturer')",
            [$sessionId, $student['id']]
        );
        $this->flash('success', 'Attendance recorded for ' . htmlspecialchars($student['name']));
    }

    $this->redirect('/lecturer/sessions/' . $sessionId . '/manual');
}
}
