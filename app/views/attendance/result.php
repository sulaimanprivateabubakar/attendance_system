<?php $pageTitle = 'Attendance – Result'; ?>

<div class="result-card <?= $success ? 'result-success' : 'result-error' ?>">
    <?php if ($success): ?>
        <div class="result-icon"><?= $status === 'late' ? '⏰' : '✅' ?></div>
        <h1><?= $status === 'late' ? 'Marked Late' : 'Attendance Marked!' ?></h1>
        <p><?= htmlspecialchars($message) ?></p>
    <?php else: ?>
        <div class="result-icon">❌</div>
        <h1>Could Not Record Attendance</h1>
        <p><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <a href="/student/dashboard" class="btn btn-primary">Go to Dashboard</a>
</div>
