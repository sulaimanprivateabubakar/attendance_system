<?php
// app/api/reports.php
// Returns attendance summary for a course as JSON

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

if (!Auth::isAdmin() && !Auth::isLecturer()) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$courseId = (int)($_GET['course_id'] ?? 0);
if (!$courseId) {
    echo json_encode(['error' => 'course_id required']);
    exit;
}

$db = Database::getInstance();

$summary = $db->all(
    "SELECT u.name, st.student_number,
            COUNT(DISTINCT sess.id)                                       AS total_sessions,
            SUM(CASE WHEN a.id IS NOT NULL THEN 1 ELSE 0 END)            AS attended,
            ROUND(
                SUM(CASE WHEN a.id IS NOT NULL THEN 1 ELSE 0 END)
                / NULLIF(COUNT(DISTINCT sess.id), 0) * 100, 1
            )                                                             AS pct
       FROM enrollments e
       JOIN students st   ON st.id = e.student_id
       JOIN users    u    ON u.id  = st.user_id
       JOIN sessions sess ON sess.course_id = e.course_id AND sess.status = 'closed'
       LEFT JOIN attendance a ON a.session_id = sess.id AND a.student_id = st.id
      WHERE e.course_id = ?
      GROUP BY st.id
      ORDER BY u.name",
    [$courseId]
);

echo json_encode(['summary' => $summary, 'count' => count($summary)]);
