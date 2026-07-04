<?php $pageTitle = 'Attendance Result'; ?>

<?php
$cardClass = $success ? ($status === 'late' ? 'late' : 'success') : 'error';
$icon = match(true) {
    !$success          => '❌',
    $status === 'late' => '⏰',
    default            => '✅',
};
$title = match(true) {
    !$success          => 'Could Not Record Attendance',
    $status === 'late' => 'Marked Late',
    default            => 'Attendance Marked!',
};
?>

<div class="scan-result-wrap">
    <div class="scan-result-card <?= $cardClass ?>">

        <div class="result-icon-wrap"><?= $icon ?></div>

        <h1><?= $title ?></h1>
        <p class="result-msg"><?= htmlspecialchars($message) ?></p>

        <?php if ($success && !empty($sessionInfo)): ?>
        <div class="result-meta">
            <div class="result-meta-row">
                <span>Course</span>
                <strong><?= htmlspecialchars($sessionInfo['course_code']) ?></strong>
            </div>
            <div class="result-meta-row">
                <span>Session</span>
                <strong><?= htmlspecialchars($sessionInfo['title'] ?? 'Class Session') ?></strong>
            </div>
            <div class="result-meta-row">
                <span>Date</span>
                <strong><?= htmlspecialchars($sessionInfo['session_date']) ?></strong>
            </div>
            <div class="result-meta-row">
                <span>Status</span>
                <strong><span class="badge badge-<?= $status ?>"><?= strtoupper($status) ?></span></strong>
            </div>
        </div>
        <?php endif; ?>

        <a href="<?= BASE_URL ?>/student/dashboard" class="btn btn-primary btn-full">
            <i class="fas fa-th-large"></i> Go to Dashboard
        </a>

        <p class="scan-time">
            <i class="fas fa-clock" style="margin-right:4px"></i>
            Recorded at <?= date('H:i, M j Y') ?>
        </p>
    </div>
</div>