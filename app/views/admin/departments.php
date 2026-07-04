<?php $pageTitle = 'Departments'; ?>

<div class="page-title">
    <div>
        <h1>Departments</h1>
        <p>Manage university departments</p>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1.5fr;gap:22px;align-items:start">

    <!-- Add Department -->
    <div class="form-card">
        <h2 style="font-size:1rem;font-weight:600;margin-bottom:20px">
            <i class="fas fa-plus-circle" style="color:var(--primary);margin-right:8px"></i>
            Add Department
        </h2>
        <form method="POST" action="<?= BASE_URL ?>/admin/departments/create" class="form">
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">
            <div class="form-group">
                <label>Department Name</label>
                <input type="text" name="name" required placeholder="e.g. Computer Science">
            </div>
            <div class="form-group">
                <label>Code</label>
                <input type="text" name="code" required placeholder="e.g. CS">
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Department
            </button>
        </form>
    </div>

    <!-- Departments Table -->
    <div class="panel">
        <div class="panel-header">
            <h2><i class="fas fa-building" style="color:var(--primary);margin-right:8px"></i>All Departments</h2>
            <span class="badge badge-count"><?= count($departments) ?></span>
        </div>
        <div class="table-wrap">
            <?php if (empty($departments)): ?>
            <div class="empty-state">
                <div class="empty-icon">🏛</div>
                <p>No departments yet.</p>
            </div>
            <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th><th>Code</th>
                        <th>Students</th><th>Lecturers</th><th>Courses</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($departments as $d): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($d['name']) ?></strong></td>
                        <td>
                            <span class="course-code-badge"><?= htmlspecialchars($d['code']) ?></span>
                        </td>
                        <td><?= (int)$d['student_count'] ?></td>
                        <td><?= (int)$d['lecturer_count'] ?></td>
                        <td><?= (int)$d['course_count'] ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>

</div>