<?php $pageTitle = 'Review Claim — ' . htmlspecialchars($claim['lecturer_name']); ?>

<div class="page-title">
    <div>
        <a href="<?= BASE_URL ?>/approver/claims" class="back-link">
            <i class="fas fa-arrow-left"></i> Claims
        </a>
        <h1>Payment Claim Review</h1>
        <p><?= htmlspecialchars($claim['lecturer_name']) ?> — <?= $monthLabel ?></p>
    </div>
    <a href="<?= BASE_URL ?>/approver/claims/<?= $claim['id'] ?>/print"
       class="btn btn-secondary" target="_blank">
        <i class="fas fa-print"></i> Print
    </a>
</div>

<!-- Approval Pipeline -->
<div class="panel" style="margin-bottom:22px">
    <div class="panel-header">
        <h2><i class="fas fa-sitemap" style="color:var(--primary);margin-right:8px"></i>Approval Pipeline</h2>
    </div>
    <div style="padding:24px">
        <div style="display:flex;align-items:center;gap:0;overflow-x:auto;padding-bottom:8px">
            <?php
            $stages = ['hod','hoa','registrar','vc','accounts'];
            $labels = [
                'hod'       => 'Head of Dept',
                'hoa'       => 'Head of Academics',
                'registrar' => 'Registrar',
                'vc'        => 'Vice Chancellor',
                'accounts'  => 'Accounts Office',
            ];
            foreach ($stages as $i => $stage):
                $col       = $stage;
                $approved  = $claim[$col.'_approved'] ?? null;
                $approvedAt= $claim[$col.'_approved_at'] ?? null;
                $sigName   = $claim[$col.'_name'] ?? null;
                $isCurrent = $claim['current_stage'] === $stage;
                $isRejected= $claim['status'] === 'rejected' && $claim['rejected_by'] && str_starts_with($claim['rejected_by'], $stage);

                if ($approved === '1' || $approved === 1) {
                    $color = 'var(--success)'; $icon = 'fa-check'; $bg = 'rgba(34,197,94,.15)';
                } elseif ($approved === '0' || $approved === 0 || $isRejected) {
                    $color = 'var(--danger)';  $icon = 'fa-times'; $bg = 'rgba(239,68,68,.15)';
                } elseif ($isCurrent) {
                    $color = 'var(--warning)'; $icon = 'fa-clock'; $bg = 'rgba(245,158,11,.15)';
                } else {
                    $color = 'var(--text-muted)'; $icon = 'fa-circle'; $bg = 'rgba(255,255,255,.04)';
                }
            ?>
            <!-- Stage node -->
            <div style="flex-shrink:0;text-align:center;min-width:120px">
                <div style="width:48px;height:48px;border-radius:50%;background:<?= $bg ?>;
                            border:2px solid <?= $color ?>;display:flex;align-items:center;
                            justify-content:center;margin:0 auto 8px;position:relative">
                    <i class="fas <?= $icon ?>" style="color:<?= $color ?>"></i>
                </div>
                <div style="font-size:.75rem;font-weight:600;color:<?= $isCurrent ? 'var(--warning)' : 'var(--text-light)' ?>">
                    <?= $labels[$stage] ?>
                </div>
                <?php if ($sigName): ?>
                <div style="font-size:.68rem;color:var(--text-muted);margin-top:3px">
                    <?= htmlspecialchars($sigName) ?>
                </div>
                <?php endif; ?>
                <?php if ($approvedAt): ?>
                <div style="font-size:.65rem;color:var(--text-muted)">
                    <?= date('M j, Y', strtotime($approvedAt)) ?>
                </div>
                <?php endif; ?>
                <?php if ($isCurrent && $claim['status'] !== 'rejected'): ?>
                <div style="font-size:.68rem;color:var(--warning);font-weight:700;margin-top:4px">
                    ← AWAITING
                </div>
                <?php endif; ?>
            </div>

            <!-- Connector -->
            <?php if ($i < count($stages) - 1): ?>
            <div style="flex:1;height:2px;min-width:30px;
                        background:<?= ($approved == 1) ? 'var(--success)' : 'rgba(255,255,255,.08)' ?>;
                        margin-bottom:30px"></div>
            <?php endif; ?>

            <?php endforeach; ?>
        </div>

        <?php if ($claim['status'] === 'rejected'): ?>
        <div class="alert alert-error" style="margin-top:16px;margin-bottom:0">
            <i class="fas fa-times-circle"></i>
            <div>
                <strong>Rejected by <?= htmlspecialchars($claim['rejected_by'] ?? '') ?></strong><br>
                Reason: <?= htmlspecialchars($claim['rejection_reason'] ?? '—') ?>
                <?php if ($claim['rejected_at']): ?>
                &nbsp;·&nbsp; <?= date('M j, Y H:i', strtotime($claim['rejected_at'])) ?>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Claim Details -->
