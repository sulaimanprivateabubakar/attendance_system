<?php
// app/api/students.php
// Returns students not yet enrolled in a course (for admin enroll UI)

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

if (!Auth::isAdmin()) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$courseId = (int)($_GET['course_id'] ?? 0);
$search   = trim($_GET['q'] ?? '');

if (!$courseId) {
    echo json_encode(['error' => 'course_id required']);
    exit;
}

$db = Database::getInstance();

$like   = '%' . $search . '%';
$students = $db->all(
    "SELECT s.id, s.student_number, u.name, u.email
       FROM students s
       JOIN users u ON u.id = s.user_id
      WHERE s.id NOT IN (SELECT student_id FROM enrollments WHERE course_id = ?)
        AND (u.name LIKE ? OR s.student_number LIKE ? OR u.email LIKE ?)
      ORDER BY u.name
      LIMIT 30",
    [$courseId, $like, $like, $like]
);

echo json_encode(['students' => $students]);
