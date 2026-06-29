<?php $pageTitle = 'Student Dashboard'; ?>

<div class="page-header">
    <div>
        <h1>Welcome, <?= htmlspecialchars($user['name']) ?> 👋</h1>
        <p class="subtitle">Your enrolled courses and attendance overview</p>
    </div>
</div>

<!-- ── Enrolled Courses ─────────────────────────────────────────────── -->
<section class="section">
    <h2 class="section-title">My Courses</h2>

    <?php if (empty($courses)): ?>
        <div class="empty-state">
            <div class="empty-icon">📚</div>
            <p>You are not enrolled in any courses yet.</p>
            <p class="text-muted">Contact your administrator to get enrolled.</p>
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
            <p class="course-lecturer">👤 <?= htmlspecialchars($c['lecturer_name']) ?></p>

            <!-- Attendance bar -->
            <?php
                $pct = (float)($c['pct'] ?? 0);
                $barClass = $pct >= 75 ? 'bar-good' : ($pct >= 50 ? 'bar-warn' : 'bar-bad');
            ?>
            <div class="att-bar-wrap">
                <div class="att-bar-label">
                    <span>Attendance</span>
                    <strong><?= $pct ?>%</strong>
                </div>
                <div class="att-bar-track">
                    <div class="att-bar-fill <?= $barClass ?>"
                         style="width: <?= min($pct, 100) ?>%"></div>
                </div>
                <p class="att-bar-note">
                    <?= (int)$c['attended'] ?> / <?= (int)$c['total_sessions'] ?> sessions attended
                </p>
            </div>

            <a href="/student/courses/<?= $c['id'] ?>" class="btn btn-secondary btn-sm">View History</a>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</section>

<!-- ── Recent Activity ──────────────────────────────────────────────── -->
<section class="section">
    <h2 class="section-title">Recent Activity</h2>

    <?php if (empty($recent)): ?>
        <p class="text-muted">No attendance records yet.</p>
    <?php else: ?>
    <div class="card">
        <table class="table">
            <thead>
                <tr>
                    <th>Course</th>
                    <th>Session</th>
                    <th>Date</th>
                    <th>Time Scanned</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($recent as $row): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($row['code']) ?></strong></td>
                    <td><?= htmlspecialchars($row['title'] ?? 'Class Session') ?></td>
                    <td><?= htmlspecialchars($row['session_date']) ?></td>
                    <td><?= htmlspecialchars(date('H:i', strtotime($row['scanned_at']))) ?></td>
                    <td><span class="badge badge-<?= $row['status'] ?>"><?= $row['status'] ?></span></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</section>
