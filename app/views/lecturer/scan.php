<?php $pageTitle = 'Scan Session – ' . htmlspecialchars($session['course_name']); ?>

<div class="scan-page">

    <div class="scan-header">
        <h1><?= htmlspecialchars($session['course_code']) ?>: <?= htmlspecialchars($session['course_name']) ?></h1>
        <p><?= htmlspecialchars($session['title'] ?? 'Class Session') ?>
           &nbsp;|&nbsp; <?= htmlspecialchars($session['session_date']) ?>
           &nbsp;|&nbsp; <?= htmlspecialchars($session['start_time']) ?> – <?= htmlspecialchars($session['end_time']) ?>
        </p>

        <span class="badge badge-<?= $session['status'] ?>">
            <?= strtoupper($session['status']) ?>
        </span>
    </div>

    <div class="scan-layout">

        <!-- QR Code Panel -->
        <div class="qr-panel">
            <h2>QR Code</h2>

            <?php if ($session['status'] === 'pending'): ?>
                <p class="info">Activate the session to start scanning.</p>
                <form method="POST" action="<?= BASE_URL ?>/lecturer/sessions/<?= $session['id'] ?>/activate">
                    <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">
                    <button type="submit" class="btn btn-primary btn-large">▶ Activate Session</button>
                </form>

            <?php elseif ($session['status'] === 'active'): ?>
                <?php if ($session['qr_image_path'] && file_exists(ROOT_PATH . '/public/' . $session['qr_image_path'])): ?>
                    <img src="<?= BASE_URL ?>/<?= htmlspecialchars($session['qr_image_path']) ?>"
                         alt="QR Code" class="qr-image" id="qrImage">
                <?php else: ?>
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=<?= urlencode($scanUrl) ?>"
                         alt="QR Code" class="qr-image" id="qrImage">
                <?php endif; ?>

                <p class="qr-url"><?= htmlspecialchars($scanUrl) ?></p>

                <div class="qr-actions">
                    <button onclick="toggleFullscreen()" class="btn btn-secondary">⛶ Fullscreen</button>
                    <form method="POST"
                          action="<?= BASE_URL ?>/lecturer/sessions/<?= $session['id'] ?>/close"
                          onsubmit="return confirm('Close this session?');"
                          style="display:inline">
                        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">
                        <button type="submit" class="btn btn-danger">■ Close Session</button>
                    </form>
                </div>

                <p class="expires-note">
                    QR expires: <?= htmlspecialchars($session['qr_expires_at']) ?>
                </p>

            <?php else: ?>
                <p class="info">This session is closed. No more scans accepted.</p>
            <?php endif; ?>
        </div>

        <!-- Live Attendance List -->
        <div class="attendance-panel">
            <h2>Attendance <span class="badge badge-count" id="attendanceCount"><?= count($attendance) ?></span></h2>

            <?php if ($session['status'] === 'active'): ?>
            <p class="live-indicator">🟢 Live – auto-refreshes every 10 seconds</p>
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
                            <th>Student No.</th>
                            <th>Time</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($attendance as $i => $row): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['student_number']) ?></td>
                            <td><?= htmlspecialchars(date('H:i', strtotime($row['scanned_at']))) ?></td>
                            <td><span class="badge badge-<?= $row['status'] ?>"><?= $row['status'] ?></span></td>
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
setInterval(() => {
    fetch('<?= BASE_URL ?>/api/attendance.php?session_id=<?= $session['id'] ?>')
        .then(r => r.json())
        .then(data => {
            document.getElementById('attendanceCount').textContent = data.count;
            let html = '';
            if (data.records.length === 0) {
                html = '<p class="empty">No students have scanned yet.</p>';
            } else {
                html = '<table class="table"><thead><tr><th>#</th><th>Name</th><th>Student No.</th><th>Time</th><th>Status</th></tr></thead><tbody>';
                data.records.forEach((row, i) => {
                    html += `<tr>
                        <td>${i+1}</td>
                        <td>${row.name}</td>
                        <td>${row.student_number}</td>
                        <td>${row.scanned_at}</td>
                        <td><span class="badge badge-${row.status}">${row.status}</span></td>
                    </tr>`;
                });
                html += '</tbody></table>';
            }
            document.getElementById('attendanceList').innerHTML = html;
        })
        .catch(() => {/* silent fail */});
}, 10000);

function toggleFullscreen() {
    const img = document.getElementById('qrImage');
    if (!document.fullscreenElement) {
        img.requestFullscreen();
    } else {
        document.exitFullscreen();
    }
}
</script>
<?php endif; ?>