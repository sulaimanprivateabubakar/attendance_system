<?php $pageTitle = 'Bulk Import'; ?>

<div class="page-title">
    <div>
        <h1>📥 Bulk Import</h1>
        <p>Upload CSV or XLSX files to import data automatically</p>
    </div>
    <a href="<?= BASE_URL ?>/admin/import/logs" class="btn btn-secondary">
        <i class="fas fa-history"></i> Import Logs
    </a>
</div>

<!-- How it works -->
<div class="panel" style="margin-bottom:22px">
    <div class="panel-header">
        <h2>
            <i class="fas fa-info-circle" style="color:var(--primary);margin-right:8px"></i>
            How It Works — Smart Auto-Enrollment
        </h2>
    </div>
    <div style="padding:20px 24px">
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;margin-bottom:20px">
            <div style="text-align:center;padding:14px;background:rgba(255,255,255,.03);
                        border-radius:12px;border:1px solid rgba(255,255,255,.06)">
                <div style="font-size:2rem;margin-bottom:8px">1️⃣</div>
                <strong style="color:var(--text);font-size:.9rem">Import Lecturers</strong>
                <p style="font-size:.75rem;color:var(--text-muted);margin-top:4px">
                    Add all teaching staff first
                </p>
            </div>
            <div style="text-align:center;padding:14px;background:rgba(255,255,255,.03);
                        border-radius:12px;border:1px solid rgba(255,255,255,.06)">
                <div style="font-size:2rem;margin-bottom:8px">2️⃣</div>
                <strong style="color:var(--text);font-size:.9rem">Import Courses</strong>
                <p style="font-size:.75rem;color:var(--text-muted);margin-top:4px">
                    Set department + year + semester per course
                </p>
            </div>
            <div style="text-align:center;padding:14px;background:rgba(255,255,255,.03);
                        border-radius:12px;border:1px solid rgba(255,255,255,.06)">
                <div style="font-size:2rem;margin-bottom:8px">3️⃣</div>
                <strong style="color:var(--text);font-size:.9rem">Import Students</strong>
                <p style="font-size:.75rem;color:var(--text-muted);margin-top:4px">
                    Students are auto-enrolled into matching courses
                </p>
            </div>
            <div style="text-align:center;padding:14px;background:rgba(255,255,255,.03);
                        border-radius:12px;border:1px solid rgba(255,255,255,.06)">
                <div style="font-size:2rem;margin-bottom:8px">✅</div>
                <strong style="color:var(--text);font-size:.9rem">Done!</strong>
                <p style="font-size:.75rem;color:var(--text-muted);margin-top:4px">
                    System ready — no manual enrollment needed
                </p>
            </div>
        </div>
        <div class="alert alert-success" style="margin:0">
            <i class="fas fa-magic"></i>
            <div>
                <strong>Smart Auto-Enrollment:</strong>
                When importing students, the system automatically enrolls them into all active courses
                that match their <strong>department + year of study + semester</strong>.
                No manual enrollment needed!
            </div>
        </div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:22px;margin-bottom:22px">

    <!-- Import Lecturers -->
    <div class="form-card">
        <div style="text-align:center;margin-bottom:20px">
            <div style="width:60px;height:60px;border-radius:16px;
                        background:rgba(6,182,212,.12);display:flex;align-items:center;
                        justify-content:center;font-size:1.8rem;margin:0 auto 12px">
                👨‍🏫
            </div>
            <h2 style="font-size:1rem;font-weight:700;color:var(--text)">Import Lecturers</h2>
            <p style="font-size:.78rem;color:var(--text-muted);margin-top:4px">
                Add all teaching staff at once
            </p>
        </div>

        <a href="<?= BASE_URL ?>/admin/import/template/lecturers"
           class="btn btn-secondary btn-full" style="margin-bottom:16px">
            <i class="fas fa-download"></i> Download Template
        </a>

        <div style="background:rgba(255,255,255,.03);border-radius:10px;
                    padding:12px;margin-bottom:16px;font-size:.76rem;color:var(--text-muted);
                    line-height:1.7">
            <strong style="color:var(--text-light)">Required:</strong>
            full_name, email, staff_number, password, department_code<br>
            <strong style="color:var(--text-light)">Optional:</strong> phone
        </div>

        <form method="POST" action="<?= BASE_URL ?>/admin/import/lecturers"
              enctype="multipart/form-data" class="form">
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Auth::generateCsrfToken()) ?>">
            <div class="form-group">
                <label>Select CSV or XLSX File</label>
                <input type="file" name="file" accept=".csv,.xlsx,.xls" required
                       style="cursor:pointer;color:var(--text-light)">
            </div>
            <button type="submit" class="btn btn-primary btn-full">
                <i class="fas fa-upload"></i> Import Lecturers
            </button>
        </form>
    </div>

    <!-- Import Courses -->
    <div class="form-card">
        <div style="text-align:center;margin-bottom:20px">
            <div style="width:60px;height:60px;border-radius:16px;
                        background:rgba(245,158,11,.12);display:flex;align-items:center;
                        justify-content:center;font-size:1.8rem;margin:0 auto 12px">
                📚
            </div>
            <h2 style="font-size:1rem;font-weight:700;color:var(--text)">Import Courses</h2>
            <p style="font-size:.78rem;color:var(--text-muted);margin-top:4px">
                Add courses with auto-enrollment rules
            </p>
        </div>

        <a href="<?= BASE_URL ?>/admin/import/template/courses"
           class="btn btn-secondary btn-full" style="margin-bottom:16px">
            <i class="fas fa-download"></i> Download Template
        </a>

        <div style="background:rgba(255,255,255,.03);border-radius:10px;
                    padding:12px;margin-bottom:16px;font-size:.76rem;color:var(--text-muted);
                    line-height:1.7">
            <strong style="color:var(--text-light)">Required:</strong>
            code, name, department_code, semester, year_of_study, academic_year<br>
            <strong style="color:var(--text-light)">Optional:</strong>
            staff_number, credit_hours
        </div>

        <form method="POST" action="<?= BASE_URL ?>/admin/import/courses"
              enctype="multipart/form-data" class="form">
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Auth::generateCsrfToken()) ?>">
            <div class="form-group">
                <label>Select CSV or XLSX File</label>
                <input type="file" name="file" accept=".csv,.xlsx,.xls" required
                       style="cursor:pointer;color:var(--text-light)">
            </div>
            <button type="submit" class="btn btn-primary btn-full">
                <i class="fas fa-upload"></i> Import Courses
            </button>
        </form>
    </div>

    <!-- Import Students -->
    <div class="form-card" style="border:1px solid rgba(22,163,74,.3)">
        <div style="text-align:center;margin-bottom:20px">
            <div style="width:60px;height:60px;border-radius:16px;
                        background:rgba(22,163,74,.12);display:flex;align-items:center;
                        justify-content:center;font-size:1.8rem;margin:0 auto 12px">
                🎓
            </div>
            <h2 style="font-size:1rem;font-weight:700;color:var(--text)">Import Students</h2>
            <p style="font-size:.78rem;color:var(--text-muted);margin-top:4px">
                Auto-enrolled into matching courses ✨
            </p>
        </div>

        <a href="<?= BASE_URL ?>/admin/import/template/students"
           class="btn btn-secondary btn-full" style="margin-bottom:16px">
            <i class="fas fa-download"></i> Download Template
        </a>

        <div style="background:rgba(22,163,74,.06);border-radius:10px;
                    padding:12px;margin-bottom:16px;font-size:.76rem;color:var(--text-muted);
                    line-height:1.7;border:1px solid rgba(22,163,74,.15)">
            <strong style="color:var(--success)">✨ Smart enrollment:</strong>
            Students are automatically enrolled into all courses matching
            their department + year + semester.<br><br>
            <strong style="color:var(--text-light)">Required:</strong>
            full_name, email, reg_number, password, department_code, year_of_study<br>
            <strong style="color:var(--text-light)">Optional:</strong>
            phone, semester (default: 1)
        </div>

        <form method="POST" action="<?= BASE_URL ?>/admin/import/students"
              enctype="multipart/form-data" class="form">
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Auth::generateCsrfToken()) ?>">
            <div class="form-group">
                <label>Select CSV or XLSX File</label>
                <input type="file" name="file" accept=".csv,.xlsx,.xls" required
                       style="cursor:pointer;color:var(--text-light)">
            </div>
            <button type="submit" class="btn btn-primary btn-full">
                <i class="fas fa-upload"></i> Import Students
            </button>
        </form>
    </div>

