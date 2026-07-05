<?php $pageTitle = 'Payment Claims'; ?>

<div class="page-title">
    <div>
        <h1>Payment Claims</h1>
        <p>Your part-time teaching payment claim forms</p>
    </div>
    <a href="<?= BASE_URL ?>/lecturer/claims/create" class="btn btn-primary">
        <i class="fas fa-plus"></i> New Claim
    </a>
</div>

<?php if (empty($claims)): ?>
<div class="empty-state">
    <div class="empty-icon">📄</div>
    <p>No claims submitted yet.</p>
    <a href="<?= BASE_URL ?>/lecturer/claims/create" class="btn btn-primary">
        <i class="fas fa-plus"></i> Create First Claim
    </a>
</div>
<?php else: ?>
<div class="panel">
    <div class="panel-header">
        <h2><i class="fas fa-file-invoice-dollar" style="color:var(--primary);margin-right:8px"></i>My Claims</h2>
        <span class="badge badge-count"><?= count($claims) ?></span>
    </div>
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Month</th>
                    <th>Academic Year</th>
                    <th>Hourly Rate</th>
                    <th>Designation</th>
                    <th>Status</th>
                    <th>Submitted</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($claims as $c): ?>
                <tr>
                    <td><strong><?= date('F Y', strtotime($c['month'] . '-01')) ?></strong></td>
                    <td><?= htmlspecialchars($c['academic_year']) ?></td>
                    <td>K <?= number_format($c['hourly_rate'], 2) ?></td>
                    <td>
                        <span class="badge <?= $c['designation'] === 'full_time' ? 'badge-lecturer' : 'badge-student' ?>">
                            <?= $c['designation'] === 'full_time' ? 'Full Time' : 'Part Time' ?>
                        </span>
                    </td>
                    <td>
                        <span class="badge badge-<?= $c['status'] === 'approved' ? 'active' : ($c['status'] === 'rejected' ? 'absent' : ($c['status'] === 'submitted' ? 'pending' : 'closed')) ?>">
                            <?= ucfirst($c['status']) ?>
                        </span>
                    </td>
                    <td style="color:var(--text-muted);font-size:.8rem">
                        <?= $c['submitted_at'] ? date('M j, Y', strtotime($c['submitted_at'])) : '—' ?>
                    </td>
                    <td>
                        <div style="display:flex;gap:8px">
                            <a href="<?= BASE_URL ?>/lecturer/claims/<?= $c['id'] ?>"
                               class="btn btn-sm btn-secondary">
                                <i class="fas fa-eye"></i> View
                            </a>
                            <a href="<?= BASE_URL ?>/lecturer/claims/<?= $c['id'] ?>/print"
                               target="_blank"
                               class="btn btn-sm btn-primary">
                                <i class="fas fa-print"></i> Print
                            </a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>