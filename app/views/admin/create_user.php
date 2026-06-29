<?php $pageTitle = 'Create User'; ?>

<div class="page-header">
    <div>
        <a href="<?= BASE_URL ?>/admin/users" class="back-link">← Back to Users</a>
        <h1>Create User</h1>
    </div>
</div>

<div class="form-card">
    <form method="POST" action="<?= BASE_URL ?>/admin/users/create" class="form">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">

        <div class="form-row">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" required placeholder="e.g. John Banda">
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" required placeholder="john@university.edu">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required minlength="8">
            </div>
            <div class="form-group">
                <label>Role</label>
                <select name="role" required>
                    <option value="">– Select Role –</option>
                    <option value="student">Student</option>
                    <option value="lecturer">Lecturer</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label>Department</label>
            <select name="department_id">
                <option value="">– None –</option>
                <?php foreach ($departments as $d): ?>
                    <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Create User</button>
        <a href="<?= BASE_URL ?>/admin/users" class="btn btn-secondary">Cancel</a>
    </form>
</div>