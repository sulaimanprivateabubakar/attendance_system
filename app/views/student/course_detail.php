<?php $pageTitle = htmlspecialchars($course['code']) . ' — History'; ?>

<div class="page-title">
    <div>
        <a href="<?= BASE_URL ?>/student/dashboard" class="back-link">
            <i class="fas fa-arrow-left"></i> Dashboard
        </a>
        <h1><?= htmlspecialchars($course['code']) ?>: <?= htmlspecialchars($course['name']) ?></h1>
        <p>
            <i class="fas fa-chalkboard-teacher" style="margin-right:4px"></i>
            <?= htmlspecialchars($course['lecturer_name']) ?>
            &nbsp;·&nbsp;
            <?= htmlspecialchars($course['dept_name'] ?? '') ?>
        </p>
    </div>
    <a href="<?= BASE_URL ?>/student/scan" class="btn btn-primary">
        <i class="fas fa-qrcode"></i> Scan QR
    </a>
</div>

<!-- Stats -->
<div class="stat-strip" style="margin-bottom:22px">
    <div class="stat-box">
        <span class="stat-num"><?= $total ?></span>
        <span class="stat-label">Total Sessions</span>
    </div>
    <div class="stat-box">
        <span class="stat-num text-success"><?= $attended ?></span>
        <span class="stat-label">Attended</span>
    </div>
    <div class="stat-box">
        <span class="stat-num text-danger"><?= $total - $attended ?></span>
        <span class="stat-label">Missed</span>
    </div>
    <div class="stat-box">
        <span class="stat-num <?= $pct >= 75 ? 'text-success' : ($pct >= 50 ? 'text-warn' : 'text-danger') ?>">
            <?= $pct ?>%
        </span>
        <span class="stat-label">Attendance Rate</span>
    </div>
</div>

<!-- Progress bar -->
<div class="panel" style="margin-bottom:22px;padding:20px 24px">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px">
        <span style="font-weight:600;color:var(--text)">Overall Attendance</span>
        <span style="font-size:1.2rem;font-weight:700;
                     color:<?= $pct >= 75 ? 'var(--success)' : ($pct >= 50 ? 'var(--warning)' : 'var(--danger)') ?>">
            <?= $pct ?>%
        </span>
    </div>
    <div class="progress" style="height:12px">
        <div class="progress-bar <?= $pct >= 75 ? 'success' : ($pct >= 50 ? 'warning' : 'danger') ?>"
             style="width:<?= min($pct,100) ?>%"></div>
    </div>
    <?php if ($pct < 75): ?>
    <p style="font-size:.8rem;color:var(--warning);margin-top:8px">
        <i class="fas fa-exclamation-triangle" style="margin-right:4px"></i>
        You need at least 75% attendance. You need to attend
        <?= max(0, ceil($total * 0.75) - $attended) ?> more session(s) to reach 75%.
    </p>
    <?php else: ?>
    <p style="font-size:.8rem;color:var(--success);margin-top:8px">
        <i class="fas fa-check-circle" style="margin-right:4px"></i>
        Great! You are above the 75% minimum attendance requirement.
    </p>
    <?php endif; ?>
</div>

<!-- Session History -->
<div class="panel">
    <div class="panel-header">
        <h2>
            <i class="fas fa-calendar-check" style="color:var(--primary);margin-right:8px"></i>
            Session History
        </h2>
        <span class="badge badge-count"><?= $total ?></span>
    </div>
    <div class="table-wrap">
        <?php if (empty($history)): ?>
        <div class="empty-state">
            <div class="empty-icon">📋</div>
            <p>No closed sessions yet for this course.</p>
        </div>
        <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Session</th>
                    <th>Time</th>
                    <th>Scanned At</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($history as $i => $row): ?>
                <tr class="<?= $row['att_status'] === 'absent' ? 'row-absent' : '' ?>">
                    <td style="color:var(--text-muted)"><?= $i + 1 ?></td>
                    <td><?= htmlspecialchars($row['session_date']) ?></td>
                    <td><?= htmlspecialchars($row['title'] ?? 'Class Session') ?></td>
                    <td style="color:var(--text-muted)">
                        <?= substr($row['start_time'],0,5) ?> – <?= substr($row['end_time'],0,5) ?>
                    </td>
                    <td>
                        <?php if ($row['scanned_at']): ?>
                        <strong><?= date('H:i', strtotime($row['scanned_at'])) ?></strong>
                        <?php else: ?>
                        <span style="color:var(--text-muted)">—</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="badge badge-<?= $row['att_status'] ?>">
                            <?= ucfirst($row['att_status']) ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>