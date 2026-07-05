<?php $pageTitle = 'Manual Attendance'; ?>

<div class="page-title">
    <div>
        <a href="<?= BASE_URL ?>/lecturer/sessions/<?= $session['id'] ?>/scan" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Scan Page
        </a>
        <h1>Manual Attendance</h1>
        <p><?= htmlspecialchars($session['course_code']) ?>: <?= htmlspecialchars($session['course_name']) ?>
           &nbsp;·&nbsp; <?= htmlspecialchars($session['session_date']) ?>
        </p>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:22px;align-items:start">

    <!-- Enter Reg Number -->
    <div class="form-card">
        <h2 style="font-size:1rem;font-weight:600;margin-bottom:6px;color:var(--text)">
            <i class="fas fa-keyboard" style="color:var(--primary);margin-right:8px"></i>
            Enter Registration Number
        </h2>

        <?php if ($classRep): ?>
        <div class="alert alert-info" style="margin-bottom:16px">
            <i class="fas fa-info-circle"></i>
            Class rep <strong><?= htmlspecialchars($classRep['name']) ?></strong>
            (<?= htmlspecialchars($classRep['student_number']) ?>) must confirm each entry.
        </div>
        <?php else: ?>
        <div class="alert alert-warning" style="margin-bottom:16px">
            <i class="fas fa-exclamation-triangle"></i>
            No class rep assigned. Attendance will be recorded directly without confirmation.
        </div>
        <?php endif; ?>

        <form method="POST" action="<?= BASE_URL ?>/lecturer/sessions/<?= $session['id'] ?>/manual"
              class="form">
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">

            <div class="form-group">
                <label>Student Registration Number</label>
                <input type="text" name="reg_number" required
                       placeholder="e.g. 2024/CS/001"
                       autofocus autocomplete="off"
                       style="font-size:1.1rem;padding:16px;letter-spacing:.05em">
            </div>

            <button type="submit" class="btn btn-primary btn-full">
                <i class="fas fa-user-check"></i>
                <?= $classRep ? 'Submit for Confirmation' : 'Record Attendance' ?>
            </button>
        </form>
    </div>

    <!-- Pending Confirmations -->
    <div class="panel">
        <div class="panel-header">
            <h2>
                <i class="fas fa-clock" style="color:var(--warning);margin-right:8px"></i>
                Pending Confirmation
            </h2>
            <span class="badge badge-count"><?= count($pending) ?></span>
        </div>
        <div class="table-wrap">
            <?php if (empty($pending)): ?>
            <div class="empty-state">
                <div class="empty-icon">✅</div>
                <p>No pending confirmations.</p>
            </div>
            <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Reg No</th>
                        <th>Time</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($pending as $p): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($p['student_name']) ?></strong></td>
                        <td><code style="font-size:.78rem;color:var(--text-muted)">
                            <?= htmlspecialchars($p['reg_number']) ?>
                        </code></td>
                        <td style="color:var(--text-muted);font-size:.8rem">
                            <?= date('H:i', strtotime($p['created_at'])) ?>
                        </td>
                        <td><span class="badge badge-pending">Pending</span></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
        <?php if (!empty($pending)): ?>
        <div style="padding:16px;border-top:1px solid rgba(255,255,255,.05);
                    font-size:.8rem;color:var(--text-muted);text-align:center">
            <i class="fas fa-info-circle"></i>
            Waiting for class rep to confirm on their device
        </div>
        <?php endif; ?>
    </div>

</div>