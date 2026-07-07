<?php $pageTitle = 'Payment Claims'; ?>

<div class="page-title">
    <div>
        <h1>Payment Claims</h1>
        <p>Review and approve lecturer payment claims</p>
    </div>
</div>

<!-- Stats -->
<div class="stat-strip" style="margin-bottom:22px">
    <?php
    $submitted = count(array_filter($claims, fn($c) => $c['status'] === 'submitted'));
    $approved  = count(array_filter($claims, fn($c) => $c['status'] === 'approved'));
    $draft     = count(array_filter($claims, fn($c) => $c['status'] === 'draft'));
    $rejected  = count(array_filter($claims, fn($c) => $c['status'] === 'rejected'));
    ?>
    <div class="stat-box">
        <span class="stat-num text-warn"><?= $submitted ?></span>
        <span class="stat-label">Awaiting Review</span>
    </div>
    <div class="stat-box">
        <span class="stat-num text-success"><?= $approved ?></span>
        <span class="stat-label">Approved</span>
    </div>
    <div class="stat-box">
        <span class="stat-num"><?= $draft ?></span>
        <span class="stat-label">Draft</span>
    </div>
    <div class="stat-box">
        <span class="stat-num text-danger"><?= $rejected ?></span>
        <span class="stat-label">Rejected</span>
    </div>
</div>

<div class="panel">
    <div class="panel-header">
        <h2>
            <i class="fas fa-file-invoice-dollar" style="color:var(--primary);margin-right:8px"></i>
            All Claims
        </h2>
        <span class="badge badge-count"><?= count($claims) ?></span>
    </div>
    <div class="table-wrap">
        <?php if (empty($claims)): ?>
        <div class="empty-state">
            <div class="empty-icon">📄</div>
            <p>No payment claims submitted yet.</p>
        </div>
        <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Lecturer</th>
                    <th>Staff No.</th>
                    <th>Department</th>
                    <th>Month</th>
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
                    <td>
                        <div style="display:flex;align-items:center;gap:10px">
                            <div style="width:34px;height:34px;border-radius:9px;
                                        background:linear-gradient(135deg,var(--primary),var(--primary-light));
                                        display:flex;align-items:center;justify-content:center;
                                        font-weight:700;font-size:.75rem;color:#fff;flex-shrink:0">
                                <?= strtoupper(substr($c['lecturer_name'], 0, 2)) ?>
                            </div>
                            <strong><?= htmlspecialchars($c['lecturer_name']) ?></strong>
                        </div>
                    </td>
                    <td><code style="font-size:.78rem;color:var(--text-muted)"><?= htmlspecialchars($c['staff_number']) ?></code></td>
                    <td style="color:var(--text-muted)"><?= htmlspecialchars($c['dept_name'] ?? '—') ?></td>
                    <td><strong><?= date('M Y', strtotime($c['month'] . '-01')) ?></strong></td>
                    <td>K <?= number_format($c['hourly_rate'], 2) ?></td>
                    <td>
                        <span class="badge <?= $c['designation'] === 'full_time' ? 'badge-lecturer' : 'badge-student' ?>">
                            <?= $c['designation'] === 'full_time' ? 'Full Time' : 'Part Time' ?>
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
                    <td style="color:var(--text-muted);font-size:.8rem">
                        <?= $c['submitted_at'] ? date('M j, Y', strtotime($c['submitted_at'])) : '—' ?>
                    </td>
                    <td>
    <div style="display:flex;gap:6px;flex-wrap:wrap">
        <a href="<?= BASE_URL ?>/admin/claims/<?= $c['id'] ?>"
           class="btn btn-sm btn-primary">
            <i class="fas fa-eye"></i> View
        </a>
        <a href="<?= BASE_URL ?>/lecturer/claims/<?= $c['id'] ?>/print"
           target="_blank" class="btn btn-sm btn-secondary">
            <i class="fas fa-print"></i>
        </a>
        <?php if ($c['status'] === 'submitted'): ?>
        <form method="POST"
              action="<?= BASE_URL ?>/admin/claims/<?= $c['id'] ?>/approve"
              style="display:inline">
            <input type="hidden" name="_csrf" value="<?= Auth::generateCsrfToken() ?>">
            <button class="btn btn-sm btn-success"
                    onclick="return confirm('Approve this claim?')">
                <i class="fas fa-check"></i>
            </button>
        </form>
        <form method="POST"
              action="<?= BASE_URL ?>/admin/claims/<?= $c['id'] ?>/reject"
              style="display:inline">
            <input type="hidden" name="_csrf" value="<?= Auth::generateCsrfToken() ?>">
            <button class="btn btn-sm btn-danger"
                    onclick="return confirm('Reject this claim?')">
                <i class="fas fa-times"></i>
            </button>
        </form>
        <?php endif; ?>
    </div>
</td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>