<?php $pageTitle = 'Departments'; ?>

<div class="page-header">
    <div>
        <h1>Departments</h1>
        <p class="subtitle">Manage university departments</p>
    </div>
</div>

<div class="form-card" style="margin-bottom:2rem">
    <h2 style="margin-bottom:1rem">Add Department</h2>
    <form method="POST" action="<?= BASE_URL ?>/admin/departments/create" class="form">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">
        <div class="form-row">
            <div class="form-group">
                <label>Department Name</label>
                <input type="text" name="name" required placeholder="e.g. Computer Science">
            </div>
            <div class="form-group">
                <label>Code</label>
                <input type="text" name="code" required placeholder="e.g. CS">
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Add Department</button>
    </form>
</div>

<div class="card">
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
                <td><?= htmlspecialchars($d['name']) ?></td>
                <td><strong><?= htmlspecialchars($d['code']) ?></strong></td>
                <td><?= (int)$d['student_count'] ?></td>
                <td><?= (int)$d['lecturer_count'] ?></td>
                <td><?= (int)$d['course_count'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>