<?php
define('ROOT_PATH', dirname(__DIR__, 2));
define('APP_PATH',  ROOT_PATH . '/app');
define('BASE_URL',  '');

if (file_exists(ROOT_PATH . '/.env')) {
    foreach (file(ROOT_PATH . '/.env') as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) continue;
        [$key, $val] = explode('=', $line, 2) + [1 => ''];
        $_ENV[trim($key)] = trim($val, " \t\n\r\0\x0B\"'");
    }
}

require_once APP_PATH . '/core/Database.php';
require_once APP_PATH . '/core/Auth.php';

Auth::startSession();
header('Content-Type: application/json');

if (!Auth::check()) {
    echo json_encode(['notifications' => [], 'count' => 0]);
    exit;
}

$db   = Database::getInstance();
$role = Auth::role();
$id   = Auth::id();
$notifs = [];

// ── ADMIN notifications ──────────────────────────────────────────
if ($role === 'admin') {

    // New students registered in last 7 days
    $newStudents = $db->scalar(
        "SELECT COUNT(*) FROM users
          WHERE role = 'student'
            AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)"
    );
    if ($newStudents > 0) {
        $notifs[] = [
            'icon'    => '🎓',
            'color'   => 'rgba(37,99,235,.15)',
            'title'   => $newStudents . ' new student' . ($newStudents > 1 ? 's' : '') . ' registered',
            'message' => 'In the last 7 days',
            'time'    => 'This week',
        ];
    }

    // Active sessions right now
    $activeSessions = $db->scalar(
        "SELECT COUNT(*) FROM sessions WHERE status = 'active'"
    );
    if ($activeSessions > 0) {
        $notifs[] = [
            'icon'    => '📡',
            'color'   => 'rgba(34,197,94,.15)',
            'title'   => $activeSessions . ' session' . ($activeSessions > 1 ? 's' : '') . ' live right now',
            'message' => 'Students can currently scan QR codes',
            'time'    => 'Now',
        ];
    }

    // Scans today
    $scansToday = $db->scalar(
        "SELECT COUNT(*) FROM attendance WHERE DATE(scanned_at) = CURDATE()"
    );
    if ($scansToday > 0) {
        $notifs[] = [
            'icon'    => '✅',
            'color'   => 'rgba(34,197,94,.15)',
            'title'   => $scansToday . ' attendance scan' . ($scansToday > 1 ? 's' : '') . ' today',
            'message' => 'Recorded today across all courses',
            'time'    => 'Today',
        ];
    }

    // Courses with no sessions yet
    $noSessions = $db->scalar(
        "SELECT COUNT(*) FROM courses c
          WHERE c.is_active = 1
            AND NOT EXISTS (SELECT 1 FROM sessions s WHERE s.course_id = c.id)"
    );
    if ($noSessions > 0) {
        $notifs[] = [
            'icon'    => '⚠️',
            'color'   => 'rgba(245,158,11,.15)',
            'title'   => $noSessions . ' course' . ($noSessions > 1 ? 's' : '') . ' with no sessions',
            'message' => 'Lecturers have not created any sessions yet',
            'time'    => '',
        ];
    }

    // Pending sessions (created but not activated)
    $pendingSessions = $db->scalar(
        "SELECT COUNT(*) FROM sessions WHERE status = 'pending'"
    );
    if ($pendingSessions > 0) {
        $notifs[] = [
            'icon'    => '🕐',
            'color'   => 'rgba(245,158,11,.15)',
            'title'   => $pendingSessions . ' pending session' . ($pendingSessions > 1 ? 's' : ''),
            'message' => 'Sessions created but not yet activated',
            'time'    => '',
        ];
    }
}

// ── LECTURER notifications ───────────────────────────────────────
if ($role === 'lecturer') {

    $lecturer = $db->single(
        "SELECT id FROM lecturers WHERE user_id = ?", [$id]
    );
    $lecturerId = $lecturer['id'] ?? 0;

    // Active session right now
    $activeSession = $db->single(
        "SELECT s.id, c.name AS course_name, COUNT(a.id) AS att_count
           FROM sessions s
           JOIN courses c ON c.id = s.course_id
           LEFT JOIN attendance a ON a.session_id = s.id
          WHERE s.lecturer_id = ? AND s.status = 'active'
          GROUP BY s.id
          LIMIT 1",
        [$lecturerId]
    );

    if ($activeSession) {
        $notifs[] = [
            'icon'    => '📡',
            'color'   => 'rgba(34,197,94,.15)',
            'title'   => 'Session is LIVE — ' . htmlspecialchars($activeSession['course_name']),
            'message' => $activeSession['att_count'] . ' student(s) have scanned so far',
            'time'    => 'Now',
        ];
    }

    // Today's sessions
    $todaySessions = $db->scalar(
        "SELECT COUNT(*) FROM sessions
          WHERE lecturer_id = ? AND session_date = CURDATE()",
        [$lecturerId]
    );
    if ($todaySessions > 0) {
        $notifs[] = [
            'icon'    => '📅',
            'color'   => 'rgba(37,99,235,.15)',
            'title'   => $todaySessions . ' session' . ($todaySessions > 1 ? 's' : '') . ' scheduled today',
            'message' => 'Check your sessions list',
            'time'    => 'Today',
        ];
    }

    // Total attendance today across all courses
    $attToday = $db->scalar(
        "SELECT COUNT(*) FROM attendance a
           JOIN sessions s ON s.id = a.session_id
          WHERE s.lecturer_id = ?
            AND DATE(a.scanned_at) = CURDATE()",
        [$lecturerId]
    );
    if ($attToday > 0) {
        $notifs[] = [
            'icon'    => '✅',
            'color'   => 'rgba(34,197,94,.15)',
            'title'   => $attToday . ' student' . ($attToday > 1 ? 's' : '') . ' marked attendance today',
            'message' => 'Across all your courses today',
            'time'    => 'Today',
        ];
    }

    // Courses with enrolled students
    $myCourses = $db->scalar(
        "SELECT COUNT(*) FROM courses WHERE lecturer_id = ? AND is_active = 1",
        [$lecturerId]
    );
    $notifs[] = [
        'icon'    => '📚',
        'color'   => 'rgba(139,92,246,.15)',
        'title'   => $myCourses . ' active course' . ($myCourses > 1 ? 's' : '') . ' assigned',
        'message' => 'You can create sessions for these courses',
        'time'    => '',
    ];
}

