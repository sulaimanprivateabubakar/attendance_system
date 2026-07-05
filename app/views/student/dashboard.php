<?php $pageTitle = 'Dashboard'; ?>

<div class="page-title">
    <div>
        <h1>My Dashboard</h1>
        <p>Welcome back, <?= htmlspecialchars($user['name']) ?> 👋</p>
    </div>
    <?php if (!empty($isClassRep)): ?>
    <span class="badge" style="background:rgba(245,158,11,.2);color:var(--warning);
                               font-size:.85rem;padding:8px 16px;border-radius:30px">
        ⭐ Class Rep — <?= htmlspecialchars($isClassRep['course_code']) ?>
    </span>
    <?php endif; ?>
</div>

<!-- ── Class Rep Pending Confirmations ─────────────────────── -->
<?php if (!empty($pendingConfirmations)): ?>
<div class="panel" style="margin-bottom:22px;border-left:4px solid var(--warning)">
    <div class="panel-header">
        <h2>
            <i class="fas fa-user-check" style="color:var(--warning);margin-right:8px"></i>
            Pending Manual Attendance Confirmations
        </h2>
        <span class="badge" style="background:rgba(245,158,11,.2);color:var(--warning)">
            <?= count($pendingConfirmations) ?> pending
        </span>
    </div>

    <div style="padding:16px 24px">
        <p style="font-size:.84rem;color:var(--text-muted);margin-bottom:16px">
            <i class="fas fa-info-circle" style="margin-right:6px"></i>
            As class rep for <strong><?= htmlspecialchars($isClassRep['course_name']) ?></strong>,
            review and confirm or reject these manual attendance requests submitted by your lecturer.
        </p>

        <?php foreach ($pendingConfirmations as $p): ?>
        <div style="display:flex;justify-content:space-between;align-items:center;
                    padding:16px 18px;background:rgba(255,255,255,.03);
                    border-radius:14px;margin-bottom:10px;
                    border:1px solid rgba(245,158,11,.2)">
            <div>
                <!-- Student info -->
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:6px">
                    <div style="width:38px;height:38px;border-radius:10px;
                                background:linear-gradient(135deg,var(--primary),var(--primary-light));
                                display:flex;align-items:center;justify-content:center;
                                font-weight:700;color:#fff;font-size:.85rem;flex-shrink:0">
                        <?= strtoupper(substr($p['student_name'], 0, 2)) ?>
                    </div>
                    <div>
                        <strong style="color:var(--text);font-size:.95rem">
                            <?= htmlspecialchars($p['student_name']) ?>
                        </strong>
                        <code style="margin-left:8px;font-size:.75rem;
                                     color:var(--text-muted);background:rgba(255,255,255,.06);
                                     padding:2px 8px;border-radius:6px">
                            <?= htmlspecialchars($p['student_number']) ?>
                        </code>
                    </div>
                </div>
                <!-- Session info -->
                <div style="font-size:.78rem;color:var(--text-muted);padding-left:50px">
                    <i class="fas fa-calendar" style="margin-right:4px"></i>
                    <?= htmlspecialchars($p['course_code']) ?>
                    &nbsp;·&nbsp; <?= htmlspecialchars($p['session_date']) ?>
                    &nbsp;·&nbsp; Submitted <?= date('H:i', strtotime($p['created_at'])) ?>
                </div>
            </div>

            <!-- Action buttons -->
            <div style="display:flex;gap:8px;flex-shrink:0;margin-left:16px">
                <form method="POST"
                      action="<?= BASE_URL ?>/student/confirm-attendance/<?= $p['id'] ?>">
                    <input type="hidden" name="_csrf"   value="<?= Auth::generateCsrfToken() ?>">
                    <input type="hidden" name="action"  value="confirm">
                    <button type="submit" class="btn btn-sm btn-success"
                            onclick="return confirm('Confirm attendance for <?= htmlspecialchars($p['student_name']) ?>?')">
                        <i class="fas fa-check"></i> Confirm
                    </button>
                </form>
                <form method="POST"
                      action="<?= BASE_URL ?>/student/confirm-attendance/<?= $p['id'] ?>">
                    <input type="hidden" name="_csrf"   value="<?= Auth::generateCsrfToken() ?>">
                    <input type="hidden" name="action"  value="reject">
                    <button type="submit" class="btn btn-sm btn-danger"
                            onclick="return confirm('Reject attendance for <?= htmlspecialchars($p['student_name']) ?>?')">
                        <i class="fas fa-times"></i> Reject
                    </button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- ── Stats ────────────────────────────────────────────────── -->
