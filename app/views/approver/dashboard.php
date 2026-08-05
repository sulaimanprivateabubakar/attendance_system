<?php $pageTitle = $info['label'] . ' Dashboard'; ?>

<div class="page-title">
    <div>
        <h1><?= htmlspecialchars($info['label']) ?></h1>
        <p>Payment Claim Approval Dashboard</p>
    </div>
</div>

<!-- Stats -->
<div class="stat-strip" style="margin-bottom:22px">
    <div class="stat-box">
        <span class="stat-num text-warn"><?= $stats['pending'] ?></span>
        <span class="stat-label">Awaiting Your Review</span>
    </div>
    <div class="stat-box">
        <span class="stat-num text-success"><?= $stats['approved'] ?></span>
        <span class="stat-label">Approved</span>
    </div>
    <div class="stat-box">
        <span class="stat-num text-danger"><?= $stats['rejected'] ?></span>
        <span class="stat-label">Rejected</span>
    </div>
</div>

<!-- Pending Claims -->
<?php if (!empty($pending)): ?>
<div class="panel" style="margin-bottom:22px;border-left:4px solid var(--warning)">
    <div class="panel-header">
        <h2>
            <i class="fas fa-clock" style="color:var(--warning);margin-right:8px"></i>
            Pending Your Approval
        </h2>
        <span class="badge" style="background:rgba(245,158,11,.15);color:var(--warning)">
            <?= count($pending) ?>
        </span>
    </div>
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Lecturer</th><th>Department</th>
                    <th>Month</th><th>Submitted</th><th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($pending as $c): ?>
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px">
                            <div style="width:34px;height:34px;border-radius:9px;
                                        background:linear-gradient(135deg,var(--primary),var(--primary-light));
                                        display:flex;align-items:center;justify-content:center;
                                        font-weight:700;font-size:.75rem;color:#fff;flex-shrink:0">
                                <?= strtoupper(substr($c['lecturer_name'],0,2)) ?>
                            </div>
                            <div>
                                <strong><?= htmlspecialchars($c['lecturer_name']) ?></strong><br>
                                <code style="font-size:.72rem;color:var(--text-muted)">
                                    <?= htmlspecialchars($c['staff_number']) ?>
                                </code>
                            </div>
                        </div>
                    </td>
                    <td><?= htmlspecialchars($c['dept_name'] ?? '—') ?></td>
                    <td><strong><?= date('F Y', strtotime($c['month'].'-01')) ?></strong></td>
                    <td style="font-size:.8rem;color:var(--text-muted)">
                        <?= $c['submitted_at'] ? date('M j, Y', strtotime($c['submitted_at'])) : '—' ?>
                    </td>
                    <td>
                        <a href="<?= BASE_URL ?>/approver/claims/<?= $c['id'] ?>"
                           class="btn btn-primary btn-sm">
                            <i class="fas fa-eye"></i> Review
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php else: ?>
<div class="panel" style="margin-bottom:22px">
    <div class="empty-state">
        <div class="empty-icon">✅</div>
        <p>No claims pending your approval.</p>
    </div>
</div>
<?php endif; ?>

<!-- Recently Processed -->
<?php if (!empty($processed)): ?>
<div class="panel">
    <div class="panel-header">
        <h2>
            <i class="fas fa-history" style="color:var(--primary);margin-right:8px"></i>
            Recently Processed (Last 30 Days)
        </h2>
    </div>
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Lecturer</th><th>Month</th><th>Decision</th><th>Date</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $col = $info['col'];
            foreach ($processed as $c):
            ?>
                <tr>
                    <td><?= htmlspecialchars($c['lecturer_name']) ?></td>
                    <td><?= date('F Y', strtotime($c['month'].'-01')) ?></td>
                    <td>
                        <span class="badge <?= $c[$col.'_approved'] ? 'badge-active' : 'badge-absent' ?>">
                            <?= $c[$col.'_approved'] ? '✓ Approved' : '✗ Rejected' ?>
                        </span>
                    </td>
                    <td style="font-size:.8rem;color:var(--text-muted)">
                        <?= $c[$col.'_approved_at'] ? date('M j, Y H:i', strtotime($c[$col.'_approved_at'])) : '—' ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>