<div style="display:grid;grid-template-columns:1fr 1fr;gap:22px;margin-bottom:22px">

    <div class="form-card">
        <h2 style="font-size:.82rem;font-weight:700;text-transform:uppercase;
                   letter-spacing:.08em;color:var(--text-muted);margin-bottom:14px">
            Part A — Claimant Details
        </h2>
        <table class="table">
            <tr><td style="color:var(--text-muted);width:130px">Name</td>
                <td><strong><?= htmlspecialchars($claim['lecturer_name']) ?></strong></td></tr>
            <tr><td style="color:var(--text-muted)">Staff ID</td>
                <td><code><?= htmlspecialchars($claim['staff_number']) ?></code></td></tr>
            <tr><td style="color:var(--text-muted)">Department</td>
                <td><?= htmlspecialchars($claim['dept_name'] ?? '—') ?></td></tr>
            <tr><td style="color:var(--text-muted)">Telephone</td>
                <td><?= htmlspecialchars($claim['telephone'] ?? '—') ?></td></tr>
            <tr><td style="color:var(--text-muted)">Designation</td>
                <td><?= $claim['designation'] === 'full_time' ? 'Full-time' : 'Part-time' ?></td></tr>
            <tr><td style="color:var(--text-muted)">Academic Year</td>
                <td><?= htmlspecialchars($claim['academic_year']) ?></td></tr>
            <tr><td style="color:var(--text-muted)">Period</td>
                <td><strong><?= $monthLabel ?></strong></td></tr>
            <tr><td style="color:var(--text-muted)">Bank</td>
                <td><?= htmlspecialchars($claim['bank_name'] ?? '—') ?></td></tr>
            <tr><td style="color:var(--text-muted)">Account No.</td>
                <td><?= htmlspecialchars($claim['account_number'] ?? '—') ?></td></tr>
        </table>
    </div>

    <div style="display:flex;flex-direction:column;gap:16px">
        <div class="form-card">
            <h2 style="font-size:.82rem;font-weight:700;text-transform:uppercase;
                       letter-spacing:.08em;color:var(--text-muted);margin-bottom:14px">
                Part C — Summary
            </h2>
            <table class="table">
                <tr><td style="color:var(--text-muted)">Students Enrolled</td>
                    <td><strong><?= (int)$totalStudents ?></strong></td></tr>
                <tr><td style="color:var(--text-muted)">Hours Taught</td>
                    <td><strong><?= number_format($totalHours, 2) ?> hrs</strong></td></tr>
                <tr><td style="color:var(--text-muted)">Hourly Rate</td>
                    <td><strong>K <?= number_format($claim['hourly_rate'], 2) ?></strong></td></tr>
                <tr><td style="color:var(--text-muted)">Total Amount</td>
                    <td><strong style="color:var(--success);font-size:1.05rem">
                        K <?= number_format($totalAmount, 2) ?>
                    </strong></td></tr>
            </table>
        </div>
    </div>
</div>

