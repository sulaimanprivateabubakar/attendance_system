<?php $pageTitle = 'Claim — ' . $data['monthLabel']; ?>

<div class="page-title">
    <div>
        <a href="<?= BASE_URL ?>/lecturer/claims" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Claims
        </a>
        <h1>Payment Claim — <?= $data['monthLabel'] ?></h1>
        <p>Review your claim before submitting</p>
    </div>
    <div style="display:flex;gap:10px">
        <a href="<?= BASE_URL ?>/lecturer/claims/<?= $claim['id'] ?>/print"
           target="_blank" class="btn btn-secondary">
            <i class="fas fa-print"></i> Print / PDF
        </a>
        <?php if ($claim['status'] === 'draft'): ?>
        <form method="POST"
              action="<?= BASE_URL ?>/lecturer/claims/<?= $claim['id'] ?>/submit">
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">
            <button type="submit" class="btn btn-primary"
                    onclick="return confirm('Submit this claim? You cannot edit it after submission.')">
                <i class="fas fa-paper-plane"></i> Submit Claim
            </button>
        </form>
        <?php endif; ?>
    </div>
</div>

<!-- Status banner -->
<?php
$statusColor = match($claim['status']) {
    'approved'  => 'var(--success)',
    'rejected'  => 'var(--danger)',
    'submitted' => 'var(--warning)',
    default     => 'var(--text-muted)',
};
?>
<div style="background:rgba(255,255,255,.04);border-radius:14px;padding:16px 20px;
            margin-bottom:22px;border-left:4px solid <?= $statusColor ?>;
            display:flex;justify-content:space-between;align-items:center">
    <div>
        <strong style="color:var(--text)">Status:</strong>
        <span class="badge badge-<?= $claim['status'] === 'approved' ? 'active' : ($claim['status'] === 'rejected' ? 'absent' : 'pending') ?>"
              style="margin-left:8px;font-size:.85rem">
            <?= strtoupper($claim['status']) ?>
        </span>
    </div>
    <?php if ($claim['submitted_at']): ?>
    <div style="font-size:.8rem;color:var(--text-muted)">
        Submitted: <?= date('M j, Y H:i', strtotime($claim['submitted_at'])) ?>
    </div>
    <?php endif; ?>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:22px;margin-bottom:22px">

    <!-- Part A — Claimant -->
    <div class="form-card">
        <h2 style="font-size:.85rem;font-weight:700;text-transform:uppercase;
                   letter-spacing:.08em;color:var(--text-muted);margin-bottom:16px">
            Part A — Claimant Details
        </h2>
        <table class="table">
            <tr>
                <td style="color:var(--text-muted);width:140px">Name</td>
                <td><strong><?= htmlspecialchars($claim['lecturer_name']) ?></strong></td>
            </tr>
            <tr>
                <td style="color:var(--text-muted)">Staff ID</td>
                <td><?= htmlspecialchars($claim['staff_number']) ?></td>
            </tr>
            <tr>
                <td style="color:var(--text-muted)">Department</td>
                <td><?= htmlspecialchars($claim['dept_name'] ?? '—') ?></td>
            </tr>
            <tr>
                <td style="color:var(--text-muted)">Telephone</td>
                <td><?= htmlspecialchars($claim['telephone'] ?? '—') ?></td>
            </tr>
            <tr>
                <td style="color:var(--text-muted)">Designation</td>
                <td><?= $claim['designation'] === 'full_time' ? 'Full-time Staff' : 'Part-time Staff' ?></td>
            </tr>
            <tr>
                <td style="color:var(--text-muted)">Academic Year</td>
                <td><?= htmlspecialchars($claim['academic_year']) ?></td>
            </tr>
            <tr>
                <td style="color:var(--text-muted)">Bank</td>
                <td><?= htmlspecialchars($claim['bank_name'] ?? '—') ?></td>
            </tr>
            <tr>
                <td style="color:var(--text-muted)">Branch</td>
                <td><?= htmlspecialchars($claim['bank_branch'] ?? '—') ?></td>
            </tr>
            <tr>
                <td style="color:var(--text-muted)">Account No.</td>
                <td><?= htmlspecialchars($claim['account_number'] ?? '—') ?></td>
            </tr>
        </table>
    </div>

    <!-- Part C summary -->
    <div class="form-card">
        <h2 style="font-size:.85rem;font-weight:700;text-transform:uppercase;
                   letter-spacing:.08em;color:var(--text-muted);margin-bottom:16px">
            Part C — Claim Summary
        </h2>
        <table class="table">
            <tr>
                <td style="color:var(--text-muted)">No. of Students Enrolled</td>
                <td><strong><?= (int)$data['totalStudents'] ?></strong></td>
            </tr>
            <tr>
                <td style="color:var(--text-muted)">Total Hours Taught</td>
                <td><strong><?= number_format($data['totalHours'], 2) ?> hrs</strong></td>
            </tr>
            <tr>
                <td style="color:var(--text-muted)">Hourly Rate</td>
                <td><strong>K <?= number_format($claim['hourly_rate'], 2) ?></strong></td>
            </tr>
            <tr>
                <td style="color:var(--text-muted)">Total Amount</td>
                <td>
                    <strong style="color:var(--success);font-size:1.1rem">
                        K <?= number_format($data['totalAmount'], 2) ?>
                    </strong>
                </td>
            </tr>
            <tr>
                <td style="color:var(--text-muted)">Period</td>
                <td><?= $data['monthLabel'] ?></td>
            </tr>
        </table>
    </div>