<?php
$totalAtt  = array_sum(array_column($courses, 'attended'));
$totalSess = array_sum(array_column($courses, 'total_sessions'));
$avgPct    = $totalSess > 0 ? round($totalAtt / $totalSess * 100, 1) : 0;
?>

<div class="stat-strip">
    <div class="stat-box">
        <span class="stat-num"><?= count($courses) ?></span>
        <span class="stat-label">Courses</span>
    </div>
    <div class="stat-box">
        <span class="stat-num text-success"><?= $totalAtt ?></span>
        <span class="stat-label">Attended</span>
    </div>
    <div class="stat-box">
        <span class="stat-num <?= $avgPct >= 75 ? 'text-success' : ($avgPct >= 50 ? 'text-warn' : 'text-danger') ?>">
            <?= $avgPct ?>%
        </span>
        <span class="stat-label">Avg Rate</span>
    </div>
    <div class="stat-box">
        <span class="stat-num"><?= count($recent) ?></span>
        <span class="stat-label">Recent Scans</span>
    </div>
</div>

<!-- ── Enrolled Courses ──────────────────────────────────────── -->
<div class="section">
    <div class="section-title">My Courses</div>
    <?php if (empty($courses)): ?>
    <div class="empty-state">
        <div class="empty-icon">📚</div>
        <p>You are not enrolled in any courses yet.</p>
        <p style="font-size:.8rem;color:var(--text-muted)">
            Contact your administrator to get enrolled.
        </p>
    </div>
    <?php else: ?>
    <div class="course-grid">
        <?php foreach ($courses as $c): ?>
        <?php
        $pct      = (float)($c['pct'] ?? 0);
        $barClass = $pct >= 75 ? 'success' : ($pct >= 50 ? 'warning' : 'danger');
        ?>
        <div class="course-card">
            <div class="course-card-header">
                <span class="course-code-badge"><?= htmlspecialchars($c['code']) ?></span>
                <span style="font-size:.72rem;color:var(--text-muted)">
                    Sem <?= $c['semester'] ?? '–' ?>
                </span>
            </div>
            <div class="course-name"><?= htmlspecialchars($c['name']) ?></div>
            <div class="course-lecturer">
                <i class="fas fa-chalkboard-teacher" style="margin-right:5px;color:var(--text-muted)"></i>
                <?= htmlspecialchars($c['lecturer_name']) ?>
            </div>

            <div class="att-bar-wrap">
                <div class="att-bar-label">
                    <span>Attendance</span>
                    <strong><?= $pct ?>%</strong>
                </div>
                <div class="progress">
                    <div class="progress-bar <?= $barClass ?>"
                         style="width:<?= min($pct, 100) ?>%"></div>
                </div>
                <div class="att-bar-note">
                    <?= (int)$c['attended'] ?> / <?= (int)$c['total_sessions'] ?> sessions attended
                </div>
            </div>

            <a href="<?= BASE_URL ?>/student/courses/<?= $c['id'] ?>"
               class="btn btn-secondary btn-sm">
                <i class="fas fa-history"></i> View History
            </a>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<!-- ── Recent Activity ───────────────────────────────────────── -->
<div class="section">
    <div class="section-title">Recent Activity</div>
    <?php if (empty($recent)): ?>
    <div class="empty-state">
        <div class="empty-icon">🔍</div>
        <p>No attendance records yet.</p>
    </div>
    <?php else: ?>
    <div class="panel">
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Session</th>
                        <th>Date</th>
                        <th>Time Scanned</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($recent as $row): ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($row['code']) ?></strong>
                        </td>
                        <td><?= htmlspecialchars($row['title'] ?? 'Class Session') ?></td>
                        <td><?= htmlspecialchars($row['session_date']) ?></td>
                        <td><?= date('H:i', strtotime($row['scanned_at'])) ?></td>
                        <td>
                            <span class="badge badge-<?= $row['status'] ?>">
                                <?= $row['status'] ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>