<!-- Part B -->
<div class="panel" style="margin-bottom:22px">
    <div class="panel-header">
        <h2>Part B — Details of Claim</h2>
    </div>
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr><th>Code</th><th>Course</th><th>Dates</th>
                    <th>Sessions</th><th>Hours</th><th>Rate</th><th>Amount</th></tr>
            </thead>
            <tbody>
            <?php foreach ($courses as $c): ?>
            <?php $amt = $c['total_hours'] * $claim['hourly_rate']; ?>
                <tr>
                    <td><span class="course-code-badge"><?= htmlspecialchars($c['code']) ?></span></td>
                    <td><?= htmlspecialchars($c['course_name']) ?></td>
                    <td style="font-size:.78rem;color:var(--text-muted)">
                        <?= $c['first_date'] ?> — <?= $c['last_date'] ?>
                    </td>
                    <td><?= (int)$c['session_count'] ?></td>
                    <td><strong><?= number_format($c['total_hours'], 2) ?></strong></td>
                    <td>K <?= number_format($claim['hourly_rate'], 2) ?></td>
                    <td><strong>K <?= number_format($amt, 2) ?></strong></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr style="background:rgba(255,255,255,.04)">
                    <td colspan="4" style="font-weight:700;text-align:right;padding-right:16px">TOTAL</td>
                    <td><strong><?= number_format($totalHours, 2) ?> hrs</strong></td>
                    <td></td>
                    <td><strong style="color:var(--success)">K <?= number_format($totalAmount, 2) ?></strong></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<!-- Signatures collected so far -->
<div class="panel" style="margin-bottom:22px">
    <div class="panel-header">
        <h2><i class="fas fa-signature" style="color:var(--primary);margin-right:8px"></i>Approval Signatures</h2>
    </div>
    <div style="padding:20px 24px">
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:20px">
            <?php
            $sigStages = [
                'hod'       => ['Head of Department',  'hod'],
                'hoa'       => ['Head of Academics',   'hoa'],
                'registrar' => ['Registrar',            'registrar'],
                'vc'        => ['Vice Chancellor',      'vc'],
            ];
            foreach ($sigStages as $key => [$label, $col]):
                $approved  = $claim[$col.'_approved'] ?? null;
                $name      = $claim[$col.'_name'] ?? null;
                $at        = $claim[$col.'_approved_at'] ?? null;
                $notes     = $claim[$col.'_notes'] ?? null;
            ?>
            <div style="border:1px solid <?= $approved == 1 ? 'rgba(34,197,94,.3)' : 'rgba(255,255,255,.08)' ?>;
                        border-radius:12px;padding:16px;text-align:center">
                <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;
                            letter-spacing:.08em;color:var(--text-muted);margin-bottom:12px">
                    <?= $label ?>
                </div>

                <?php if ($approved == 1 && $name): ?>
                <!-- Signature block -->
                <div style="border-bottom:2px solid var(--success);padding-bottom:8px;
                            margin-bottom:8px;min-height:40px;display:flex;align-items:flex-end;
                            justify-content:center">
                    <span style="font-family:'Brush Script MT',cursive,serif;
                                 font-size:1.3rem;color:var(--success);letter-spacing:.05em">
                        <?= htmlspecialchars($name) ?>
                    </span>
                </div>
                <div style="font-size:.75rem;font-weight:600;color:var(--text)">
                    <?= htmlspecialchars($name) ?>
                </div>
                <div style="font-size:.7rem;color:var(--text-muted)">
                    <?= $at ? date('d/m/Y H:i', strtotime($at)) : '' ?>
                </div>
                <?php if ($notes): ?>
                <div style="font-size:.7rem;color:var(--text-muted);margin-top:4px;font-style:italic">
                    "<?= htmlspecialchars($notes) ?>"
                </div>
                <?php endif; ?>
                <span class="badge badge-active" style="margin-top:8px">✓ Approved</span>

                <?php elseif ($approved === '0' || $approved === 0): ?>
                <div style="border-bottom:1px dashed var(--danger);padding-bottom:8px;
                            margin-bottom:8px;min-height:40px"></div>
                <span class="badge badge-absent">✗ Rejected</span>

                <?php else: ?>
                <div style="border-bottom:1px dashed var(--border);padding-bottom:8px;
                            margin-bottom:8px;min-height:40px;display:flex;align-items:flex-end;
                            justify-content:center">
                    <span style="color:var(--text-muted);font-size:.72rem">Awaiting signature</span>
                </div>
                <span class="badge badge-closed">Pending</span>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Approval / Rejection Form -->
