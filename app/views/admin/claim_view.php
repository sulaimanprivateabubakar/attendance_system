<?php $pageTitle = 'Review Claim — ' . htmlspecialchars($claim['lecturer_name']); ?>

<div class="page-title">
    <div>
        <a href="<?= BASE_URL ?>/admin/claims" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Claims
        </a>
        <h1>Payment Claim Review</h1>
        <p><?= htmlspecialchars($claim['lecturer_name']) ?> — <?= $monthLabel ?></p>
    </div>
    <div style="display:flex;gap:10px;flex-wrap:wrap">
        <a href="<?= BASE_URL ?>/lecturer/claims/<?= $claim['id'] ?>/print"
           target="_blank" class="btn btn-secondary">
            <i class="fas fa-print"></i> Print / PDF
        </a>
        <?php if ($claim['status'] === 'submitted'): ?>
        <form method="POST"
              action="<?= BASE_URL ?>/admin/claims/<?= $claim['id'] ?>/approve">
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">
            <button type="submit" class="btn btn-success"
                    onclick="return confirm('Approve this payment claim?')">
                <i class="fas fa-check"></i> Approve Claim
            </button>
        </form>
        <form method="POST"
              action="<?= BASE_URL ?>/admin/claims/<?= $claim['id'] ?>/reject">
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">
            <button type="submit" class="btn btn-danger"
                    onclick="return confirm('Reject this payment claim?')">
                <i class="fas fa-times"></i> Reject Claim
            </button>
        </form>
        <?php endif; ?>
    </div>
</div>

<!-- Status Banner -->
<?php
$statusColor = match($claim['status']) {
    'approved'  => 'var(--success)',
    'rejected'  => 'var(--danger)',
    'submitted' => 'var(--warning)',
    default     => 'var(--text-muted)',
};
$statusIcon = match($claim['status']) {
    'approved'  => 'fas fa-check-circle',
    'rejected'  => 'fas fa-times-circle',
    'submitted' => 'fas fa-clock',
    default     => 'fas fa-edit',
};
?>
<div style="background:rgba(255,255,255,.04);border-radius:14px;padding:18px 22px;
            margin-bottom:22px;border-left:4px solid <?= $statusColor ?>;
            display:flex;justify-content:space-between;align-items:center">
    <div style="display:flex;align-items:center;gap:12px">
        <i class="<?= $statusIcon ?>" style="color:<?= $statusColor ?>;font-size:1.3rem"></i>
        <div>
            <strong style="color:var(--text)">
                Status: <?= strtoupper($claim['status']) ?>
            </strong>
            <?php if ($claim['status'] === 'submitted'): ?>
            <div style="font-size:.8rem;color:var(--text-muted);margin-top:2px">
                This claim is awaiting your review and approval
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php if ($claim['submitted_at']): ?>
    <div style="font-size:.8rem;color:var(--text-muted);text-align:right">
        <div>Submitted</div>
        <strong><?= date('M j, Y H:i', strtotime($claim['submitted_at'])) ?></strong>
    </div>
    <?php endif; ?>
</div>

