<?php
// app/controllers/AttendanceController.php

require_once APP_PATH . '/services/AttendanceService.php';

class AttendanceController extends Controller
{
    private AttendanceService $attendanceService;

    public function __construct()
    {
        parent::__construct();
        $this->attendanceService = new AttendanceService();
    }

    /**
     * GET /attend/:token
     * Student lands here after scanning the QR code.
     * If not logged in, they're redirected to login first,
     * then sent back here after authentication.
     */
    public function scan(array $params): void
    {
        $token = $params['token'] ?? '';

        // Must be logged in as a student
        if (!Auth::check()) {
            $_SESSION['intended_url'] = '/attend/' . urlencode($token);
            $this->redirect('/login');
        }

        if (!Auth::isStudent()) {
            $this->view('attendance/error', [
                'message' => 'Only students can mark attendance.',
            ]);
            return;
        }

        $ip        = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

        $result = $this->attendanceService->recordScan(
            $token,
            Auth::id(),
            $ip,
            $userAgent
        );

        $this->view('attendance/result', [
            'success' => $result['success'],
            'message' => $result['message'],
            'status'  => $result['status'] ?? null,
        ]);
    }
}
