<?php $pageTitle = 'Create Session'; ?>

<div class="page-title">
    <div>
        <a href="<?= BASE_URL ?>/lecturer/sessions" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Sessions
        </a>
        <h1>Create New Session</h1>
        <p>A QR code will be generated automatically</p>
    </div>
</div>

<div class="form-card" style="max-width:640px">
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
            <label>Session Title <span style="font-weight:400;text-transform:none;letter-spacing:0">(optional)</span></label>
            <input type="text" name="title" placeholder="e.g. Week 3 – Lecture">
        </div>

        <div class="form-group">
            <label>Date</label>
            <input type="date" name="session_date" required value="<?= date('Y-m-d') ?>">
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

        <div class="form-hint">
            <i class="fas fa-info-circle"></i>
            QR code stays valid for <?= $_ENV['QR_EXPIRY_MINUTES'] ?? 120 ?> minutes from session creation.
        </div>

        <button type="submit" class="btn btn-primary btn-lg">
            <i class="fas fa-qrcode"></i> Generate Session & QR Code
        </button>
    </form>
</div>