<!-- Summary Strip -->
<div class="stat-strip" style="margin-bottom:22px">
    <div class="stat-box">
        <span class="stat-num"><?= (int)$totalStudents ?></span>
        <span class="stat-label">Students Enrolled</span>
    </div>
    <div class="stat-box">
        <span class="stat-num"><?= count($courses) ?></span>
        <span class="stat-label">Courses</span>
    </div>
    <div class="stat-box">
        <span class="stat-num"><?= number_format($totalHours, 1) ?></span>
        <span class="stat-label">Total Hours</span>
    </div>
    <div class="stat-box">
        <span class="stat-num text-success" style="font-size:1.4rem">
            K<?= number_format($totalAmount, 2) ?>
        </span>
        <span class="stat-label">Total Amount</span>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:22px;margin-bottom:22px">

    <!-- Part A -->
    <div class="form-card">
        <h2 style="font-size:.82rem;font-weight:700;text-transform:uppercase;
                   letter-spacing:.08em;color:var(--text-muted);margin-bottom:16px">
            <i class="fas fa-user" style="color:var(--primary);margin-right:6px"></i>
            Part A — Claimant Details
        </h2>
        <table class="table">
            <tr>
                <td style="color:var(--text-muted);width:130px;font-size:.82rem">Name</td>
                <td><strong><?= htmlspecialchars($claim['lecturer_name']) ?></strong></td>
            </tr>
            <tr>
                <td style="color:var(--text-muted);font-size:.82rem">Staff ID</td>
                <td>
                    <code style="background:rgba(255,255,255,.06);padding:2px 8px;border-radius:6px">
                        <?= htmlspecialchars($claim['staff_number']) ?>
                    </code>
                </td>
            </tr>
            <tr>
                <td style="color:var(--text-muted);font-size:.82rem">Department</td>
                <td><?= htmlspecialchars($claim['dept_name'] ?? '—') ?></td>
            </tr>
            <tr>
                <td style="color:var(--text-muted);font-size:.82rem">Telephone</td>
                <td><?= htmlspecialchars($claim['telephone'] ?? '—') ?></td>
            </tr>
            <tr>
                <td style="color:var(--text-muted);font-size:.82rem">Designation</td>
                <td>
                    <span class="badge <?= $claim['designation'] === 'full_time' ? 'badge-lecturer' : 'badge-student' ?>">
                        <?= $claim['designation'] === 'full_time' ? 'Full-time' : 'Part-time' ?>
                    </span>
                </td>
            </tr>
            <tr>
                <td style="color:var(--text-muted);font-size:.82rem">Academic Year</td>
                <td><?= htmlspecialchars($claim['academic_year']) ?></td>
            </tr>
            <tr>
                <td style="color:var(--text-muted);font-size:.82rem">Period</td>
                <td><strong><?= $monthLabel ?></strong></td>
            </tr>
        </table>
    </div>

    <!-- Part A Bank + Part C -->
    <div style="display:flex;flex-direction:column;gap:16px">

        <!-- Bank Details -->
        <div class="form-card">
            <h2 style="font-size:.82rem;font-weight:700;text-transform:uppercase;
                       letter-spacing:.08em;color:var(--text-muted);margin-bottom:14px">
                <i class="fas fa-university" style="color:var(--primary);margin-right:6px"></i>
                Bank Details
            </h2>
            <table class="table">
                <tr>
                    <td style="color:var(--text-muted);width:100px;font-size:.82rem">Bank</td>
                    <td><?= htmlspecialchars($claim['bank_name'] ?? '—') ?></td>
                </tr>
                <tr>
                    <td style="color:var(--text-muted);font-size:.82rem">Branch</td>
                    <td><?= htmlspecialchars($claim['bank_branch'] ?? '—') ?></td>
                </tr>
                <tr>
                    <td style="color:var(--text-muted);font-size:.82rem">Account No.</td>
                    <td>
                        <code style="background:rgba(255,255,255,.06);padding:2px 8px;border-radius:6px">
                            <?= htmlspecialchars($claim['account_number'] ?? '—') ?>
                        </code>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Part C Summary -->
        <div class="form-card">
            <h2 style="font-size:.82rem;font-weight:700;text-transform:uppercase;
                       letter-spacing:.08em;color:var(--text-muted);margin-bottom:14px">
                <i class="fas fa-calculator" style="color:var(--primary);margin-right:6px"></i>
                Part C — Claim Summary
            </h2>
            <table class="table">
                <tr>
                    <td style="color:var(--text-muted);font-size:.82rem">Students Enrolled</td>
                    <td><strong><?= (int)$totalStudents ?></strong></td>
                </tr>
                <tr>
                    <td style="color:var(--text-muted);font-size:.82rem">Hours Taught</td>
                    <td><strong><?= number_format($totalHours, 2) ?> hrs</strong></td>
                </tr>
                <tr>
                    <td style="color:var(--text-muted);font-size:.82rem">Hourly Rate</td>
                    <td><strong>K <?= number_format($claim['hourly_rate'], 2) ?></strong></td>
                </tr>
                <tr>
                    <td style="color:var(--text-muted);font-size:.82rem">Total Amount</td>
                    <td>
                        <strong style="color:var(--success);font-size:1.05rem">
                            K <?= number_format($totalAmount, 2) ?>
                        </strong>
                    </td>
                </tr>
            </table>
        </div>

    </div>
