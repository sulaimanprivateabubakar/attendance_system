<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#111827">
    <title><?= htmlspecialchars($pageTitle ?? 'Dashboard') ?> — QR Attendance</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/app.css">
    <script>window.BASE_URL = '<?= BASE_URL ?>';</script>
</head>
<body>

<?php if (Auth::check()): ?>

<div class="wrapper">

    <!-- ── SIDEBAR ─────────────────────────────────────────── -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-logo">
            <div class="auth-logo">
    <img src="<?= BASE_URL ?>/assets/images/logo.webp"
         alt="IQRA'A e-Attendance Logo">
</div>
            <span>Iqra'a University</span>
        </div>

        <?php if (Auth::isAdmin()): ?>
        <div class="sidebar-section">
            <div class="sidebar-section-label">Main</div>
            <ul>
                <li><a href="<?= BASE_URL ?>/admin/dashboard"><i class="fas fa-th-large"></i> Dashboard</a></li>
            </ul>
        </div>
        <div class="sidebar-section">
            <div class="sidebar-section-label">Management</div>
            <ul>
                <li><a href="<?= BASE_URL ?>/admin/users"><i class="fas fa-users"></i> Users</a></li>
                <li><a href="<?= BASE_URL ?>/admin/courses"><i class="fas fa-book"></i> Courses</a></li>
                <li><a href="<?= BASE_URL ?>/admin/departments"><i class="fas fa-building"></i> Departments</a></li>
            </ul>
        </div>
        <div class="sidebar-section">
    <div class="sidebar-section-label">Analytics</div>
    <ul>
        <li><a href="<?= BASE_URL ?>/admin/reports">
            <i class="fas fa-chart-bar"></i> Reports</a></li>
        <li><a href="<?= BASE_URL ?>/admin/claims">
            <i class="fas fa-file-invoice-dollar"></i> Payment Claims</a></li>
    </ul>
</div>


        <?php elseif (Auth::isLecturer()): ?>
<div class="sidebar-section">
    <div class="sidebar-section-label">Main</div>
    <ul>
        <li><a href="<?= BASE_URL ?>/lecturer/dashboard"><i class="fas fa-th-large"></i> Dashboard</a></li>
        <li><a href="<?= BASE_URL ?>/lecturer/sessions"><i class="fas fa-list"></i> Sessions</a></li>
    </ul>
</div>
<div class="sidebar-section">
    <div class="sidebar-section-label">Actions</div>
    <ul>
        <li><a href="<?= BASE_URL ?>/lecturer/sessions/create"><i class="fas fa-plus-circle"></i> New Session</a></li>
        <li><a href="<?= BASE_URL ?>/lecturer/claims"><i class="fas fa-file-invoice-dollar"></i> Payment Claims</a></li>
    </ul>
</div>
<div class="sidebar-section">
    <div class="sidebar-section-label">Analytics</div>
</div>
        <?php elseif (Auth::isStudent()): ?>
        <div class="sidebar-section">
            <div class="sidebar-section-label">Main</div>
            <ul>
                <li><a href="<?= BASE_URL ?>/student/dashboard"><i class="fas fa-th-large"></i> Dashboard</a></li>
            </ul>
        </div>
        <?php endif; ?>

        <div class="sidebar-section">
            <div class="sidebar-section-label">Account</div>
            <ul>
                <li><a href="<?= BASE_URL ?>/profile"><i class="fas fa-user-circle"></i> My Profile</a></li>
                <li><a href="<?= BASE_URL ?>/logout"><i class="fas fa-sign-out-alt"></i> Sign Out</a></li>
            </ul>
        </div>

        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="sidebar-avatar">
                    <?= strtoupper(substr(Auth::user()['name'], 0, 2)) ?>
                </div>
                <div class="sidebar-user-info">
                    <h4><?= htmlspecialchars(Auth::user()['name']) ?></h4>
                    <p><?= ucfirst(Auth::role()) ?></p>
                </div>
            </div>
        </div>
    </aside>

    <!-- ── MAIN ────────────────────────────────────────────── -->
    <div class="main">

        <!-- Topbar -->
        <div class="topbar">
            <div class="topbar-left">
                <button class="menu-btn" aria-label="Toggle sidebar">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search...">
                </div>
            </div>
            <div class="topbar-right">
                <span class="topbar-clock" id="clock"></span>
                <button class="icon-btn" id="themeToggle" title="Toggle theme" aria-label="Toggle dark/light mode">
                    <i class="fas fa-sun"></i>
                </button>
                </button>
                <button class="icon-btn" title="Notifications">
                    <i class="fas fa-bell"></i>
                    <span class="notif-badge">1</span>
                </button>
            </div>
        </div>

        <!-- Page Content -->
        <div class="page-content">

<?php
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
if ($flash):
?>
<div class="alert alert-<?= htmlspecialchars($flash['type']) ?>">
    <i class="fas fa-<?= $flash['type'] === 'success' ? 'check-circle' : ($flash['type'] === 'error' ? 'times-circle' : 'info-circle') ?>"></i>
    <?= htmlspecialchars($flash['message']) ?>
</div>
<?php endif; ?>

<?php else: ?>
<div class="auth-wrapper">
<?php
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
if ($flash):
?>
<div class="alert alert-<?= htmlspecialchars($flash['type']) ?>" style="position:fixed;top:20px;left:50%;transform:translateX(-50%);z-index:9999;min-width:320px">
    <?= htmlspecialchars($flash['message']) ?>
</div>
<?php endif; ?>
<?php endif; ?>