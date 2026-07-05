<?php $pageTitle = htmlspecialchars($session['course_name']) . ' — Scan'; ?>

<style>
/* Force scan layout to always be side by side */
.scan-layout {
    display: grid !important;
    grid-template-columns: 360px 1fr !important;
    gap: 22px !important;
    align-items: start !important;
    width: 100% !important;
}
@media (max-width: 900px) {
    .scan-layout { grid-template-columns: 1fr !important; }
}
</style>

<div class="page-title">
    <div>
        <a href="<?= BASE_URL ?>/lecturer/sessions" class="back-link">
            <i class="fas fa-arrow-left"></i> Sessions
        </a>
        <h1><?= htmlspecialchars($session['course_code']) ?>: <?= htmlspecialchars($session['course_name']) ?></h1>
        <p>
            <?= htmlspecialchars($session['title'] ?? 'Class Session') ?>
            &nbsp;·&nbsp; <?= htmlspecialchars($session['session_date']) ?>
            &nbsp;·&nbsp; <?= htmlspecialchars(substr($session['start_time'],0,5)) ?> – <?= htmlspecialchars(substr($session['end_time'],0,5)) ?>
        </p>
        <div style="margin-top:.5rem">
            <span class="badge badge-<?= $session['status'] ?>"><?= strtoupper($session['status']) ?></span>
        </div>
    </div>
    <?php if ($session['status'] === 'active'): ?>
    <a href="<?= BASE_URL ?>/lecturer/sessions/<?= $session['id'] ?>/manual"
       class="btn btn-secondary">
        <i class="fas fa-keyboard"></i> Manual Entry
    </a>
    <?php endif; ?>
</div>

<div class="scan-layout">

    <!-- ── QR Code Panel ────────────────────────────── -->
    <div class="qr-panel">
        <div class="qr-panel-header">
            <span>
                <i class="fas fa-qrcode" style="margin-right:8px;color:var(--primary)"></i>
                QR Code
            </span>
            <?php if ($session['status'] === 'active'): ?>
            <div class="live-indicator" style="margin:0">
                <span class="live-dot"></span> Live
            </div>
            <?php endif; ?>
        </div>

        <div class="qr-panel-body">

            <?php if ($session['status'] === 'pending'): ?>
                <div class="empty-state">
                    <div class="empty-icon">📋</div>
                    <p>Activate the session to display the QR code.</p>
                    <form method="POST"
                          action="<?= BASE_URL ?>/lecturer/sessions/<?= $session['id'] ?>/activate">
                        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-play"></i> Activate Session
                        </button>
                    </form>
                </div>

            <?php elseif ($session['status'] === 'active'): ?>
                <div class="qr-image-wrap">
                    <?php if ($session['qr_image_path'] && file_exists(ROOT_PATH . '/public/' . $session['qr_image_path'])): ?>
                        <img src="<?= BASE_URL ?>/<?= htmlspecialchars($session['qr_image_path']) ?>"
                             alt="QR Code" class="qr-image" id="qrImage">
                    <?php else: ?>
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=260x260&margin=10&data=<?= urlencode($scanUrl) ?>"
                             alt="QR Code" class="qr-image" id="qrImage">
                    <?php endif; ?>
                </div>

                <div class="qr-url"><?= htmlspecialchars($scanUrl) ?></div>

                <div class="qr-actions">
                    <button onclick="toggleFullscreen()" class="btn btn-secondary btn-sm">
                        <i class="fas fa-expand"></i> Fullscreen
                    </button>
                    <a href="<?= BASE_URL ?>/lecturer/sessions/<?= $session['id'] ?>/manual"
                       class="btn btn-secondary btn-sm">
                        <i class="fas fa-keyboard"></i> Manual
                    </a>
                    <form method="POST"
                          action="<?= BASE_URL ?>/lecturer/sessions/<?= $session['id'] ?>/close"
                          style="display:inline"
                          onsubmit="return confirm('Close this session?')">
                        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">
                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="fas fa-stop"></i> Close
                        </button>
                    </form>
                </div>

                <p class="expires-note" style="margin-top:12px">
                    <i class="fas fa-clock" style="margin-right:4px"></i>
                    Expires: <?= htmlspecialchars($session['qr_expires_at']) ?>
                </p>

            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-icon">🔒</div>
                    <p>This session is closed.<br>No more scans accepted.</p>
                </div>
            <?php endif; ?>

        </div>
    </div>

    <!-- ── Attendance Panel ──────────────────────────── -->
    <div class="attendance-panel">
        <div class="attendance-panel-header">
            <span>
                <i class="fas fa-users" style="margin-right:8px;color:var(--primary)"></i>
                Attendance
            </span>
            <span class="badge badge-count" id="attendanceCount">
                <?= count($attendance) ?>
            </span>
        </div>

        <div class="attendance-panel-body">

            <?php if ($session['status'] === 'active'): ?>
            <div class="live-indicator">
                <span class="live-dot"></span>
                Auto-refreshes every 10 seconds
            </div>
            <?php endif; ?>

            <div id="attendanceList">
                <?php if (empty($attendance)): ?>
                    <p class="empty">No students have scanned yet.</p>
                <?php else: ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Reg No.</th>
                            <th>Time</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($attendance as $i => $row): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><strong><?= htmlspecialchars($row['name']) ?></strong></td>
                            <td>
                                <code style="font-size:.75rem;color:var(--text-muted)">
                                    <?= htmlspecialchars($row['student_number']) ?>
                                </code>
                            </td>
                            <td><?= date('H:i', strtotime($row['scanned_at'])) ?></td>
                            <td>
                                <span class="badge badge-<?= $row['status'] ?>">
                                    <?= $row['status'] ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>

        </div>
    </div>

</div>

<?php if ($session['status'] === 'active'): ?>
<script>
startAttendancePolling(<?= $session['id'] ?>, '<?= BASE_URL ?>', 10000);
</script>
<?php endif; ?>