<?php if ($canAct): ?>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:22px">

    <!-- Approve -->
    <div class="form-card" style="border-left:4px solid var(--success)">
        <h2 style="font-size:1rem;font-weight:700;color:var(--success);margin-bottom:16px">
            <i class="fas fa-check-circle" style="margin-right:8px"></i>
            Approve Claim
        </h2>
        <form method="POST"
              action="<?= BASE_URL ?>/approver/claims/<?= $claim['id'] ?>/approve"
              class="form">
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">
            <div class="form-group">
                <label>Your Full Name (as signature)</label>
                <input type="text" name="signatory_name" required
                       value="<?= htmlspecialchars($user['name']) ?>"
                       placeholder="Enter your full name">
            </div>
            <div class="form-group">
                <label>Notes (optional)</label>
                <textarea name="notes" rows="3"
                          placeholder="Any remarks or conditions..."></textarea>
            </div>
            <button type="submit" class="btn btn-success btn-full"
                    onclick="return confirm('Approve this claim? It will be forwarded to the next approver.')">
                <i class="fas fa-check"></i>
                Approve &amp; Forward to <?= htmlspecialchars($stageMap[$info['next']]['label'] ?? 'Accounts') ?>
            </button>
        </form>
    </div>

    <!-- Reject -->
    <div class="form-card" style="border-left:4px solid var(--danger)">
        <h2 style="font-size:1rem;font-weight:700;color:var(--danger);margin-bottom:16px">
            <i class="fas fa-times-circle" style="margin-right:8px"></i>
            Reject Claim
        </h2>
        <form method="POST"
              action="<?= BASE_URL ?>/approver/claims/<?= $claim['id'] ?>/reject"
              class="form">
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">
            <div class="form-group">
                <label>Your Full Name</label>
                <input type="text" name="signatory_name" required
                       value="<?= htmlspecialchars($user['name']) ?>">
            </div>
            <div class="form-group">
                <label>Reason for Rejection <span style="color:var(--danger)">*</span></label>
                <textarea name="rejection_reason" rows="3" required
                          placeholder="Explain why this claim is being rejected..."></textarea>
            </div>
            <button type="submit" class="btn btn-danger btn-full"
                    onclick="return confirm('Reject this claim? The lecturer will be notified.')">
                <i class="fas fa-times"></i> Reject Claim
            </button>
        </form>
    </div>

</div>
<?php elseif ($role === 'accounts' && $claim['current_stage'] === 'accounts'): ?>
<!-- Accounts office view-only + mark as processed -->
<div class="form-card" style="border-left:4px solid var(--info)">
    <h2 style="font-size:1rem;font-weight:700;color:var(--info);margin-bottom:16px">
        <i class="fas fa-university" style="margin-right:8px"></i>
        Accounts Office — Mark as Processed
    </h2>
    <form method="POST"
          action="<?= BASE_URL ?>/approver/claims/<?= $claim['id'] ?>/approve"
          class="form">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">
        <div class="form-row">
            <div class="form-group">
                <label>Processed by</label>
                <input type="text" name="signatory_name" required
                       value="<?= htmlspecialchars($user['name']) ?>">
            </div>
            <div class="form-group">
                <label>Notes</label>
                <input type="text" name="notes" placeholder="Payment reference, batch no. etc.">
            </div>
        </div>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-check"></i> Mark as Processed &amp; Complete
        </button>
    </form>
</div>
<?php endif; ?>