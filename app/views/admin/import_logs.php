<?php $pageTitle = 'Import Logs'; ?>

<div class="page-title">
    <div>
        <a href="<?= BASE_URL ?>/admin/import" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Import
        </a>
        <h1>Import Logs</h1>
        <p>History of all bulk imports</p>
    </div>
</div>

<div class="panel">
    <div class="panel-header">
        <h2><i class="fas fa-history" style="color:var(--primary);margin-right:8px"></i>All Import History</h2>
        <span class="badge badge-count"><?= count($logs) ?></span>
    </div>
    <div class="table-wrap">
        <?php if (empty($logs)): ?>
        <div class="empty-state">
            <div class="empty-icon">📂</div>
            <p>No imports yet.</p>
        </div>
        <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Type</th>
                    <th>File</th>
                    <th>Total Rows</th>
                    <th>✅ Success</th>
                    <th>❌ Failed</th>
                    <th>Success Rate</th>
                    <th>Imported By</th>
                    <th>Date & Time</th>
                    <th>Errors</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($logs as $i => $log): ?>
                <?php
                $rate = $log['total_rows'] > 0
                    ? round($log['success'] / $log['total_rows'] * 100)
                    : 0;
                ?>
                <tr>
                    <td style="color:var(--text-muted)"><?= $i + 1 ?></td>
                    <td>
                        <span class="badge <?= $log['type'] === 'students' ? 'badge-student' : ($log['type'] === 'courses' ? 'badge-lecturer' : 'badge-pending') ?>">
                            <?= ucfirst($log['type']) ?>
                        </span>
                    </td>
                    <td style="font-size:.78rem;color:var(--text-muted);max-width:180px;
                               overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                        <?= htmlspecialchars($log['filename'] ?? '—') ?>
                    </td>
                    <td><?= (int)$log['total_rows'] ?></td>
                    <td><strong style="color:var(--success)"><?= (int)$log['success'] ?></strong></td>
                    <td>
                        <?php if ($log['failed'] > 0): ?>
                        <strong style="color:var(--danger)"><?= (int)$log['failed'] ?></strong>
                        <?php else: ?>
                        <span style="color:var(--text-muted)">0</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div style="display:flex;align-items:center;gap:8px">
                            <div style="flex:1;background:rgba(255,255,255,.06);
                                        border-radius:99px;height:6px;width:80px">
                                <div style="height:6px;border-radius:99px;width:<?= $rate ?>%;
                                            background:<?= $rate >= 90 ? 'var(--success)' : ($rate >= 50 ? 'var(--warning)' : 'var(--danger)') ?>">
                                </div>
                            </div>
                            <span style="font-size:.78rem;color:var(--text-muted)"><?= $rate ?>%</span>
                        </div>
                    </td>
                    <td><?= htmlspecialchars($log['imported_by']) ?></td>
                    <td style="font-size:.78rem;color:var(--text-muted);white-space:nowrap">
                        <?= date('M j, Y H:i', strtotime($log['created_at'])) ?>
                    </td>
                    <td>
                        <?php if ($log['errors']): ?>
                        <button class="btn btn-sm btn-secondary"
                                onclick="showErrors(<?= htmlspecialchars(json_encode($log['errors'])) ?>)">
                            <i class="fas fa-exclamation-triangle" style="color:var(--warning)"></i>
                            View Errors
                        </button>
                        <?php else: ?>
                        <span style="color:var(--success);font-size:.8rem">
                            <i class="fas fa-check"></i> Clean
                        </span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<!-- Error Modal -->
<div id="errorModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.7);
                             z-index:99999;align-items:center;justify-content:center">
    <div style="background:var(--card);border-radius:16px;padding:24px;
                max-width:600px;width:90%;max-height:80vh;overflow-y:auto;
                border:1px solid var(--border)">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
            <h3 style="color:var(--text)">Import Errors</h3>
            <button onclick="closeErrors()"
                    style="background:none;border:none;color:var(--text-muted);
                           font-size:1.3rem;cursor:pointer">&times;</button>
        </div>
        <pre id="errorContent"
             style="font-size:.8rem;color:var(--danger);line-height:1.8;
                    white-space:pre-wrap;word-break:break-word"></pre>
    </div>
</div>

<script>
function showErrors(errors) {
    document.getElementById('errorContent').textContent = errors;
    document.getElementById('errorModal').style.display = 'flex';
}
function closeErrors() {
    document.getElementById('errorModal').style.display = 'none';
}
document.getElementById('errorModal').addEventListener('click', function(e) {
    if (e.target === this) closeErrors();
});
</script>