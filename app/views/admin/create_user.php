<?php $pageTitle = 'Create User'; ?>

<div class="page-title">
    <div>
        <a href="<?= BASE_URL ?>/admin/users" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Users
        </a>
        <h1>Create User</h1>
        <p>Add a new user to the system</p>
    </div>
</div>

<div class="form-card" style="max-width:680px">
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
                <input type="password" name="password" required minlength="8" placeholder="Min 8 characters">
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

        <div class="form-hint">
            <i class="fas fa-info-circle"></i>
            Student/staff numbers are auto-generated. Users can update their profile later.
        </div>

        <div style="display:flex;gap:12px;margin-top:8px">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-user-plus"></i> Create User
            </button>
            <a href="<?= BASE_URL ?>/admin/users" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>