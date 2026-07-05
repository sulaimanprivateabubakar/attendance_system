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
                <select name="role" required id="roleSelect" onchange="toggleRegField()">
                    <option value="">– Select Role –</option>
                    <option value="student">Student</option>
                    <option value="lecturer">Lecturer</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
        </div>

        <!-- Student reg number — shown only for student role -->
        <div class="form-group" id="studentRegField" style="display:none">
            <label>Student Registration Number</label>
            <input type="text" name="student_number" id="studentNumberInput"
                   placeholder="e.g. 2024/CS/001">
            <div class="form-hint" style="margin-top:6px">
                <i class="fas fa-info-circle"></i>
                Enter the official registration number provided by the school.
            </div>
        </div>

        <!-- Staff number — shown only for lecturer role -->
        <div class="form-group" id="staffField" style="display:none">
            <label>Staff Number</label>
            <input type="text" name="staff_number" id="staffNumberInput"
                   placeholder="e.g. STF-001">
            <div class="form-hint" style="margin-top:6px">
                <i class="fas fa-info-circle"></i>
                Enter the official staff number.
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

        <div style="display:flex;gap:12px;margin-top:8px">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-user-plus"></i> Create User
            </button>
            <a href="<?= BASE_URL ?>/admin/users" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
function toggleRegField() {
    var role        = document.getElementById('roleSelect').value;
    var stuField    = document.getElementById('studentRegField');
    var staffField  = document.getElementById('staffField');
    var stuInput    = document.getElementById('studentNumberInput');
    var staffInput  = document.getElementById('staffNumberInput');

    stuField.style.display   = role === 'student'  ? 'block' : 'none';
    staffField.style.display = role === 'lecturer' ? 'block' : 'none';

    stuInput.required   = role === 'student';
    staffInput.required = role === 'lecturer';
}
</script>