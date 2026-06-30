<?php
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH',  ROOT_PATH . '/app');

require_once APP_PATH . '/core/Database.php';

if (file_exists(ROOT_PATH . '/.env')) {
    foreach (file(ROOT_PATH . '/.env') as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) continue;
        [$key, $val] = explode('=', $line, 2) + [1 => ''];
        $_ENV[trim($key)] = trim($val, " \t\n\r\0\x0B\"'");
    }
}

$db = Database::getInstance();

// Generate a fresh hash and update directly via PHP
$newPassword = 'admin123';
$newHash     = password_hash($newPassword, PASSWORD_BCRYPT);

$db->execute(
    "UPDATE users SET password = ? WHERE email = 'admin@university.edu'",
    [$newHash]
);

// Verify it worked
$user = $db->single("SELECT password FROM users WHERE email = 'admin@university.edu'");
$works = password_verify($newPassword, $user['password']);

echo "<pre>";
echo "New hash saved: " . $user['password'] . "\n\n";
echo "Verify test:    " . ($works ? '✅ WORKS' : '❌ FAILED') . "\n\n";
if ($works) {
    echo "✅ You can now log in with:\n";
    echo "   Email:    admin@university.edu\n";
    echo "   Password: admin123\n";
}
echo "</pre>";