<?php $pageTitle = htmlspecialchars($course['code']) . ' – Attendance History'; ?>

<div class="page-header">
    <div>
        <a href="/student/dashboard" class="back-link">← Back to Dashboard</a>
        <h1><?= htmlspecialchars($course['code']) ?>: <?= htmlspecialchars($course['name']) ?></h1>
        <p class="subtitle">Your full attendance history for this course</p>
    </div>
</div>

<?php
$total    = count($history);
$attended = count(array_filter($history, fn($r) => $r['status'] !== 'absent'));
$pct      = $total > 0 ? round($attended / $total * 100, 1) : 0;
$barClass = $pct >= 75 ? 'bar-good' : ($pct >= 50 ? 'bar-warn' : 'bar-bad');
?>

<!-- Summary strip -->
<div class="stat-strip">
    <div class="stat-box">
        <span class="stat-num"><?= $total ?></span>
        <span class="stat-label">Total Sessions</span>
    </div>
    <div class="stat-box">
        <span class="stat-num"><?= $attended ?></span>
        <span class="stat-label">Attended</span>
    </div>
    <div class="stat-box">
        <span class="stat-num"><?= $total - $attended ?></span>
        <span class="stat-label">Absent</span>
    </div>
    <div class="stat-box stat-pct">
        <span class="stat-num <?= $pct >= 75 ? 'text-success' : ($pct >= 50 ? 'text-warn' : 'text-danger') ?>">
            <?= $pct ?>%
        </span>
        <span class="stat-label">Attendance Rate</span>
    </div>
</div>

<!-- Full history table -->
<div class="card">
    <?php if (empty($history)): ?>
        <p class="empty">No sessions recorded yet for this course.</p>
    <?php else: ?>
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Date</th>
                <th>Session</th>
                <th>Start Time</th>
                <th>Scanned At</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($history as $i => $row): ?>
            <tr class="<?= $row['status'] === 'absent' ? 'row-absent' : '' ?>">
                <td><?= $i + 1 ?></td>
                <td><?= htmlspecialchars($row['session_date']) ?></td>
                <td><?= htmlspecialchars($row['title'] ?? 'Class Session') ?></td>
                <td><?= htmlspecialchars(substr($row['start_time'], 0, 5)) ?></td>
                <td>
                    <?= $row['scanned_at']
                        ? htmlspecialchars(date('H:i', strtotime($row['scanned_at'])))
                        : '–' ?>
                </td>
                <td><span class="badge badge-<?= $row['status'] ?>"><?= $row['status'] ?></span></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>