// ── STUDENT notifications ────────────────────────────────────────
if ($role === 'student') {

    $student = $db->single(
        "SELECT id FROM students WHERE user_id = ?", [$id]
    );
    $studentId = $student['id'] ?? 0;

    // Attendance today
    $todayAtt = $db->single(
        "SELECT COUNT(*) AS cnt, c.name AS course_name
           FROM attendance a
           JOIN sessions s ON s.id = a.session_id
           JOIN courses  c ON c.id = s.course_id
          WHERE a.student_id = ?
            AND DATE(a.scanned_at) = CURDATE()
          LIMIT 1",
        [$studentId]
    );

    if ($todayAtt && $todayAtt['cnt'] > 0) {
        $notifs[] = [
            'icon'    => '✅',
            'color'   => 'rgba(34,197,94,.15)',
            'title'   => 'Attendance marked today',
            'message' => 'You have scanned in ' . $todayAtt['cnt'] . ' session(s) today',
            'time'    => 'Today',
        ];
    }

    // Active sessions for enrolled courses
    $activeSessions = $db->scalar(
        "SELECT COUNT(*) FROM sessions s
           JOIN enrollments e ON e.course_id = s.course_id
          WHERE e.student_id = ?
            AND s.status = 'active'
            AND s.qr_expires_at > NOW()",
        [$studentId]
    );

    if ($activeSessions > 0) {
        $notifs[] = [
            'icon'    => '📡',
            'color'   => 'rgba(239,68,68,.15)',
            'title'   => $activeSessions . ' live session' . ($activeSessions > 1 ? 's' : '') . ' right now!',
            'message' => 'Scan the QR code to mark your attendance',
            'time'    => 'Now',
        ];
    }

    // Overall attendance rate
    $rate = $db->single(
        "SELECT ROUND(COUNT(a.id) / NULLIF(COUNT(s.id), 0) * 100, 1) AS rate
           FROM sessions s
           JOIN enrollments e ON e.course_id = s.course_id
           LEFT JOIN attendance a ON a.session_id = s.id AND a.student_id = ?
          WHERE e.student_id = ? AND s.status = 'closed'",
        [$studentId, $studentId]
    );

    $pct = (float)($rate['rate'] ?? 0);
    if ($pct > 0) {
        $emoji = $pct >= 75 ? '🌟' : ($pct >= 50 ? '⚠️' : '❌');
        $color = $pct >= 75
            ? 'rgba(34,197,94,.15)'
            : ($pct >= 50 ? 'rgba(245,158,11,.15)' : 'rgba(239,68,68,.15)');
        $notifs[] = [
            'icon'    => $emoji,
            'color'   => $color,
            'title'   => 'Overall attendance rate: ' . $pct . '%',
            'message' => $pct >= 75
                ? 'Great job! Keep it up.'
                : ($pct >= 50 ? 'Attendance is below recommended level.' : 'Critical: attendance is very low!'),
            'time'    => '',
        ];
    }

    // Enrolled courses count
    $enrolled = $db->scalar(
        "SELECT COUNT(*) FROM enrollments WHERE student_id = ?", [$studentId]
    );
    $notifs[] = [
        'icon'    => '📚',
        'color'   => 'rgba(37,99,235,.15)',
        'title'   => 'Enrolled in ' . $enrolled . ' course' . ($enrolled != 1 ? 's' : ''),
        'message' => 'View your attendance history on the dashboard',
        'time'    => '',
    ];
}

// If nothing to show
if (empty($notifs)) {
    $notifs[] = [
        'icon'    => '🎉',
        'color'   => 'rgba(34,197,94,.15)',
        'title'   => 'All caught up!',
        'message' => 'No new notifications at this time.',
        'time'    => '',
    ];
}

echo json_encode([
    'notifications' => $notifs,
    'count'         => count($notifs),
]);