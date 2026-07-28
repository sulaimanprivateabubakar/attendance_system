<?php $pageTitle = 'Dashboard'; ?>

<div class="page-title">
    <div>
        <h1>Dashboard</h1>
        <p>Welcome back, <?= htmlspecialchars($user['name']) ?> </p>
    </div>
    <a href="<?= BASE_URL ?>/student/scan" class="btn btn-primary">
        <i class="fas fa-qrcode"></i> Scan QR Code
    </a>
</div>

<!-- ── Class Rep Pending ────────────────────────────────────── -->
<?php if (!empty($pendingConfirmations)): ?>
<div class="alert alert-warning" style="display:flex;align-items:center;gap:12px">
    <i class="fas fa-user-shield" style="font-size:1.2rem"></i>
    <div>
        <strong><?= count($pendingConfirmations) ?> manual attendance request(s) need your confirmation as Class Rep.</strong>
        <a href="<?= BASE_URL ?>/student/rep-dashboard"
           style="margin-left:12px;color:var(--warning);font-weight:700;text-decoration:underline">
            Review Now →
        </a>
    </div>
</div>
<?php endif; ?>

<!-- ── Live / Pending Sessions TODAY ────────────────────────── -->
<?php if (!empty($liveSessions)): ?>
<div class="panel" style="margin-bottom:22px;border-left:4px solid var(--success)">
    <div class="panel-header">
        <h2>
            <i class="fas fa-broadcast-tower" style="color:var(--success);margin-right:8px"></i>
            Today's Sessions
        </h2>
        <span class="badge" style="background:rgba(34,197,94,.15);color:var(--success)">
            <?= count($liveSessions) ?> session(s)
        </span>
    </div>
    <div style="padding:16px 24px">
        <?php foreach ($liveSessions as $sess): ?>
        <div style="display:flex;justify-content:space-between;align-items:center;
                    padding:16px 18px;border-radius:14px;margin-bottom:12px;
                    background:<?= $sess['status'] === 'active' ? 'rgba(34,197,94,.06)' : 'rgba(255,255,255,.03)' ?>;
                    border:1px solid <?= $sess['status'] === 'active' ? 'rgba(34,197,94,.25)' : 'rgba(255,255,255,.06)' ?>">
            <div>
                <!-- Status indicator -->
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px">
                    <?php if ($sess['status'] === 'active'): ?>
                    <span style="display:flex;align-items:center;gap:6px;
                                 background:rgba(34,197,94,.15);color:var(--success);
                                 padding:4px 10px;border-radius:20px;font-size:.75rem;font-weight:700">
                        <span style="width:6px;height:6px;border-radius:50%;
                                     background:var(--success);animation:pulse 1.5s infinite"></span>
                        LIVE NOW
                    </span>
                    <?php else: ?>
                    <span style="background:rgba(245,158,11,.12);color:var(--warning);
                                 padding:4px 10px;border-radius:20px;font-size:.75rem;font-weight:700">
                        ⏳ PENDING
                    </span>
                    <?php endif; ?>
                    <strong style="color:var(--text)">
                        <?= htmlspecialchars($sess['course_code']) ?>:
                        <?= htmlspecialchars($sess['course_name']) ?>
                    </strong>
                </div>
                <div style="font-size:.78rem;color:var(--text-muted);padding-left:4px">
                    <i class="fas fa-chalkboard-teacher" style="margin-right:4px"></i>
                    <?= htmlspecialchars($sess['lecturer_name']) ?>
                    &nbsp;·&nbsp;
                    <i class="fas fa-clock" style="margin-right:4px"></i>
                    <?= substr($sess['start_time'],0,5) ?> – <?= substr($sess['end_time'],0,5) ?>
                    <?php if ($sess['title']): ?>
                    &nbsp;·&nbsp; <?= htmlspecialchars($sess['title']) ?>
                    <?php endif; ?>
                </div>
            </div>
            <div style="flex-shrink:0;margin-left:16px">
                <?php if ($sess['already_attended']): ?>
                <span class="badge badge-active" style="padding:8px 14px;font-size:.8rem">
                    <i class="fas fa-check"></i> Attended
                </span>
                <?php elseif ($sess['status'] === 'active'): ?>
                <a href="<?= BASE_URL ?>/student/scan" class="btn btn-success">
                    <i class="fas fa-qrcode"></i> Scan Now
                </a>
                <?php else: ?>
                <span class="badge badge-pending" style="padding:8px 14px;font-size:.8rem">
                    Not started yet
                </span>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- ── Stats (clickable) ─────────────────────────────────────── -->
<?php
$totalAtt  = array_sum(array_column($courses, 'attended'));
$totalSess = array_sum(array_column($courses, 'total_sessions'));
$avgPct    = $totalSess > 0 ? round($totalAtt / $totalSess * 100, 1) : 0;
?>