</div>

<!-- Recent Logs -->
<?php if (!empty($recentLogs)): ?>
<div class="panel">
    <div class="panel-header">
        <h2>
            <i class="fas fa-history" style="color:var(--primary);margin-right:8px"></i>
            Recent Imports
        </h2>
        <a href="<?= BASE_URL ?>/admin/import/logs" class="btn btn-sm btn-secondary">
            View All
        </a>
    </div>
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Type</th><th>File</th><th>Total</th>
                    <th>✅ OK</th><th>❌ Failed</th>
                    <th>By</th><th>Date</th><th>Errors</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($recentLogs as $log): ?>
                <tr>
                    <td>
                        <span class="badge <?= match($log['type']) {
                            'students'  => 'badge-student',
                            'lecturers' => 'badge-lecturer',
                            'courses'   => 'badge-pending',
                            default     => 'badge-closed'
                        } ?>">
                            <?= ucfirst($log['type']) ?>
                        </span>
                    </td>
                    <td style="font-size:.78rem;color:var(--text-muted)">
                        <?= htmlspecialchars($log['filename'] ?? '—') ?>
                    </td>
                    <td><?= (int)$log['total_rows'] ?></td>
                    <td><strong style="color:var(--success)"><?= (int)$log['success'] ?></strong></td>
                    <td>
                        <strong style="color:<?= $log['failed'] > 0 ? 'var(--danger)' : 'var(--text-muted)' ?>">
                            <?= (int)$log['failed'] ?>
                        </strong>
                    </td>
                    <td style="font-size:.8rem"><?= htmlspecialchars($log['imported_by']) ?></td>
                    <td style="font-size:.78rem;color:var(--text-muted)">
                        <?= date('M j, Y H:i', strtotime($log['created_at'])) ?>
                    </td>
                    <td>
                        <?php if ($log['errors']): ?>
                        <button class="btn btn-sm btn-secondary"
                                onclick="showErrors(<?= htmlspecialchars(json_encode($log['errors'])) ?>)">
                            <i class="fas fa-exclamation-triangle" style="color:var(--warning)"></i>
                        </button>
                        <?php else: ?>
                        <i class="fas fa-check" style="color:var(--success)"></i>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- Error Modal -->
