<?php $pageTitle = 'Create Course'; ?>

<div class="page-header">
    <div>
        <a href="<?= BASE_URL ?>/admin/courses" class="back-link">← Back to Courses</a>
        <h1>Create Course</h1>
    </div>
</div>

<div class="form-card">
    <form method="POST" action="<?= BASE_URL ?>/admin/courses/create" class="form">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">

        <div class="form-row">
            <div class="form-group">
                <label>Course Code</label>
                <input type="text" name="code" required placeholder="e.g. CS101">
            </div>
            <div class="form-group">
                <label>Course Name</label>
                <input type="text" name="name" required placeholder="e.g. Introduction to Programming">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Department</label>
                <select name="department_id">
                    <option value="">– None –</option>
                    <?php foreach ($departments as $d): ?>
                        <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Lecturer</label>
                <select name="lecturer_id">
                    <option value="">– None –</option>
                    <?php foreach ($lecturers as $l): ?>
                        <option value="<?= $l['id'] ?>">
                            <?= htmlspecialchars($l['name']) ?> (<?= htmlspecialchars($l['staff_number']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Semester</label>
                <select name="semester">
                    <option value="1">Semester 1</option>
                    <option value="2">Semester 2</option>
                </select>
            </div>
            <div class="form-group">
                <label>Credit Hours</label>
                <input type="number" name="credit_hours" value="3" min="1" max="6">
            </div>
        </div>
        <div class="form-group">
            <label>Academic Year</label>
            <input type="text" name="academic_year" placeholder="e.g. 2024/2025">
        </div>

        <button type="submit" class="btn btn-primary">Create Course</button>
        <a href="<?= BASE_URL ?>/admin/courses" class="btn btn-secondary">Cancel</a>
    </form>
</div>