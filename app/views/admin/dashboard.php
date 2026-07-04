<?php $pageTitle = 'Dashboard'; ?>

<div class="page-title">
    <div>
        <h1>Admin Dashboard</h1>
        <p>Welcome back, <?= htmlspecialchars($user['name']) ?></p>
    </div>
    <div class="actions">
        <a href="<?= BASE_URL ?>/admin/users/create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add User
        </a>
        <a href="<?= BASE_URL ?>/admin/courses/create" class="btn btn-secondary">
            <i class="fas fa-book"></i> Add Course
        </a>
    </div>
</div>

<!-- Stats -->
<div class="stats">
    <a href="<?= BASE_URL ?>/admin/users?filter=student" class="stat-card" style="text-decoration:none;cursor:pointer">
        <div class="stat-info">
            <h3>Students</h3>
            <h1 data-counter="<?= (int)$stats['students'] ?>"><?= (int)$stats['students'] ?></h1>
            <small>Registered</small>
        </div>
        <div class="stat-icon blue"><i class="fas fa-user-graduate"></i></div>
    </a>
    <a href="<?= BASE_URL ?>/admin/users?filter=lecturer" class="stat-card" style="text-decoration:none;cursor:pointer">
        <div class="stat-info">
            <h3>Lecturers</h3>
            <h1 data-counter="<?= (int)$stats['lecturers'] ?>"><?= (int)$stats['lecturers'] ?></h1>
            <small>Active staff</small>
        </div>
        <div class="stat-icon green"><i class="fas fa-chalkboard-teacher"></i></div>
    </a>
    <a href="<?= BASE_URL ?>/admin/courses" class="stat-card" style="text-decoration:none;cursor:pointer">
        <div class="stat-info">
            <h3>Courses</h3>
            <h1 data-counter="<?= (int)$stats['courses'] ?>"><?= (int)$stats['courses'] ?></h1>
            <small>Active courses</small>
        </div>
        <div class="stat-icon amber"><i class="fas fa-book-open"></i></div>
    </a>
    <a href="<?= BASE_URL ?>/admin/reports" class="stat-card" style="text-decoration:none;cursor:pointer">
        <div class="stat-info">
            <h3>Scans Today</h3>
            <h1 data-counter="<?= (int)$stats['today'] ?>"><?= (int)$stats['today'] ?></h1>
            <small>Attendance records</small>
        </div>
        <div class="stat-icon cyan"><i class="fas fa-qrcode"></i></div>
    </a>
</div>

<!-- Quick Nav -->
<div class="admin-nav-grid">
    <a href="<?= BASE_URL ?>/admin/users" class="admin-nav-card">
        <div class="admin-nav-icon"><i class="fas fa-users"></i></div>
        Manage Users
    </a>
    <a href="<?= BASE_URL ?>/admin/courses" class="admin-nav-card">
        <div class="admin-nav-icon"><i class="fas fa-clipboard-list"></i></div>
        Manage Courses
    </a>
    <a href="<?= BASE_URL ?>/admin/departments" class="admin-nav-card">
        <div class="admin-nav-icon"><i class="fas fa-sitemap"></i></div>
        Departments
    </a>
    <a href="<?= BASE_URL ?>/admin/reports" class="admin-nav-card">
        <div class="admin-nav-icon"><i class="fas fa-chart-line"></i></div>
        Reports & Export
    </a>
</div>

<!-- Recent Sessions -->
<div class="panel">
    <div class="panel-header">
        <h2><i class="fas fa-history" style="color:var(--primary);margin-right:8px"></i>Recent Sessions</h2>
        <span class="badge badge-count"><?= count($recentSessions) ?></span>
    </div>
    <div class="table-wrap">
        <?php if (empty($recentSessions)): ?>
        <div class="empty-state">
            <div class="empty-icon">📋</div>
            <p>No sessions yet.</p>
        </div>
        <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Course</th><th>Lecturer</th><th>Date</th>
                    <th>Time</th><th>Attended</th><th>Status</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($recentSessions as $s): ?>
                <tr>
                    <td>
                        <strong><?= htmlspecialchars($s['course_code']) ?></strong>
                        <div style="font-size:.78rem;color:var(--text-muted)"><?= htmlspecialchars($s['course_name']) ?></div>
                    </td>
                    <td><?= htmlspecialchars($s['lecturer_name']) ?></td>
                    <td><?= htmlspecialchars($s['session_date']) ?></td>
                    <td><?= htmlspecialchars(substr($s['start_time'],0,5)) ?></td>
                    <td><strong><?= (int)$s['att_count'] ?></strong></td>
                    <td><span class="badge badge-<?= $s['status'] ?>"><?= $s['status'] ?></span></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>