<div class="stats" style="margin-bottom:22px">
    <a href="<?= BASE_URL ?>/student/dashboard" class="stat-card"
       style="text-decoration:none;cursor:pointer">
        <div class="stat-info">
            <h3>Courses</h3>
            <h1 data-counter="<?= count($courses) ?>"><?= count($courses) ?></h1>
            <small>Enrolled</small>
        </div>
        <div class="stat-icon blue"><i class="fas fa-book"></i></div>
    </a>
    <a href="<?= BASE_URL ?>/student/attended" class="stat-card"
       style="text-decoration:none;cursor:pointer">
        <div class="stat-info">
            <h3>Attended</h3>
            <h1 data-counter="<?= $totalAtt ?>"><?= $totalAtt ?></h1>
            <small>Sessions attended</small>
        </div>
        <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
    </a>
    <a href="<?= BASE_URL ?>/student/attended" class="stat-card"
       style="text-decoration:none;cursor:pointer">
        <div class="stat-info">
            <h3>Avg Rate</h3>
            <h1 style="font-size:1.6rem"><?= $avgPct ?>%</h1>
            <small><?= $avgPct >= 75 ? '✅ Good standing' : ($avgPct >= 50 ? '⚠️ Needs improvement' : '❌ Critical') ?></small>
        </div>
        <div class="stat-icon <?= $avgPct >= 75 ? 'green' : ($avgPct >= 50 ? 'amber' : 'red') ?>">
            <i class="fas fa-chart-line"></i>
        </div>
    </a>
    <a href="<?= BASE_URL ?>/student/attended" class="stat-card"
       style="text-decoration:none;cursor:pointer">
        <div class="stat-info">
            <h3>Recent Scans</h3>
            <h1 data-counter="<?= count($recent) ?>"><?= count($recent) ?></h1>
            <small>Last 10 records</small>
        </div>
        <div class="stat-icon cyan"><i class="fas fa-qrcode"></i></div>
    </a>
</div>

<!-- ── Enrolled Courses (clickable) ─────────────────────────── -->
<div class="section">
    <div class="section-title">My Courses</div>
    <?php if (empty($courses)): ?>
    <div class="empty-state">
        <div class="empty-icon">📚</div>
        <p>You are not enrolled in any courses yet.</p>
    </div>
    <?php else: ?>
    <div class="course-grid">
        <?php foreach ($courses as $c): ?>
        <?php
        $pct      = (float)($c['pct'] ?? 0);
        $barClass = $pct >= 75 ? 'success' : ($pct >= 50 ? 'warning' : 'danger');
        ?>
        <a href="<?= BASE_URL ?>/student/courses/<?= $c['id'] ?>"
           style="text-decoration:none;display:block" class="course-card">
            <div class="course-card-header">
                <span class="course-code-badge"><?= htmlspecialchars($c['code']) ?></span>
                <span style="font-size:.72rem;color:var(--text-muted)">
                    Sem <?= $c['semester'] ?? '–' ?>
                </span>
            </div>
            <div class="course-name"><?= htmlspecialchars($c['name']) ?></div>
            <div class="course-lecturer">
                <i class="fas fa-chalkboard-teacher" style="margin-right:5px"></i>
                <?= htmlspecialchars($c['lecturer_name']) ?>
            </div>
            <div class="att-bar-wrap">
                <div class="att-bar-label">
                    <span>Attendance</span>
                    <strong><?= $pct ?>%</strong>
                </div>
                <div class="progress">
                    <div class="progress-bar <?= $barClass ?>"
                         style="width:<?= min($pct,100) ?>%"></div>
                </div>
                <div class="att-bar-note">
                    <?= (int)$c['attended'] ?> / <?= (int)$c['total_sessions'] ?> sessions
                </div>
            </div>
            <div class="btn btn-secondary btn-sm" style="width:100%;justify-content:center">
                <i class="fas fa-history"></i> View History
            </div>
        </a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<!-- ── Recent Activity (clickable) ──────────────────────────── -->
<div class="section">
    <div class="section-title">
        Recent Activity
        <a href="<?= BASE_URL ?>/student/attended"
           style="margin-left:auto;font-size:.78rem;color:var(--primary-light);
                  font-weight:500;letter-spacing:0;text-transform:none">
            View all →
        </a>
    </div>
    <?php if (empty($recent)): ?>
    <div class="empty-state">
        <div class="empty-icon">📋</div>
        <p>No attendance records yet.</p>
        <a href="<?= BASE_URL ?>/student/scan" class="btn btn-primary">
            <i class="fas fa-qrcode"></i> Scan Your First QR
        </a>
    </div>
    <?php else: ?>
    <div class="panel">
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Course</th><th>Session</th><th>Date</th>
                        <th>Time</th><th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($recent as $row): ?>
                    <tr onclick="window.location='<?= BASE_URL ?>/student/courses/<?= $row['id'] ?? '' ?>'"
                        style="cursor:pointer">
                        <td><strong><?= htmlspecialchars($row['code']) ?></strong></td>
                        <td><?= htmlspecialchars($row['title'] ?? 'Class Session') ?></td>
                        <td><?= htmlspecialchars($row['session_date']) ?></td>
                        <td><?= date('H:i', strtotime($row['scanned_at'])) ?></td>
                        <td><span class="badge badge-<?= $row['status'] ?>"><?= $row['status'] ?></span></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>