</div>

<!-- Part B — Session Details -->
<div class="panel" style="margin-bottom:22px">
    <div class="panel-header">
        <h2>
            <i class="fas fa-table" style="color:var(--primary);margin-right:8px"></i>
            Part B — Details of Claim
        </h2>
        <span class="badge badge-count"><?= count($courses) ?> modules</span>
    </div>
    <div class="table-wrap">
        <?php if (empty($courses)): ?>
        <div class="empty-state">
            <div class="empty-icon">📋</div>
            <p>No sessions found for this period.</p>
        </div>
        <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Module Code</th>
                    <th>Module Description</th>
                    <th>Date Range</th>
                    <th>Sessions</th>
                    <th>Total Hours</th>
                    <th>@ Hourly Rate</th>
                    <th>Amount Due</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($courses as $c):
                $amount = $c['total_hours'] * $claim['hourly_rate'];
            ?>
                <tr>
                    <td>
                        <span class="course-code-badge">
                            <?= htmlspecialchars($c['code']) ?>
                        </span>
                    </td>
                    <td><?= htmlspecialchars($c['course_name']) ?></td>
                    <td style="font-size:.8rem;color:var(--text-muted)">
                        <?= $c['first_date'] ?> —<br><?= $c['last_date'] ?>
                    </td>
                    <td style="text-align:center"><?= (int)$c['session_count'] ?></td>
                    <td>
                        <strong><?= number_format($c['total_hours'], 2) ?> hrs</strong>
                    </td>
                    <td>K <?= number_format($claim['hourly_rate'], 2) ?></td>
                    <td>
                        <strong>K <?= number_format($amount, 2) ?></strong>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr style="background:rgba(255,255,255,.04)">
                    <td colspan="4"
                        style="font-weight:700;color:var(--text);text-align:right;
                               padding-right:16px">
                        TOTAL PAYMENT
                    </td>
                    <td>
                        <strong><?= number_format($totalHours, 2) ?> hrs</strong>
                    </td>
                    <td></td>
                    <td>
                        <strong style="color:var(--success);font-size:1rem">
                            K <?= number_format($totalAmount, 2) ?>
                        </strong>
                    </td>
                </tr>
            </tfoot>
        </table>
        <?php endif; ?>
    </div>
</div>

<?php if (!empty($claim['notes'])): ?>
<div class="form-card" style="margin-bottom:22px">
    <h2 style="font-size:.82rem;font-weight:700;text-transform:uppercase;
               letter-spacing:.08em;color:var(--text-muted);margin-bottom:12px">
        <i class="fas fa-sticky-note" style="color:var(--primary);margin-right:6px"></i>
        Further Information
    </h2>
    <p style="color:var(--text-light);font-size:.9rem;line-height:1.6">
        <?= htmlspecialchars($claim['notes']) ?>
    </p>
</div>
<?php endif; ?>

<!-- Approve / Reject at bottom too -->
<?php if ($claim['status'] === 'submitted'): ?>
<div style="display:flex;gap:12px;justify-content:flex-end;
            padding:20px;background:rgba(255,255,255,.03);
            border-radius:14px;border:1px solid rgba(255,255,255,.06)">
    <span style="color:var(--text-muted);font-size:.85rem;
                 align-self:center;margin-right:auto">
        <i class="fas fa-info-circle" style="margin-right:6px"></i>
        Review the claim above then approve or reject.
    </span>
    <form method="POST"
          action="<?= BASE_URL ?>/admin/claims/<?= $claim['id'] ?>/reject">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">
        <button type="submit" class="btn btn-danger"
                onclick="return confirm('Reject this payment claim?')">
            <i class="fas fa-times"></i> Reject Claim
        </button>
    </form>
    <form method="POST"
          action="<?= BASE_URL ?>/admin/claims/<?= $claim['id'] ?>/approve">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">
        <button type="submit" class="btn btn-success"
                onclick="return confirm('Approve this payment claim for K<?= number_format($totalAmount, 2) ?>?')">
            <i class="fas fa-check"></i> Approve Claim
        </button>
    </form>
</div>
<?php endif; ?>