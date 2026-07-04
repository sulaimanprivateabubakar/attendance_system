<?php
declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// ── Constants ────────────────────────────────────────────────────────────────
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH',  ROOT_PATH . '/app');
define('BASE_URL',  rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/'));

// ── Load .env ────────────────────────────────────────────────────────────────
if (file_exists(ROOT_PATH . '/.env')) {
    foreach (file(ROOT_PATH . '/.env') as $line) {
        $line = trim($line);
        // Skip empty lines and full-line comments
        if ($line === '' || str_starts_with($line, '#')) continue;
        // Must contain =
        if (!str_contains($line, '=')) continue;
        [$key, $val] = explode('=', $line, 2);
        $key = trim($key);
        $val = trim($val);
        // Strip inline comments (e.g. value # comment)
        if (str_contains($val, ' #')) {
            $val = trim(substr($val, 0, strpos($val, ' #')));
        }
        // Strip surrounding quotes
        $val = trim($val, "\"'");
        $_ENV[$key] = $val;
    }
}

// ── Timezone ─────────────────────────────────────────────────────────────────
date_default_timezone_set('Africa/Blantyre');

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

// ── Router setup ─────────────────────────────────────────────────────────────
$router = new Router();

// Auth routes (public)
$router->get( '/',         'AuthController@showLogin');
$router->get( '/login',    'AuthController@showLogin');
$router->post('/login',    'AuthController@login');
$router->get( '/register', 'AuthController@showRegister');
$router->post('/register', 'AuthController@register');
$router->get( '/logout',   'AuthController@logout');

// Attendance scan
$router->get('/attend/:token', 'AttendanceController@scan');

// Lecturer routes
$router->get( '/lecturer/dashboard',            'LecturerController@dashboard',         ['auth','role:lecturer']);
$router->get( '/lecturer/sessions',             'LecturerController@sessions',          ['auth','role:lecturer']);
$router->get( '/lecturer/sessions/create',      'LecturerController@createSessionForm', ['auth','role:lecturer']);
$router->post('/lecturer/sessions/create',      'LecturerController@createSession',     ['auth','role:lecturer']);
$router->post('/lecturer/sessions/:id/activate','LecturerController@activateSession', ['auth','role:lecturer']);
$router->get( '/lecturer/sessions/:id/scan',    'LecturerController@scanView',          ['auth','role:lecturer']);
$router->post('/lecturer/sessions/:id/activate','LecturerController@activateSession',   ['auth','role:lecturer']);
$router->post('/lecturer/sessions/:id/close',   'LecturerController@closeSession',      ['auth','role:lecturer']);

// Student routes
$router->get( '/student/dashboard',    'StudentController@dashboard',   ['auth','role:student']);
$router->get( '/student/courses/:id',  'StudentController@courseDetail',['auth','role:student']);

// Admin routes
$router->get( '/admin/dashboard',           'AdminController@dashboard',       ['auth','role:admin']);
$router->get( '/admin/users',               'AdminController@users',           ['auth','role:admin']);
$router->get( '/admin/users/create',        'AdminController@createUserForm',  ['auth','role:admin']);
$router->post('/admin/users/create',        'AdminController@createUser',      ['auth','role:admin']);
$router->post('/admin/users/:id/toggle',    'AdminController@toggleUser',      ['auth','role:admin']);
$router->get( '/admin/courses',             'AdminController@courses',         ['auth','role:admin']);
$router->get( '/admin/courses/create',      'AdminController@createCourseForm',['auth','role:admin']);
$router->post('/admin/courses/create',      'AdminController@createCourse',    ['auth','role:admin']);
$router->post('/admin/courses/:id/toggle',  'AdminController@toggleCourse',    ['auth','role:admin']);
$router->get( '/admin/departments',         'AdminController@departments',     ['auth','role:admin']);
$router->post('/admin/departments/create',  'AdminController@createDepartment',['auth','role:admin']);
$router->get( '/admin/reports',             'AdminController@reports',         ['auth','role:admin']);
$router->get( '/admin/reports/export',      'AdminController@exportReport',    ['auth','role:admin']);
$router->get( '/admin/courses/:id/enrollment', 'AdminController@enrollmentView', ['auth','role:admin']);
$router->post('/admin/courses/:id/enroll',     'AdminController@enrollStudent',  ['auth','role:admin']);

// ── Dispatch ──────────────────────────────────────────────────────────────────
// Profile routes (all authenticated users)
$router->get( '/profile',        'ProfileController@edit',   ['auth']);
$router->post('/profile/update', 'ProfileController@update', ['auth']);
$router->dispatch();