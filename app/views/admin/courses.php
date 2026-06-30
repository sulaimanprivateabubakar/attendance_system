<?php $pageTitle = 'Manage Courses'; ?>

<div class="page-header">
    <div>
        <h1>Courses</h1>
        <p class="subtitle">All courses in the system</p>
    </div>
    <a href="<?= BASE_URL ?>/admin/courses/create" class="btn btn-primary">+ Add Course</a>
</div>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Code</th><th>Name</th><th>Department</th>
                <th>Lecturer</th><th>Enrolled</th><th>Status</th><th>Actions</th>
            </tr>
        </thead>
        <tbody>
 <?php foreach ($courses as $c): ?>
    <tr>
        <td><strong><?= htmlspecialchars($c['code']) ?></strong></td>
        <td><?= htmlspecialchars($c['name']) ?></td>
        <td><?= htmlspecialchars($c['dept_name'] ?? '–') ?></td>
        <td><?= htmlspecialchars($c['lecturer_name'] ?? '–') ?></td>
        <td><?= (int)$c['enrolled'] ?></td>
        <td>
            <span class="badge <?= $c['is_active'] ? 'badge-active' : 'badge-closed' ?>">
                <?= $c['is_active'] ? 'Active' : 'Inactive' ?>
            </span>
        </td>
        <td>
            <a href="<?= BASE_URL ?>/admin/courses/<?= $c['id'] ?>/enrollment" class="btn btn-sm btn-primary">Enroll</a>
            <form method="POST" action="<?= BASE_URL ?>/admin/courses/<?= $c['id'] ?>/toggle" style="display:inline">
                <input type="hidden" name="_csrf" value="<?= Auth::generateCsrfToken() ?>">
                <button class="btn btn-sm btn-secondary">
                    <?= $c['is_active'] ? 'Deactivate' : 'Activate' ?>
                </button>
            </form>
        </td>
    </tr>
<?php endforeach; ?>