<div id="errorModal" style="display:none;position:fixed;inset:0;
                             background:rgba(0,0,0,.75);z-index:99999;
                             align-items:center;justify-content:center">
    <div style="background:var(--card);border-radius:16px;padding:24px;
                max-width:600px;width:90%;max-height:80vh;overflow-y:auto;
                border:1px solid var(--border);box-shadow:0 20px 60px rgba(0,0,0,.5)">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
            <h3 style="color:var(--text)">
                <i class="fas fa-exclamation-triangle" style="color:var(--warning);margin-right:8px"></i>
                Import Errors
            </h3>
            <button onclick="closeErrors()"
                    style="background:none;border:none;color:var(--text-muted);
                           font-size:1.4rem;cursor:pointer;line-height:1">&times;</button>
        </div>
        <pre id="errorContent"
             style="font-size:.8rem;color:var(--danger);line-height:1.9;
                    white-space:pre-wrap;word-break:break-word;
                    background:rgba(239,68,68,.06);padding:12px;
                    border-radius:8px;border:1px solid rgba(239,68,68,.2)"></pre>
    </div>
</div>

<script>
function showErrors(errors) {
    document.getElementById('errorContent').textContent = errors;
    const modal = document.getElementById('errorModal');
    modal.style.display = 'flex';
}
function closeErrors() {
    document.getElementById('errorModal').style.display = 'none';
}
document.getElementById('errorModal').addEventListener('click', function(e) {
    if (e.target === this) closeErrors();
});
</script>