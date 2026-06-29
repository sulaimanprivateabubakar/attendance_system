<?php $pageTitle = 'My Sessions'; ?>

<div class="page-header">
    <div>
        <h1>Sessions</h1>
        <p class="subtitle">All your class sessions</p>
    </div>
    <a href="<?= BASE_URL ?>/lecturer/sessions/create" class="btn btn-primary">+ New Session</a>
</div>

<?php if (empty($sessions)): ?>
    <div class="empty-state">
        <div class="empty-icon">📋</div>
        <p>No sessions created yet.</p>
        <a href="<?= BASE_URL ?>/lecturer/sessions/create" class="btn btn-primary">
            Create First Session
        </a>
    </div>
<?php else: ?>

<!-- Active sessions first -->
<?php
$active  = array_filter($sessions, fn($s) => $s['status'] === 'active');
$pending = array_filter($sessions, fn($s) => $s['status'] === 'pending');
$closed  = array_filter($sessions, fn($s) => $s['status'] === 'closed');
?>

<?php if (!empty($active)): ?>
<div class="alert alert-success">
    📡 You have <?= count($active) ?> active session(s) currently running.
</div>
<?php endif; ?>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Course</th>
                <th>Title</th>
                <th>Date</th>
                <th>Start</th>
                <th>End</th>
                <th>Attended</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($sessions as $s): ?>
            <tr>
                <td>
                    <strong><?= htmlspecialchars($s['course_code']) ?></strong><br>
                    <small class="text-muted"><?= htmlspecialchars($s['course_name']) ?></small>
                </td>
                <td><?= htmlspecialchars($s['title'] ?? 'Class Session') ?></td>
                <td><?= htmlspecialchars($s['session_date']) ?></td>
                <td><?= htmlspecialchars(substr($s['start_time'],0,5)) ?></td>
                <td><?= htmlspecialchars(substr($s['end_time'],0,5)) ?></td>
                <td>
                    <strong><?= (int)$s['attendance_count'] ?></strong>
                </td>
                <td>
                    <span class="badge badge-<?= $s['status'] ?>">
                        <?= strtoupper($s['status']) ?>
                    </span>
                </td>
                <td>
                    <a href="<?= BASE_URL ?>/lecturer/sessions/<?= $s['id'] ?>/scan"
                       class="btn btn-sm <?= $s['status'] === 'active' ? 'btn-primary' : 'btn-secondary' ?>">
                        <?php if ($s['status'] === 'active'): ?>
                            📡 Live QR
                        <?php elseif ($s['status'] === 'pending'): ?>
                            ▶ Activate
                        <?php else: ?>
                            👁 View
                        <?php endif; ?>
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Summary strip -->
<div class="stat-strip" style="margin-top:1.5rem">
    <div class="stat-box">
        <span class="stat-num"><?= count($sessions) ?></span>
        <span class="stat-label">Total Sessions</span>
    </div>
    <div class="stat-box">
        <span class="stat-num text-success"><?= count($active) ?></span>
        <span class="stat-label">Active Now</span>
    </div>
    <div class="stat-box">
        <span class="stat-num"><?= count($pending) ?></span>
        <span class="stat-label">Pending</span>
    </div>
    <div class="stat-box">
        <span class="stat-num"><?= count($closed) ?></span>
        <span class="stat-label">Closed</span>
    </div>
</div>

<?php endif; ?>
