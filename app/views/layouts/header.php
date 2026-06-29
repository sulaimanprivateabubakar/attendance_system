<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'QR Attendance System') ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/app.css">
</head>
<body>

<?php if (Auth::check()): ?>
<nav class="navbar">
    <div class="navbar-brand">📋 QR Attendance</div>
    <div class="navbar-menu">
        <?php if (Auth::isAdmin()): ?>
            <a href="<?= BASE_URL ?>/admin/dashboard">Dashboard</a>
            <a href="<?= BASE_URL ?>/admin/users">Users</a>
            <a href="<?= BASE_URL ?>/admin/courses">Courses</a>
            <a href="<?= BASE_URL ?>/admin/departments">Departments</a>
            <a href="<?= BASE_URL ?>/admin/reports">Reports</a>
        <?php elseif (Auth::isLecturer()): ?>
            <a href="<?= BASE_URL ?>/lecturer/dashboard">Dashboard</a>
            <a href="<?= BASE_URL ?>/lecturer/sessions">Sessions</a>
            <a href="<?= BASE_URL ?>/lecturer/sessions/create">+ New Session</a>
        <?php elseif (Auth::isStudent()): ?>
            <a href="<?= BASE_URL ?>/student/dashboard">Dashboard</a>
        <?php endif; ?>
        <span class="navbar-user">👤 <?= htmlspecialchars(Auth::user()['name']) ?></span>
        <a href="<?= BASE_URL ?>/logout" class="btn-logout">Logout</a>
    </div>
</nav>
<?php endif; ?>

<main class="container">

<?php
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
if ($flash):
?>
<div class="alert alert-<?= htmlspecialchars($flash['type']) ?>">
    <?= htmlspecialchars($flash['message']) ?>
</div>
<?php endif; ?>