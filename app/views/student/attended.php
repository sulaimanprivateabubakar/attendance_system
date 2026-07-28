<?php $pageTitle = 'Attendance History'; ?>

<div class="page-title">
    <div>
        <a href="<?= BASE_URL ?>/student/dashboard" class="back-link">
            <i class="fas fa-arrow-left"></i> Dashboard
        </a>
        <h1>My Attendance History</h1>
        <p>Complete record of all sessions attended</p>
    </div>
    <a href="<?= BASE_URL ?>/student/scan" class="btn btn-primary">
        <i class="fas fa-qrcode"></i> Scan QR
    </a>
</div>

<!-- Stats -->
<?php
$present = count(array_filter($records, fn($r) => $r['status'] === 'present'));
$late    = count(array_filter($records, fn($r) => $r['status'] === 'late'));
$total   = count($records);
?>
<div class="stat-strip" style="margin-bottom:22px">
    <div class="stat-box">
        <span class="stat-num"><?= $total ?></span>
        <span class="stat-label">Total Scans</span>
    </div>
    <div class="stat-box">
        <span class="stat-num text-success"><?= $present ?></span>
        <span class="stat-label">Present</span>
    </div>
    <div class="stat-box">
        <span class="stat-num text-warn"><?= $late ?></span>
        <span class="stat-label">Late</span>
    </div>
    <div class="stat-box">
        <span class="stat-num"><?= $total > 0 ? round($present/$total*100) : 0 ?>%</span>
        <span class="stat-label">On-Time Rate</span>
    </div>
</div>

<div class="panel">
    <div class="panel-header">
        <h2>
            <i class="fas fa-history" style="color:var(--primary);margin-right:8px"></i>
            All Attendance Records
        </h2>
        <span class="badge badge-count"><?= $total ?></span>
    </div>
    <?php if (empty($records)): ?>
    <div class="empty-state">
        <div class="empty-icon">📋</div>
        <p>No attendance records yet.</p>
        <a href="<?= BASE_URL ?>/student/scan" class="btn btn-primary">
            <i class="fas fa-qrcode"></i> Scan Your First QR
        </a>
    </div>
    <?php else: ?>
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Course</th>
                    <th>Session</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Scanned At</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($records as $i => $r): ?>
                <tr>
                    <td style="color:var(--text-muted)"><?= $i + 1 ?></td>
                    <td>
                        <strong><?= htmlspecialchars($r['course_code']) ?></strong>
                        <div style="font-size:.75rem;color:var(--text-muted)">
                            <?= htmlspecialchars($r['course_name']) ?>
                        </div>
                    </td>
                    <td><?= htmlspecialchars($r['title'] ?? 'Class Session') ?></td>
                    <td><?= htmlspecialchars($r['session_date']) ?></td>
                    <td style="color:var(--text-muted)">
                        <?= substr($r['start_time'],0,5) ?>
                    </td>
                    <td>
                        <strong><?= date('H:i', strtotime($r['scanned_at'])) ?></strong>
                        <div style="font-size:.72rem;color:var(--text-muted)">
                            <?= date('M j, Y', strtotime($r['scanned_at'])) ?>
                        </div>
                    </td>
                    <td>
                        <span class="badge badge-<?= $r['status'] ?>">
                            <?= ucfirst($r['status']) ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>