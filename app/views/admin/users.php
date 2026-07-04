<?php $pageTitle = 'Users'; ?>

<?php
$filter = $_GET['filter'] ?? 'all';
$filterLabel = match($filter) {
    'student'  => 'Students',
    'lecturer' => 'Lecturers',
    'admin'    => 'Admins',
    default    => 'All Users'
};
?>

<div class="page-title">
    <div>
        <h1><?= $filterLabel ?></h1>
        <p>Manage registered accounts</p>
    </div>
    <a href="<?= BASE_URL ?>/admin/users/create" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add User
    </a>
</div>

<!-- Filter tabs -->
<div style="display:flex;gap:8px;margin-bottom:24px;flex-wrap:wrap">
    <a href="<?= BASE_URL ?>/admin/users"
       class="btn btn-sm <?= $filter === 'all' ? 'btn-primary' : 'btn-secondary' ?>">
        All
    </a>
    <a href="<?= BASE_URL ?>/admin/users?filter=student"
       class="btn btn-sm <?= $filter === 'student' ? 'btn-primary' : 'btn-secondary' ?>">
        <i class="fas fa-user-graduate"></i> Students
    </a>
    <a href="<?= BASE_URL ?>/admin/users?filter=lecturer"
       class="btn btn-sm <?= $filter === 'lecturer' ? 'btn-primary' : 'btn-secondary' ?>">
        <i class="fas fa-chalkboard-teacher"></i> Lecturers
    </a>
    <a href="<?= BASE_URL ?>/admin/users?filter=admin"
       class="btn btn-sm <?= $filter === 'admin' ? 'btn-primary' : 'btn-secondary' ?>">
        <i class="fas fa-shield-alt"></i> Admins
    </a>
</div>

<?php
// Filter users based on tab
$filtered = $filter === 'all'
    ? $users
    : array_filter($users, fn($u) => $u['role'] === $filter);
$filtered = array_values($filtered);
?>

<div class="panel">
    <div class="panel-header">
        <h2>
            <i class="fas fa-users" style="color:var(--primary);margin-right:8px"></i>
            <?= $filterLabel ?>
        </h2>
        <span class="badge badge-count"><?= count($filtered) ?></span>
    </div>
    <div class="table-wrap">
        <?php if (empty($filtered)): ?>
        <div class="empty-state">
            <div class="empty-icon">👥</div>
            <p>No <?= strtolower($filterLabel) ?> found.</p>
            <a href="<?= BASE_URL ?>/admin/users/create" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add User
            </a>
        </div>
        <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>ID / Staff No</th>
                    <th>Department</th>
                    <th>Joined</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($filtered as $i => $u): ?>
                <tr>
                    <td style="color:var(--text-muted)"><?= $i + 1 ?></td>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px">
                            <div style="width:36px;height:36px;border-radius:10px;
                                        background:linear-gradient(135deg,var(--primary),var(--primary-light));
                                        display:flex;align-items:center;justify-content:center;
                                        font-weight:700;font-size:.8rem;color:#fff;flex-shrink:0">
                                <?= strtoupper(substr($u['name'], 0, 2)) ?>
                            </div>
                            <div>
                                <strong style="color:var(--text)"><?= htmlspecialchars($u['name']) ?></strong>
                            </div>
                        </div>
                    </td>
                    <td style="color:var(--text-muted)"><?= htmlspecialchars($u['email']) ?></td>
                    <td><span class="badge badge-<?= $u['role'] ?>"><?= $u['role'] ?></span></td>
                    <td>
                        <code style="font-size:.78rem;color:var(--text-muted);
                                     background:rgba(255,255,255,.05);padding:3px 8px;
                                     border-radius:6px">
                            <?= htmlspecialchars($u['identifier'] ?? '–') ?>
                        </code>
                    </td>
                    <td style="color:var(--text-muted)"><?= htmlspecialchars($u['department'] ?? '–') ?></td>
                    <td style="color:var(--text-muted);font-size:.8rem">
                        <?= date('M j, Y', strtotime($u['created_at'])) ?>
                    </td>
                    <td>
                        <span class="badge <?= $u['is_active'] ? 'badge-active' : 'badge-closed' ?>">
                            <?= $u['is_active'] ? 'Active' : 'Disabled' ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($u['role'] !== 'admin'): ?>
                        <form method="POST"
                              action="<?= BASE_URL ?>/admin/users/<?= $u['id'] ?>/toggle"
                              style="display:inline">
                            <input type="hidden" name="_csrf" value="<?= Auth::generateCsrfToken() ?>">
                            <button type="submit" class="btn btn-sm btn-secondary">
                                <i class="fas fa-<?= $u['is_active'] ? 'ban' : 'check' ?>"></i>
                                <?= $u['is_active'] ? 'Disable' : 'Enable' ?>
                            </button>
                        </form>
                        <?php else: ?>
                        <span style="color:var(--text-muted);font-size:.78rem">–</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>