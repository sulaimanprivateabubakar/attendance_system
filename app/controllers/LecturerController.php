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
}
