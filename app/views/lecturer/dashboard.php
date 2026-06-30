<?php $pageTitle = 'Lecturer Dashboard'; ?>

<div class="page-header">
    <div>
        <h1>Welcome, <?= htmlspecialchars($user['name']) ?></h1>
        <p class="subtitle">Manage your sessions and track attendance</p>
    </div>
    <a href="<?= BASE_URL ?>/lecturer/sessions/create" class="btn btn-primary">+ New Session</a>
</div>

<!-- ── Course Stats ──────────────────────────────────────────────── -->
<?php if (!empty($courses)): ?>
<div class="stat-strip">
    <?php foreach ($courses as $c): ?>
    <div class="stat-box">
        <span class="stat-num"><?= (int)$c['enrolled_count'] ?></span>
        <span class="stat-label"><?= htmlspecialchars($c['code']) ?> Students</span>
    </div>
    <?php endforeach; ?>
    <div class="stat-box">
        <span class="stat-num"><?= count($sessions) ?></span>
        <span class="stat-label">Total Sessions</span>
    </div>
</div>
<?php endif; ?>

<!-- ── My Courses ───────────────────────────────────────────────── -->
<section class="section">
    <h2 class="section-title">My Courses</h2>
    <?php if (empty($courses)): ?>
        <div class="empty-state">
            <div class="empty-icon">📚</div>
            <p>No courses assigned yet.</p>
            <p class="text-muted">Contact the administrator to get courses assigned.</p>
        </div>
    <?php else: ?>
    <div class="course-grid">
        <?php foreach ($courses as $c): ?>
        <div class="course-card">
            <div class="course-card-header">
                <span class="course-code"><?= htmlspecialchars($c['code']) ?></span>
                <span class="course-semester">Sem <?= htmlspecialchars($c['semester'] ?? '–') ?></span>
            </div>
            <h3 class="course-name"><?= htmlspecialchars($c['name']) ?></h3>
            <p class="course-lecturer">👥 <?= (int)$c['enrolled_count'] ?> students enrolled</p>
            <a href="<?= BASE_URL ?>/lecturer/sessions/create" class="btn btn-primary btn-sm">
                + New Session
            </a>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</section>

<!-- ── Recent Sessions ──────────────────────────────────────────── -->
<section class="section">
    <h2 class="section-title">Recent Sessions</h2>
    <?php if (empty($sessions)): ?>
        <div class="empty-state">
            <div class="empty-icon">📋</div>
            <p>No sessions yet.</p>
            <a href="<?= BASE_URL ?>/lecturer/sessions/create" class="btn btn-primary">
                Create Your First Session
            </a>
        </div>
    <?php else: ?>
    <div class="card">
        <table class="table">
            <thead>
                <tr>
                    <th>Course</th>
                    <th>Title</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Attended</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($sessions as $s): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($s['course_code']) ?></strong></td>
                    <td><?= htmlspecialchars($s['title'] ?? 'Class Session') ?></td>
                    <td><?= htmlspecialchars($s['session_date']) ?></td>
                    <td><?= htmlspecialchars(substr($s['start_time'],0,5)) ?></td>
                    <td><?= (int)$s['attendance_count'] ?></td>
                    <td><span class="badge badge-<?= $s['status'] ?>"><?= $s['status'] ?></span></td>
                    <td>
                        <a href="<?= BASE_URL ?>/lecturer/sessions/<?= $s['id'] ?>/scan"
                           class="btn btn-sm btn-secondary">
                            <?= $s['status'] === 'active' ? '📡 Live' : 'View' ?>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</section>
