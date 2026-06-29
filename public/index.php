<?php
declare(strict_types=1);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// public/index.php  –  Single entry point for all requests



// ── Constants ────────────────────────────────────────────────────────────────
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH',  ROOT_PATH . '/app');
define('BASE_URL',  rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/'));

// ── Load .env ────────────────────────────────────────────────────────────────
if (file_exists(ROOT_PATH . '/.env')) {
    foreach (file(ROOT_PATH . '/.env') as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) continue;
        [$key, $val] = explode('=', $line, 2) + [1 => ''];
        $_ENV[trim($key)] = trim($val, " \t\n\r\0\x0B\"'");
    }
}

// ── Timezone ─────────────────────────────────────────────────────────────────
date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'UTC');

// ── Autoload core classes ────────────────────────────────────────────────────
require_once APP_PATH . '/core/Database.php';
require_once APP_PATH . '/core/Auth.php';
require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/core/Router.php';

// ── Composer autoloader (when available) ────────────────────────────────────
if (file_exists(ROOT_PATH . '/vendor/autoload.php')) {
    require_once ROOT_PATH . '/vendor/autoload.php';
}

// ── Start session ────────────────────────────────────────────────────────────
Auth::startSession();

// ── Error display (dev only) ──────────────────────────────────────────────────
if (($_ENV['APP_DEBUG'] ?? 'false') === 'true') {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
}

// ── Router setup ─────────────────────────────────────────────────────────────
$router = new Router();

// Auth routes (public)
$router->get( '/',          'AuthController@showLogin');
$router->get( '/login',     'AuthController@showLogin');
$router->post('/login',     'AuthController@login');
$router->get( '/register',  'AuthController@showRegister');
$router->post('/register',  'AuthController@register');
$router->get( '/logout',    'AuthController@logout');

// Attendance scan (auth:student required – checked inside controller)
$router->get('/attend/:token', 'AttendanceController@scan');

// Lecturer routes
$router->get( '/lecturer/dashboard',           'LecturerController@dashboard',        ['auth','role:lecturer']);
$router->get( '/lecturer/sessions',            'LecturerController@sessions',         ['auth','role:lecturer']);
$router->get( '/lecturer/sessions/create',     'LecturerController@createSessionForm',['auth','role:lecturer']);
$router->post('/lecturer/sessions/create',     'LecturerController@createSession',    ['auth','role:lecturer']);
$router->get( '/lecturer/sessions/:id/scan',   'LecturerController@scanView',         ['auth','role:lecturer']);
$router->post('/lecturer/sessions/:id/activate','LecturerController@activateSession', ['auth','role:lecturer']);
$router->post('/lecturer/sessions/:id/close',  'LecturerController@closeSession',     ['auth','role:lecturer']);

// Student routes
$router->get('/student/dashboard', 'StudentController@dashboard', ['auth','role:student']);

// Admin routes
$router->get('/admin/dashboard', 'AdminController@dashboard', ['auth','role:admin']);
$router->get('/admin/reports',   'AdminController@reports',   ['auth','role:admin']);

// ── Dispatch ──────────────────────────────────────────────────────────────────
$router->dispatch();
