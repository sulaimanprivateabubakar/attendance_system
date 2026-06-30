<?php $pageTitle = 'Create Session'; ?>

<div class="page-header">
    <div>
        <a href="<?= BASE_URL ?>/lecturer/sessions" class="back-link">← Back to Sessions</a>
        <h1>Create New Session</h1>
        <p class="subtitle">A QR code will be generated automatically</p>
    </div>
</div>

<div class="form-card">
    <form method="POST" action="<?= BASE_URL ?>/lecturer/sessions/create" class="form">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">

        <div class="form-group">
            <label>Course</label>
            <select name="course_id" required>
                <option value="">– Select Course –</option>
                <?php foreach ($courses as $c): ?>
                    <option value="<?= $c['id'] ?>">
                        <?= htmlspecialchars($c['code']) ?> – <?= htmlspecialchars($c['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Session Title (optional)</label>
            <input type="text" name="title" placeholder="e.g. Week 3 – Lecture">
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Date</label>
                <input type="date" name="session_date" required
                       value="<?= date('Y-m-d') ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Start Time</label>
                <input type="time" name="start_time" required value="08:00">
            </div>
            <div class="form-group">
                <label>End Time</label>
                <input type="time" name="end_time" required value="10:00">
            </div>
        </div>

        <p class="form-hint">
            💡 The QR code will remain scannable for
            <?= $_ENV['QR_EXPIRY_MINUTES'] ?? 15 ?> minutes from session creation.
        </p>

        <button type="submit" class="btn btn-primary btn-large">
            Generate Session & QR Code
        </button>
    </form>
</div>