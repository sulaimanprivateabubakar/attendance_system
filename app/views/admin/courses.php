<?php $pageTitle = 'Courses'; ?>

<div class="page-title">
    <div>
        <h1>Courses</h1>
        <p>All courses in the system</p>
    </div>
    <a href="<?= BASE_URL ?>/admin/courses/create" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add Course
    </a>
</div>

<div class="panel">
    <div class="panel-header">
        <h2><i class="fas fa-book" style="color:var(--primary);margin-right:8px"></i>All Courses</h2>
        <span class="badge badge-count"><?= count($courses) ?></span>
    </div>
    <div class="table-wrap">
        <?php if (empty($courses)): ?>
        <div class="empty-state">
            <div class="empty-icon">📖</div>
            <p>No courses yet.</p>
            <a href="<?= BASE_URL ?>/admin/courses/create" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create First Course
            </a>
        </div>
        <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Code</th><th>Name</th><th>Department</th>
                    <th>Lecturer</th><th>Enrolled</th>
                    <th>Status</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($courses as $c): ?>
                <tr>
                    <td>
                        <span class="course-code-badge"><?= htmlspecialchars($c['code']) ?></span>
                    </td>
                    <td><strong><?= htmlspecialchars($c['name']) ?></strong></td>
                    <td style="color:var(--text-muted)"><?= htmlspecialchars($c['dept_name'] ?? '–') ?></td>
                    <td><?= htmlspecialchars($c['lecturer_name'] ?? '–') ?></td>
                    <td>
                        <span class="badge badge-count"><?= (int)$c['enrolled'] ?></span>
                    </td>
                    <td>
                        <span class="badge <?= $c['is_active'] ? 'badge-active' : 'badge-closed' ?>">
                            <?= $c['is_active'] ? 'Active' : 'Inactive' ?>
                        </span>
                    </td>
                    <td>
                        <div style="display:flex;gap:8px;flex-wrap:wrap">
                            <a href="<?= BASE_URL ?>/admin/courses/<?= $c['id'] ?>/enrollment"
                               class="btn btn-sm btn-primary">
                                <i class="fas fa-user-plus"></i> Enroll
                            </a>
                            <form method="POST" action="<?= BASE_URL ?>/admin/courses/<?= $c['id'] ?>/toggle" style="display:inline">
                                <input type="hidden" name="_csrf" value="<?= Auth::generateCsrfToken() ?>">
                                <button class="btn btn-sm btn-secondary">
                                    <i class="fas fa-<?= $c['is_active'] ? 'ban' : 'check' ?>"></i>
                                    <?= $c['is_active'] ? 'Deactivate' : 'Activate' ?>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>