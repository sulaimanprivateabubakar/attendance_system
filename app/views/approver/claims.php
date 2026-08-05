<?php $pageTitle = 'Payment Claims — ' . $info['label']; ?>

<div class="page-title">
    <div>
        <h1>Payment Claims</h1>
        <p><?= htmlspecialchars($info['label']) ?> approval queue</p>
    </div>
</div>

<!-- Pending -->
<?php if (!empty($pending)): ?>
<div style="margin-bottom:8px">
    <div class="section-title">
        <i class="fas fa-clock" style="color:var(--warning)"></i>
        Awaiting Your Approval
    </div>
</div>
<?php foreach ($pending as $c): ?>
<div class="panel" style="margin-bottom:14px;border-left:4px solid var(--warning)">
    <div style="padding:18px 22px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px">
        <div>
            <strong style="color:var(--text);font-size:1rem">
                <?= htmlspecialchars($c['lecturer_name']) ?>
            </strong>
            <code style="margin-left:8px;font-size:.75rem;color:var(--text-muted);
                         background:rgba(255,255,255,.06);padding:2px 8px;border-radius:6px">
                <?= htmlspecialchars($c['staff_number']) ?>
            </code>
            <div style="font-size:.82rem;color:var(--text-muted);margin-top:4px">
                <i class="fas fa-building" style="margin-right:4px"></i>
                <?= htmlspecialchars($c['dept_name'] ?? '—') ?>
                &nbsp;·&nbsp;
                <i class="fas fa-calendar" style="margin-right:4px"></i>
                <?= date('F Y', strtotime($c['month'].'-01')) ?>
                &nbsp;·&nbsp; K <?= number_format($c['hourly_rate'], 2) ?>/hr
            </div>
        </div>
        <div style="display:flex;gap:10px">
            <a href="<?= BASE_URL ?>/approver/claims/<?= $c['id'] ?>"
               class="btn btn-primary">
                <i class="fas fa-eye"></i> Review & Approve
            </a>
        </div>
    </div>
</div>
<?php endforeach; ?>
<?php endif; ?>

<!-- All Claims -->
<div class="panel" style="margin-top:22px">
    <div class="panel-header">
        <h2>All Claims</h2>
        <span class="badge badge-count"><?= count($all) ?></span>
    </div>
    <div class="table-wrap">
        <?php if (empty($all)): ?>
        <div class="empty-state">
            <div class="empty-icon">📄</div>
            <p>No claims yet.</p>
        </div>
        <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Lecturer</th><th>Dept</th><th>Month</th>
                    <th>Rate</th><th>Stage</th><th>Status</th><th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($all as $c): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($c['lecturer_name']) ?></strong></td>
                    <td><?= htmlspecialchars($c['dept_name'] ?? '—') ?></td>
                    <td><?= date('M Y', strtotime($c['month'].'-01')) ?></td>
                    <td>K <?= number_format($c['hourly_rate'], 2) ?></td>
                    <td>
                        <span class="badge badge-pending" style="font-size:.7rem">
                            <?= strtoupper($c['current_stage']) ?>
                        </span>
                    </td>
                    <td>
                        <span class="badge badge-<?= match($c['status']) {
                            'approved'  => 'active',
                            'rejected'  => 'absent',
                            'submitted' => 'pending',
                            default     => 'closed'
                        } ?>">
                            <?= ucfirst($c['status']) ?>
                        </span>
                    </td>
                    <td>
                        <a href="<?= BASE_URL ?>/approver/claims/<?= $c['id'] ?>"
                           class="btn btn-sm btn-secondary">
                            <i class="fas fa-eye"></i> View
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>