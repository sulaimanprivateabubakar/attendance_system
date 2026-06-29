<?php $pageTitle = 'Admin Dashboard'; ?>

<div class="page-header">
    <div>
        <h1>Admin Dashboard</h1>
        <p class="subtitle">System overview</p>
    </div>
    <div class="header-actions">
        <a href="<?= BASE_URL ?>/admin/users/create" class="btn btn-primary">+ Add User</a>
        <a href="<?= BASE_URL ?>/admin/courses/create" class="btn btn-secondary">+ Add Course</a>
    </div>
</div>

<div class="stat-strip">
    <div class="stat-box">
        <span class="stat-num"><?= (int)$stats['students'] ?></span>
        <span class="stat-label">Students</span>
    </div>
    <div class="stat-box">
        <span class="stat-num"><?= (int)$stats['lecturers'] ?></span>
        <span class="stat-label">Lecturers</span>
    </div>
    <div class="stat-box">
        <span class="stat-num"><?= (int)$stats['courses'] ?></span>
        <span class="stat-label">Active Courses</span>
    </div>
    <div class="stat-box">
        <span class="stat-num"><?= (int)$stats['sessions'] ?></span>
        <span class="stat-label">Total Sessions</span>
    </div>
    <div class="stat-box">
        <span class="stat-num text-success"><?= (int)$stats['today'] ?></span>
        <span class="stat-label">Scans Today</span>
    </div>
</div>

<div class="admin-nav-grid">
    <a href="<?= BASE_URL ?>/admin/users"       class="admin-nav-card">👥 Manage Users</a>
    <a href="<?= BASE_URL ?>/admin/courses"     class="admin-nav-card">📖 Manage Courses</a>
    <a href="<?= BASE_URL ?>/admin/departments" class="admin-nav-card">🏛 Departments</a>
    <a href="<?= BASE_URL ?>/admin/reports"     class="admin-nav-card">📊 Reports & Export</a>
</div>

<section class="section">
    <h2 class="section-title">Recent Sessions</h2>
    <div class="card">
        <?php if (empty($recentSessions)): ?>
            <p class="empty">No sessions yet.</p>
        <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Course</th>
                    <th>Lecturer</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Attended</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($recentSessions as $s): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($s['course_code']) ?></strong>
                        <?= htmlspecialchars($s['course_name']) ?></td>
                    <td><?= htmlspecialchars($s['lecturer_name']) ?></td>
                    <td><?= htmlspecialchars($s['session_date']) ?></td>
                    <td><?= htmlspecialchars(substr($s['start_time'],0,5)) ?></td>
                    <td><?= (int)$s['att_count'] ?></td>
                    <td><span class="badge badge-<?= $s['status'] ?>"><?= $s['status'] ?></span></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</section>