<?php $pageTitle = 'Dashboard'; ?>

<div class="page-title">
    <div>
        <h1>My Dashboard</h1>
        <p>Welcome back, <?= htmlspecialchars($user['name']) ?></p>
    </div>
</div>

<!-- Stats -->
<div class="stat-strip">
    <div class="stat-box">
        <span class="stat-num"><?= count($courses) ?></span>
        <span class="stat-label">Courses</span>
    </div>
    <?php
    $totalAtt  = array_sum(array_column($courses, 'attended'));
    $totalSess = array_sum(array_column($courses, 'total_sessions'));
    $avgPct    = $totalSess > 0 ? round($totalAtt / $totalSess * 100, 1) : 0;
    ?>
    <div class="stat-box">
        <span class="stat-num text-success"><?= $totalAtt ?></span>
        <span class="stat-label">Sessions Attended</span>
    </div>
    <div class="stat-box">
        <span class="stat-num <?= $avgPct >= 75 ? 'text-success' : ($avgPct >= 50 ? 'text-warn' : 'text-danger') ?>">
            <?= $avgPct ?>%
        </span>
        <span class="stat-label">Avg Attendance</span>
    </div>
    <div class="stat-box">
        <span class="stat-num"><?= count($recent) ?></span>
        <span class="stat-label">Recent Scans</span>
    </div>
</div>

<!-- Enrolled Courses -->
<div class="section">
    <div class="section-title">My Courses</div>
    <?php if (empty($courses)): ?>
    <div class="empty-state">
        <div class="empty-icon">📚</div>
        <p>You are not enrolled in any courses yet.</p>
        <p style="font-size:.8rem;color:var(--text-muted)">Contact your administrator to get enrolled.</p>
    </div>
    <?php else: ?>
    <div class="course-grid">
        <?php foreach ($courses as $c): ?>
        <?php
        $pct = (float)($c['pct'] ?? 0);
        $barClass = $pct >= 75 ? 'success' : ($pct >= 50 ? 'warning' : 'danger');
        ?>
        <div class="course-card">
            <div class="course-card-header">
                <span class="course-code-badge"><?= htmlspecialchars($c['code']) ?></span>
                <span style="font-size:.72rem;color:var(--text-muted)">Sem <?= $c['semester'] ?? '–' ?></span>
            </div>
            <div class="course-name"><?= htmlspecialchars($c['name']) ?></div>
            <div class="course-lecturer">
                <i class="fas fa-chalkboard-teacher" style="margin-right:5px;color:var(--text-muted)"></i>
                <?= htmlspecialchars($c['lecturer_name']) ?>
            </div>

            <div class="att-bar-wrap">
                <div class="att-bar-label">
                    <span>Attendance</span>
                    <strong><?= $pct ?>%</strong>
                </div>
                <div class="progress">
                    <div class="progress-bar <?= $barClass ?>" style="width:<?= min($pct,100) ?>%"></div>
                </div>
                <div class="att-bar-note">
                    <?= (int)$c['attended'] ?> / <?= (int)$c['total_sessions'] ?> sessions attended
                </div>
            </div>

            <a href="<?= BASE_URL ?>/student/courses/<?= $c['id'] ?>" class="btn btn-secondary btn-sm">
                <i class="fas fa-history"></i> View History
            </a>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Recent Activity -->
<div class="section">
    <div class="section-title">Recent Activity</div>
    <?php if (empty($recent)): ?>
    <div class="empty-state">
        <div class="empty-icon">🔍</div>
        <p>No attendance records yet.</p>
    </div>
    <?php else: ?>
    <div class="panel">
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Course</th><th>Session</th><th>Date</th>
                        <th>Time Scanned</th><th>Status</th>
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
    </div>
    <?php endif; ?>
</div>