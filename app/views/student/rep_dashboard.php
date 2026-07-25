<?php $pageTitle = 'Class Rep Dashboard'; ?>

<div class="page-title">
    <div>
        <h1>⭐ Class Rep Dashboard</h1>
        <p>
            <?= htmlspecialchars($isClassRep['course_code']) ?>:
            <?= htmlspecialchars($isClassRep['course_name']) ?>
        </p>
    </div>
    <div style="display:flex;gap:10px;align-items:center">
        <span class="badge" style="background:rgba(245,158,11,.15);
                                   color:var(--warning);padding:8px 16px;
                                   font-size:.85rem">
            <?= count($pending) ?> Pending
        </span>
        <a href="<?= BASE_URL ?>/student/dashboard" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> My Dashboard
        </a>
    </div>
</div>

<!-- Stats -->
<div class="stat-strip" style="margin-bottom:22px">
    <div class="stat-box">
        <span class="stat-num text-warn"><?= count($pending) ?></span>
        <span class="stat-label">Awaiting Confirmation</span>
    </div>
    <div class="stat-box">
        <span class="stat-num text-success">
            <?= count(array_filter($confirmedToday, fn($r) => $r['status'] ?? '' !== 'rejected')) ?>
        </span>
        <span class="stat-label">Confirmed Today</span>
    </div>
    <div class="stat-box">
        <span class="stat-num text-danger">
            <?= count(array_filter($confirmedToday, fn($r) => ($r['status'] ?? '') === 'rejected')) ?>
        </span>
        <span class="stat-label">Rejected Today</span>
    </div>
</div>

<!-- Pending Confirmations -->
<div class="panel" style="margin-bottom:22px">
    <div class="panel-header">
        <h2>
            <i class="fas fa-clock" style="color:var(--warning);margin-right:8px"></i>
            Pending Confirmations
        </h2>
        <span class="badge" style="background:rgba(245,158,11,.15);color:var(--warning)">
            <?= count($pending) ?>
        </span>
    </div>

    <?php if (empty($pending)): ?>
    <div class="empty-state">
        <div class="empty-icon">✅</div>
        <p>No pending confirmations right now.</p>
        <p style="font-size:.8rem;color:var(--text-muted)">
            When your lecturer submits a manual attendance entry,
            it will appear here for your confirmation.
        </p>
    </div>
    <?php else: ?>
    <div style="padding:16px 24px">
        <p style="font-size:.84rem;color:var(--text-muted);margin-bottom:16px">
            <i class="fas fa-info-circle" style="margin-right:6px;color:var(--warning)"></i>
            Review each request carefully. Confirm only if the student was
            physically present in class.
        </p>

        <?php foreach ($pending as $p): ?>
        <div style="display:flex;justify-content:space-between;align-items:center;
                    padding:18px 20px;background:rgba(245,158,11,.05);
                    border-radius:14px;margin-bottom:12px;
                    border:1px solid rgba(245,158,11,.2)">
            <div style="display:flex;align-items:center;gap:14px">
                <!-- Avatar -->
                <div style="width:44px;height:44px;border-radius:12px;
                            background:linear-gradient(135deg,var(--primary),var(--primary-light));
                            display:flex;align-items:center;justify-content:center;
                            font-weight:700;color:#fff;font-size:.9rem;flex-shrink:0">
                    <?= strtoupper(substr($p['student_name'], 0, 2)) ?>
                </div>
                <div>
                    <strong style="color:var(--text);font-size:.95rem">
                        <?= htmlspecialchars($p['student_name']) ?>
                    </strong>
                    <code style="margin-left:8px;font-size:.75rem;
                                 color:var(--text-muted);
                                 background:rgba(255,255,255,.06);
                                 padding:2px 8px;border-radius:6px">
                        <?= htmlspecialchars($p['student_number']) ?>
                    </code>
                    <div style="font-size:.78rem;color:var(--text-muted);margin-top:4px">
                        <i class="fas fa-book" style="margin-right:4px"></i>
                        <?= htmlspecialchars($p['course_code']) ?>
                        &nbsp;·&nbsp;
                        <i class="fas fa-calendar" style="margin-right:4px"></i>
                        <?= htmlspecialchars($p['session_date']) ?>
                        &nbsp;·&nbsp;
                        <i class="fas fa-clock" style="margin-right:4px"></i>
                        Submitted <?= date('H:i', strtotime($p['created_at'])) ?>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div style="display:flex;gap:10px;flex-shrink:0;margin-left:16px">
                <form method="POST"
                      action="<?= BASE_URL ?>/student/confirm-attendance/<?= $p['id'] ?>">
                    <input type="hidden" name="_csrf"  value="<?= Auth::generateCsrfToken() ?>">
                    <input type="hidden" name="action" value="confirm">
                    <button type="submit" class="btn btn-success"
                            onclick="return confirm('Confirm attendance for <?= htmlspecialchars(addslashes($p['student_name'])) ?>?')">
                        <i class="fas fa-check"></i> Confirm
                    </button>
                </form>
                <form method="POST"
                      action="<?= BASE_URL ?>/student/confirm-attendance/<?= $p['id'] ?>">
                    <input type="hidden" name="_csrf"  value="<?= Auth::generateCsrfToken() ?>">
                    <input type="hidden" name="action" value="reject">
                    <button type="submit" class="btn btn-danger"
                            onclick="return confirm('Reject attendance for <?= htmlspecialchars(addslashes($p['student_name'])) ?>?')">
                        <i class="fas fa-times"></i> Reject
                    </button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Today's Processed -->
<?php if (!empty($confirmedToday)): ?>
<div class="panel">
    <div class="panel-header">
        <h2>
            <i class="fas fa-history" style="color:var(--primary);margin-right:8px"></i>
            Processed Today
        </h2>
        <span class="badge badge-count"><?= count($confirmedToday) ?></span>
    </div>
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Reg No.</th>
                    <th>Course</th>
                    <th>Session Date</th>
                    <th>Time</th>
                    <th>Decision</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($confirmedToday as $r): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($r['student_name']) ?></strong></td>
                    <td>
                        <code style="font-size:.78rem;color:var(--text-muted)">
                            <?= htmlspecialchars($r['student_number']) ?>
                        </code>
                    </td>
                    <td><?= htmlspecialchars($r['course_code']) ?></td>
                    <td><?= htmlspecialchars($r['session_date']) ?></td>
                    <td style="color:var(--text-muted);font-size:.8rem">
                        <?= date('H:i', strtotime($r['created_at'])) ?>
                    </td>
                    <td>
                        <span class="badge <?= ($r['status'] ?? '') === 'rejected' ? 'badge-absent' : 'badge-active' ?>">
                            <?= ucfirst($r['status'] ?? 'confirmed') ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>