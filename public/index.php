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
        if ($line === '' || str_starts_with($line, '#')) continue;
        if (!str_contains($line, '=')) continue;
        [$key, $val] = explode('=', $line, 2);
        $key = trim($key);
        $val = trim($val);
        if (str_contains($val, ' #')) {
            $val = trim(substr($val, 0, strpos($val, ' #')));
        }
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

// ── Composer autoloader ──────────────────────────────────────────────────────
if (file_exists(ROOT_PATH . '/vendor/autoload.php')) {
    require_once ROOT_PATH . '/vendor/autoload.php';
}

// ── Start session ────────────────────────────────────────────────────────────
Auth::startSession();

// ── Router ───────────────────────────────────────────────────────────────────
$router = new Router();

// ── Auth routes (public) ─────────────────────────────────────────────────────
$router->get( '/',         'AuthController@showLogin');
$router->get( '/login',    'AuthController@showLogin');
$router->post('/login',    'AuthController@login');
$router->get( '/register', 'AuthController@showRegister');
$router->post('/register', 'AuthController@register');
$router->get( '/logout',   'AuthController@logout');

// ── Password reset ───────────────────────────────────────────────────────────
$router->get( '/forgot-password', 'AuthController@showForgotPassword');
$router->post('/forgot-password', 'AuthController@forgotPassword');
$router->get( '/reset-password',  'AuthController@showResetPassword');
$router->post('/reset-password',  'AuthController@resetPassword');

// ── Profile ──────────────────────────────────────────────────────────────────
$router->get( '/profile',        'ProfileController@edit',   ['auth']);
$router->post('/profile/update', 'ProfileController@update', ['auth']);

// ── Attendance scan ──────────────────────────────────────────────────────────
$router->get('/attend/:token', 'AttendanceController@scan');

// ── Student routes ───────────────────────────────────────────────────────────
$router->get( '/student/dashboard',              'StudentController@dashboard',        ['auth','role:student']);
$router->get( '/student/courses/:id',            'StudentController@courseDetail',     ['auth','role:student']);
$router->post('/student/confirm-attendance/:id', 'StudentController@confirmAttendance',['auth','role:student']);

// ── Lecturer routes ──────────────────────────────────────────────────────────
$router->get( '/lecturer/dashboard',             'LecturerController@dashboard',          ['auth','role:lecturer']);
$router->get( '/lecturer/sessions',              'LecturerController@sessions',           ['auth','role:lecturer']);
$router->get( '/lecturer/sessions/create',       'LecturerController@createSessionForm',  ['auth','role:lecturer']);
$router->post('/lecturer/sessions/create',       'LecturerController@createSession',      ['auth','role:lecturer']);
$router->get( '/lecturer/sessions/:id/scan',     'LecturerController@scanView',           ['auth','role:lecturer']);
$router->post('/lecturer/sessions/:id/activate', 'LecturerController@activateSession',    ['auth','role:lecturer']);
$router->post('/lecturer/sessions/:id/close',    'LecturerController@closeSession',       ['auth','role:lecturer']);
$router->get( '/lecturer/sessions/:id/manual',   'LecturerController@manualAttendanceForm',['auth','role:lecturer']);
$router->post('/lecturer/sessions/:id/manual',   'LecturerController@manualAttendance',   ['auth','role:lecturer']);

// ── Lecturer claim routes (specific before :id) ───────────────────────────────
$router->get( '/lecturer/claims',            'ClaimController@index',      ['auth','role:lecturer']);
$router->get( '/lecturer/claims/create',     'ClaimController@createForm', ['auth','role:lecturer']);
$router->post('/lecturer/claims/create',     'ClaimController@create',     ['auth','role:lecturer']);
$router->get( '/lecturer/claims/:id/print',  'ClaimController@printForm',  ['auth','role:lecturer']);
$router->post('/lecturer/claims/:id/submit', 'ClaimController@submit',     ['auth','role:lecturer']);
$router->get( '/lecturer/claims/:id',        'ClaimController@show',       ['auth','role:lecturer']);

// ── Admin routes ─────────────────────────────────────────────────────────────
$router->get( '/admin/dashboard',              'AdminController@dashboard',        ['auth','role:admin']);
$router->get( '/admin/users',                  'AdminController@users',            ['auth','role:admin']);
$router->get( '/admin/users/create',           'AdminController@createUserForm',   ['auth','role:admin']);
$router->post('/admin/users/create',           'AdminController@createUser',       ['auth','role:admin']);
$router->post('/admin/users/:id/toggle',       'AdminController@toggleUser',       ['auth','role:admin']);
$router->get( '/admin/courses',                'AdminController@courses',          ['auth','role:admin']);
$router->get( '/admin/courses/create',         'AdminController@createCourseForm', ['auth','role:admin']);
$router->post('/admin/courses/create',         'AdminController@createCourse',     ['auth','role:admin']);
$router->post('/admin/courses/:id/toggle',     'AdminController@toggleCourse',     ['auth','role:admin']);
$router->get( '/admin/courses/:id/enrollment', 'AdminController@enrollmentView',   ['auth','role:admin']);
$router->post('/admin/courses/:id/enroll',     'AdminController@enrollStudent',    ['auth','role:admin']);
$router->post('/admin/courses/:id/set-rep',    'AdminController@setClassRep',      ['auth','role:admin']);
$router->get( '/admin/departments',            'AdminController@departments',      ['auth','role:admin']);
$router->post('/admin/departments/create',     'AdminController@createDepartment', ['auth','role:admin']);
$router->get( '/admin/reports',                'AdminController@reports',          ['auth','role:admin']);
$router->get( '/admin/reports/export',         'AdminController@exportReport',     ['auth','role:admin']);

// ── Admin claim routes ────────────────────────────────────────────────────────
$router->get( '/admin/claims',             'ClaimController@adminIndex', ['auth','role:admin']);
$router->post('/admin/claims/:id/approve', 'ClaimController@approve',    ['auth','role:admin']);
$router->post('/admin/claims/:id/reject',  'ClaimController@reject',     ['auth','role:admin']);
$router->get( '/admin/claims',             'ClaimController@adminIndex', ['auth','role:admin']);
$router->get( '/admin/claims/:id',         'ClaimController@adminView',  ['auth','role:admin']);
$router->post('/admin/claims/:id/approve', 'ClaimController@approve',    ['auth','role:admin']);
$router->post('/admin/claims/:id/reject',  'ClaimController@reject',     ['auth','role:admin']);

// ── DISPATCH — must be last ───────────────────────────────────────────────────
$router->dispatch();