</div>

<!-- Part B — Session Details -->
<div class="panel">
    <div class="panel-header">
        <h2>Part B — Details of Claim</h2>
        <span class="badge badge-count"><?= count($data['courses']) ?> courses</span>
    </div>
    <div class="table-wrap">
        <?php if (empty($data['courses'])): ?>
        <div class="empty-state">
            <p>No sessions found for this period.</p>
        </div>
        <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Module Code</th>
                    <th>Module Description</th>
                    <th>Dates</th>
                    <th>Sessions</th>
                    <th>Total Hours</th>
                    <th>@ Hourly Rate</th>
                    <th>Amount Due</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($data['courses'] as $c): ?>
                <?php $amount = $c['total_hours'] * $claim['hourly_rate']; ?>
                <tr>
                    <td><span class="course-code-badge"><?= htmlspecialchars($c['code']) ?></span></td>
                    <td><?= htmlspecialchars($c['course_name']) ?></td>
                    <td style="font-size:.8rem;color:var(--text-muted)">
                        <?= $c['first_date'] ?><br><?= $c['last_date'] ?>
                    </td>
                    <td><?= (int)$c['session_count'] ?></td>
                    <td><strong><?= number_format($c['total_hours'], 2) ?></strong></td>
                    <td>K <?= number_format($claim['hourly_rate'], 2) ?></td>
                    <td><strong>K <?= number_format($amount, 2) ?></strong></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr style="background:rgba(255,255,255,.04)">
                    <td colspan="4" style="font-weight:700;color:var(--text)">TOTAL PAYMENT</td>
                    <td><strong><?= number_format($data['totalHours'], 2) ?> hrs</strong></td>
                    <td></td>
                    <td>
                        <strong style="color:var(--success);font-size:1rem">
                            K <?= number_format($data['totalAmount'], 2) ?>
                        </strong>
                    </td>
                </tr>
            </tfoot>
        </table>
        <?php endif; ?>
    </div>
</div>

<?php if ($claim['notes']): ?>
<div class="form-card" style="margin-top:22px">
    <h2 style="font-size:.85rem;font-weight:700;text-transform:uppercase;
               letter-spacing:.08em;color:var(--text-muted);margin-bottom:12px">
        Further Information
    </h2>
    <p style="color:var(--text-light)"><?= htmlspecialchars($claim['notes']) ?></p>
</div>
<?php endif; ?>