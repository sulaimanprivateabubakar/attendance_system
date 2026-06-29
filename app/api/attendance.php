<?php
// app/api/attendance.php – returns live attendance for a session (JSON)

define('ROOT_PATH', dirname(__DIR__, 2));
define('APP_PATH',  ROOT_PATH . '/app');

// Load env
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

if (!Auth::isLecturer()) {
    echo json_encode(['error' => 'Unauthorized']); exit;
}

$sessionId = (int)($_GET['session_id'] ?? 0);
if (!$sessionId) {
    echo json_encode(['error' => 'Missing session_id']); exit;
}

$db = Database::getInstance();

// Verify ownership
$lecturer = $db->single("SELECT id FROM lecturers WHERE user_id = ?", [Auth::id()]);
$session  = $db->single(
    "SELECT id FROM sessions WHERE id = ? AND lecturer_id = ?",
    [$sessionId, $lecturer['id'] ?? 0]
);

if (!$session) {
    echo json_encode(['error' => 'Session not found']); exit;
}

$records = $db->all(
    "SELECT u.name, s.student_number,
            DATE_FORMAT(a.scanned_at, '%H:%i') AS scanned_at,
            a.status
       FROM attendance a
       JOIN students s ON s.id = a.student_id
       JOIN users u    ON u.id = s.user_id
      WHERE a.session_id = ?
      ORDER BY a.scanned_at ASC",
    [$sessionId]
);

echo json_encode(['count' => count($records), 'records' => $records]);
