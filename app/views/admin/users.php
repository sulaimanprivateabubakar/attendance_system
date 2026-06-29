<?php $pageTitle = 'Manage Users'; ?>

<div class="page-header">
    <div>
        <h1>Users</h1>
        <p class="subtitle">All registered accounts</p>
    </div>
    <a href="<?= BASE_URL ?>/admin/users/create" class="btn btn-primary">+ Add User</a>
</div>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Name</th><th>Email</th><th>Role</th>
                <th>ID / Staff No</th><th>Department</th>
                <th>Status</th><th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($users as $u): ?>
            <tr>
                <td><?= htmlspecialchars($u['name']) ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td><span class="badge badge-<?= $u['role'] ?>"><?= $u['role'] ?></span></td>
                <td><?= htmlspecialchars($u['identifier'] ?? '–') ?></td>
                <td><?= htmlspecialchars($u['department'] ?? '–') ?></td>
                <td>
                    <span class="badge <?= $u['is_active'] ? 'badge-active' : 'badge-closed' ?>">
                        <?= $u['is_active'] ? 'Active' : 'Disabled' ?>
                    </span>
                </td>
                <td>
                    <?php if ($u['role'] !== 'admin'): ?>
                    <form method="POST" action="<?= BASE_URL ?>/admin/users/<?= $u['id'] ?>/toggle">
                        <input type="hidden" name="_csrf" value="<?= Auth::generateCsrfToken() ?>">
                        <button class="btn btn-sm btn-secondary">
                            <?= $u['is_active'] ? 'Disable' : 'Enable' ?>
                        </button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>