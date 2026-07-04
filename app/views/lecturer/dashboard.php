<?php $pageTitle = 'Dashboard'; ?>

<div class="page-title">
    <div>
        <h1>Dashboard</h1>
        <p>Welcome back, <?= htmlspecialchars($user['name']) ?></p>
    </div>
    <a href="<?= BASE_URL ?>/lecturer/sessions/create" class="btn btn-primary">
        <i class="fas fa-plus"></i> New Session
    </a>
</div>

<!-- Stats -->
<div class="stats">
    <?php foreach ($courses as $c): ?>
    <div class="stat-card">
        <div class="stat-info">
            <h3><?= htmlspecialchars($c['code']) ?></h3>
            <h1 data-counter="<?= (int)$c['enrolled_count'] ?>"><?= (int)$c['enrolled_count'] ?></h1>
            <small>Students enrolled</small>
        </div>
        <div class="stat-icon blue"><i class="fas fa-users"></i></div>
    </div>
    <?php endforeach; ?>
    <div class="stat-card">
        <div class="stat-info">
            <h3>Total Sessions</h3>
            <h1 data-counter="<?= count($sessions) ?>"><?= count($sessions) ?></h1>
            <small>All time</small>
        </div>
        <div class="stat-icon green"><i class="fas fa-calendar-check"></i></div>
    </div>
</div>

<!-- My Courses -->
<div class="section">
    <div class="section-title">My Courses</div>
    <?php if (empty($courses)): ?>
    <div class="empty-state">
        <div class="empty-icon">📚</div>
        <p>No courses assigned yet.</p>
        <p style="font-size:.8rem;color:var(--text-muted)">Contact the administrator to get courses assigned.</p>
    </div>
    <?php else: ?>
    <div class="course-grid">
        <?php foreach ($courses as $c): ?>
        <div class="course-card">
            <div class="course-card-header">
                <span class="course-code-badge"><?= htmlspecialchars($c['code']) ?></span>
                <span style="font-size:.75rem;color:var(--text-muted)">Sem <?= $c['semester'] ?? '–' ?></span>
            </div>
            <div class="course-name"><?= htmlspecialchars($c['name']) ?></div>
            <div class="course-lecturer"><i class="fas fa-users" style="margin-right:5px"></i><?= (int)$c['enrolled_count'] ?> students enrolled</div>
            <a href="<?= BASE_URL ?>/lecturer/sessions/create" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> New Session
            </a>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Recent Sessions -->
<div class="section">
    <div class="section-title">Recent Sessions</div>
    <?php if (empty($sessions)): ?>
    <div class="empty-state">
        <div class="empty-icon">📋</div>
        <p>No sessions created yet.</p>
        <a href="<?= BASE_URL ?>/lecturer/sessions/create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create First Session
        </a>
    </div>
    <?php else: ?>
    <div class="panel">
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Course</th><th>Title</th><th>Date</th>
                        <th>Time</th><th>Attended</th><th>Status</th><th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($sessions as $s): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($s['course_code']) ?></strong></td>
                        <td><?= htmlspecialchars($s['title'] ?? 'Class Session') ?></td>
                        <td><?= htmlspecialchars($s['session_date']) ?></td>
                        <td><?= htmlspecialchars(substr($s['start_time'],0,5)) ?></td>
                        <td><strong><?= (int)$s['attendance_count'] ?></strong></td>
                        <td><span class="badge badge-<?= $s['status'] ?>"><?= $s['status'] ?></span></td>
                        <td>
                            <a href="<?= BASE_URL ?>/lecturer/sessions/<?= $s['id'] ?>/scan"
                               class="btn btn-sm <?= $s['status'] === 'active' ? 'btn-success' : 'btn-secondary' ?>">
                                <?= $s['status'] === 'active' ? '<i class="fas fa-signal"></i> Live' : '<i class="fas fa-eye"></i> View' ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>