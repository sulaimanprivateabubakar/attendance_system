<?php $pageTitle = 'Enrollment — ' . htmlspecialchars($course['code']); ?>

<div class="page-title">
    <div>
        <a href="<?= BASE_URL ?>/admin/courses" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Courses
        </a>
        <h1><?= htmlspecialchars($course['code']) ?>: <?= htmlspecialchars($course['name']) ?></h1>
        <p>Manage student enrollment for this course</p>
    </div>
    <span class="badge badge-count" style="font-size:.85rem;padding:8px 16px">
        <?= count($enrolled) ?> Enrolled
    </span>
</div>

<div class="enroll-layout">

    <!-- Search & Add -->
    <div class="form-card">
        <h2 style="font-size:1rem;font-weight:600;margin-bottom:18px">
            <i class="fas fa-user-plus" style="color:var(--primary);margin-right:8px"></i>
            Enroll a Student
        </h2>

        <div class="form-group">
            <label>Search by name, student number or email</label>
            <input type="text" id="studentSearch"
                   placeholder="Start typing..." autocomplete="off">
        </div>

        <div id="searchResults"></div>
        <p class="form-hint" id="searchHint">
            <i class="fas fa-info-circle"></i>
            Type at least 2 characters to search.
        </p>
    </div>

    <!-- Enrolled Students -->
    <div class="panel">
        <div class="panel-header">
            <h2><i class="fas fa-users" style="color:var(--primary);margin-right:8px"></i>Enrolled Students</h2>
            <span class="badge badge-count"><?= count($enrolled) ?></span>
        </div>
        <div class="table-wrap">
            <?php if (empty($enrolled)): ?>
            <div class="empty-state">
                <div class="empty-icon">👥</div>
                <p>No students enrolled yet.</p>
            </div>
            <?php else: ?>
            <table class="table">
                <thead>
                    <tr><th>#</th><th>Name</th><th>Student No.</th><th>Email</th><th>Enrolled</th></tr>
                </thead>
                <tbody>
                <?php foreach ($enrolled as $i => $s): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px">
                                <div style="width:32px;height:32px;border-radius:8px;background:linear-gradient(135deg,var(--primary),var(--primary-light));display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.72rem;color:#fff;flex-shrink:0">
                                    <?= strtoupper(substr($s['name'],0,2)) ?>
                                </div>
                                <strong><?= htmlspecialchars($s['name']) ?></strong>
                            </div>
                        </td>
                        <td><code style="font-size:.78rem;color:var(--text-muted)"><?= htmlspecialchars($s['student_number']) ?></code></td>
                        <td style="color:var(--text-muted)"><?= htmlspecialchars($s['email']) ?></td>
                        <td style="color:var(--text-muted)"><?= date('M j, Y', strtotime($s['enrolled_at'])) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>

</div>

<script>
const courseId  = <?= (int)$course['id'] ?>;
const baseUrl   = '<?= BASE_URL ?>';
const csrfToken = '<?= htmlspecialchars($csrf) ?>';
const searchBox = document.getElementById('studentSearch');
const resultsEl = document.getElementById('searchResults');
const hintEl    = document.getElementById('searchHint');
let timer;

searchBox.addEventListener('input', () => {
    clearTimeout(timer);
    const q = searchBox.value.trim();
    if (q.length < 2) {
        resultsEl.innerHTML = '';
        hintEl.style.display = 'flex';
        return;
    }
    hintEl.style.display = 'none';
    timer = setTimeout(() => {
        fetch(`${baseUrl}/api/students.php?course_id=${courseId}&q=${encodeURIComponent(q)}`)
            .then(r => r.json())
            .then(data => {
                if (!data.students || !data.students.length) {
                    resultsEl.innerHTML = '<p class="empty">No matching students found or all already enrolled.</p>';
                    return;
                }
                resultsEl.innerHTML = data.students.map(s => `
                    <div class="search-result-row">
                        <div>
                            <strong>${esc(s.name)}</strong>
                            <span style="color:var(--text-muted);margin-left:6px">${esc(s.student_number)}</span>
                            <div style="font-size:.75rem;color:var(--text-muted)">${esc(s.email)}</div>
                        </div>
                        <button class="btn btn-sm btn-primary" onclick="enroll(${s.id}, this)">
                            <i class="fas fa-plus"></i> Enroll
                        </button>
                    </div>
                `).join('');
            })
            .catch(() => { resultsEl.innerHTML = '<p class="empty">Search failed. Try again.</p>'; });
    }, 300);
});

function enroll(studentId, btn) {
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enrolling...';
    const fd = new FormData();
    fd.append('_csrf', csrfToken);
    fd.append('student_id', studentId);
    fetch(`${baseUrl}/admin/courses/${courseId}/enroll`, { method: 'POST', body: fd })
        .then(() => location.reload())
        .catch(() => { btn.disabled = false; btn.innerHTML = '<i class="fas fa-plus"></i> Enroll'; });
}

function esc(s) {
    const d = document.createElement('div');
    d.appendChild(document.createTextNode(String(s)));
    return d.innerHTML;
}
</script>