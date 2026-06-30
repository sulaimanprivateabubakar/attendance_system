<?php $pageTitle = 'Manage Enrollment – ' . htmlspecialchars($course['code']); ?>

<div class="page-header">
    <div>
        <a href="<?= BASE_URL ?>/admin/courses" class="back-link">← Back to Courses</a>
        <h1><?= htmlspecialchars($course['code']) ?>: <?= htmlspecialchars($course['name']) ?></h1>
        <p class="subtitle">Manage student enrollment for this course</p>
    </div>
</div>

<div class="enroll-layout">

    <!-- ── Add Student Panel ────────────────────────────────────────── -->
    <div class="card">
        <h2 style="margin-bottom:1rem">Enroll a Student</h2>

        <div class="form-group">
            <label>Search by name, student number, or email</label>
            <input type="text" id="studentSearch" placeholder="Start typing..." autocomplete="off">
        </div>

        <div id="searchResults" class="search-results"></div>

        <p class="text-muted" id="searchHint">Type at least 2 characters to search.</p>
    </div>

    <!-- ── Currently Enrolled ───────────────────────────────────────── -->
    <div class="card">
        <h2 style="margin-bottom:1rem">
            Enrolled Students <span class="badge badge-count"><?= count($enrolled) ?></span>
        </h2>

        <?php if (empty($enrolled)): ?>
            <p class="empty">No students enrolled yet. Search above to add some.</p>
        <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Student No.</th>
                    <th>Email</th>
                    <th>Enrolled</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($enrolled as $i => $s): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= htmlspecialchars($s['name']) ?></td>
                    <td><?= htmlspecialchars($s['student_number']) ?></td>
                    <td><?= htmlspecialchars($s['email']) ?></td>
                    <td><?= htmlspecialchars(date('M j, Y', strtotime($s['enrolled_at']))) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

</div>

<script>
const courseId   = <?= (int)$course['id'] ?>;
const baseUrl    = '<?= BASE_URL ?>';
const csrfToken  = '<?= htmlspecialchars($csrf) ?>';
const searchBox  = document.getElementById('studentSearch');
const resultsBox = document.getElementById('searchResults');
const hintText   = document.getElementById('searchHint');

let debounceTimer;

searchBox.addEventListener('input', () => {
    clearTimeout(debounceTimer);
    const q = searchBox.value.trim();

    if (q.length < 2) {
        resultsBox.innerHTML = '';
        hintText.style.display = 'block';
        return;
    }

    hintText.style.display = 'none';

    debounceTimer = setTimeout(() => {
        fetch(`${baseUrl}/api/students.php?course_id=${courseId}&q=${encodeURIComponent(q)}`)
            .then(r => r.json())
            .then(data => {
                if (!data.students || data.students.length === 0) {
                    resultsBox.innerHTML = '<p class="empty">No matching students found (or already enrolled).</p>';
                    return;
                }

                resultsBox.innerHTML = data.students.map(s => `
                    <div class="search-result-row">
                        <div>
                            <strong>${escHtml(s.name)}</strong>
                            <span class="text-muted"> – ${escHtml(s.student_number)}</span>
                            <br><small class="text-muted">${escHtml(s.email)}</small>
                        </div>
                        <button class="btn btn-sm btn-primary" onclick="enrollStudent(${s.id}, this)">
                            + Enroll
                        </button>
                    </div>
                `).join('');
            })
            .catch(() => {
                resultsBox.innerHTML = '<p class="empty">Search failed. Try again.</p>';
            });
    }, 300);
});

function enrollStudent(studentId, btnEl) {
    btnEl.disabled = true;
    btnEl.textContent = 'Enrolling...';

    const form = new FormData();
    form.append('_csrf', csrfToken);
    form.append('student_id', studentId);

    fetch(`${baseUrl}/admin/courses/${courseId}/enroll`, {
        method: 'POST',
        body: form
    }).then(() => {
        // Reload to show updated enrolled list
        window.location.reload();
    }).catch(() => {
        btnEl.disabled = false;
        btnEl.textContent = '+ Enroll';
        alert('Failed to enroll student. Please try again.');
    });
}

function escHtml(str) {
    const div = document.createElement('div');
    div.appendChild(document.createTextNode(String(str)));
    return div.innerHTML;
}
</script>