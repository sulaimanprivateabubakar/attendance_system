<?php $pageTitle = 'Reports'; ?>

<div class="page-header">
    <div>
        <h1>Attendance Reports</h1>
        <p class="subtitle">Export attendance data by course</p>
    </div>
</div>

<div class="form-card">
    <h2 style="margin-bottom:1rem">Export Report</h2>
    <form method="GET" action="<?= BASE_URL ?>/admin/reports/export" class="form">
        <div class="form-group">
            <label>Select Course</label>
            <select name="course_id" required>
                <option value="">– Select a Course –</option>
                <?php foreach ($courses as $c): ?>
                    <option value="<?= $c['id'] ?>">
                        <?= htmlspecialchars($c['code']) ?> – <?= htmlspecialchars($c['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Format</label>
            <select name="format">
                <option value="csv">CSV (Excel)</option>
                <option value="pdf">PDF / Print</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Export</button>
    </form>
</div>