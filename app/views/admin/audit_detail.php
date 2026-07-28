<?php $pageTitle = 'Audit Event #' . $log['id']; ?>

<div class="page-title">
    <div>
        <a href="<?= BASE_URL ?>/admin/audit" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Audit Logs
        </a>
        <h1>Audit Event Detail</h1>
        <p><?= htmlspecialchars($log['action']) ?> — <?= date('M j, Y H:i:s', strtotime($log['created_at'])) ?></p>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:22px">

    <div class="form-card">
        <h2 style="font-size:.85rem;font-weight:700;text-transform:uppercase;
                   letter-spacing:.08em;color:var(--text-muted);margin-bottom:16px">
            Event Details
        </h2>
        <table class="table">
            <tr>
                <td style="color:var(--text-muted);width:130px">Event ID</td>
                <td><code>#<?= $log['id'] ?></code></td>
            </tr>
            <tr>
                <td style="color:var(--text-muted)">Action</td>
                <td>
                    <code style="color:var(--primary-light);background:rgba(255,255,255,.06);
                                 padding:3px 8px;border-radius:6px">
                        <?= htmlspecialchars($log['action']) ?>
                    </code>
                </td>
            </tr>
            <tr>
                <td style="color:var(--text-muted)">Module</td>
                <td><?= htmlspecialchars($log['module']) ?></td>
            </tr>
            <tr>
                <td style="color:var(--text-muted)">Description</td>
                <td><?= htmlspecialchars($log['description'] ?? '—') ?></td>
            </tr>
            <tr>
                <td style="color:var(--text-muted)">Date & Time</td>
                <td><?= date('D, M j Y \a\t H:i:s', strtotime($log['created_at'])) ?></td>
            </tr>
        </table>
    </div>

    <div class="form-card">
        <h2 style="font-size:.85rem;font-weight:700;text-transform:uppercase;
                   letter-spacing:.08em;color:var(--text-muted);margin-bottom:16px">
            User & Session
        </h2>
        <table class="table">
            <tr>
                <td style="color:var(--text-muted);width:130px">User</td>
                <td><strong><?= htmlspecialchars($log['user_name'] ?? 'System') ?></strong></td>
            </tr>
            <tr>
                <td style="color:var(--text-muted)">Role</td>
                <td>
                    <span class="badge badge-<?= $log['user_role'] ?? 'closed' ?>">
                        <?= $log['user_role'] ?? '—' ?>
                    </span>
                </td>
            </tr>
            <tr>
                <td style="color:var(--text-muted)">IP Address</td>
                <td><code><?= htmlspecialchars($log['ip_address'] ?? '—') ?></code></td>
            </tr>
            <tr>
                <td style="color:var(--text-muted)">Browser</td>
                <td style="font-size:.75rem;color:var(--text-muted)">
                    <?= htmlspecialchars(substr($log['user_agent'] ?? '—', 0, 80)) ?>
                </td>
            </tr>
        </table>
    </div>

</div>

<?php if ($log['old_value'] || $log['new_value']): ?>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:22px;margin-top:22px">

    <?php if ($log['old_value']): ?>
    <div class="form-card" style="border-left:3px solid var(--danger)">
        <h2 style="font-size:.85rem;font-weight:700;text-transform:uppercase;
                   letter-spacing:.08em;color:var(--danger);margin-bottom:12px">
            <i class="fas fa-minus-circle" style="margin-right:6px"></i>
            Before (Old Value)
        </h2>
        <pre style="font-size:.8rem;color:var(--text-light);line-height:1.7;
                    background:rgba(239,68,68,.06);padding:14px;border-radius:10px;
                    white-space:pre-wrap;word-break:break-word">
<?php
$old = $log['old_value'];
$decoded = json_decode($old, true);
echo htmlspecialchars($decoded ? json_encode($decoded, JSON_PRETTY_PRINT) : $old);
?>
        </pre>
    </div>
    <?php endif; ?>

    <?php if ($log['new_value']): ?>
    <div class="form-card" style="border-left:3px solid var(--success)">
        <h2 style="font-size:.85rem;font-weight:700;text-transform:uppercase;
                   letter-spacing:.08em;color:var(--success);margin-bottom:12px">
            <i class="fas fa-plus-circle" style="margin-right:6px"></i>
            After (New Value)
        </h2>
        <pre style="font-size:.8rem;color:var(--text-light);line-height:1.7;
                    background:rgba(22,163,74,.06);padding:14px;border-radius:10px;
                    white-space:pre-wrap;word-break:break-word">
<?php
$new = $log['new_value'];
$decoded = json_decode($new, true);
echo htmlspecialchars($decoded ? json_encode($decoded, JSON_PRETTY_PRINT) : $new);
?>
        </pre>
    </div>
    <?php endif; ?>

</div>
<?php endif; ?>