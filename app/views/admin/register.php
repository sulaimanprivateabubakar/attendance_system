<?php $pageTitle = 'Register – QR Attendance'; ?>

<div class="auth-card" style="max-width:520px">
    <h1>🎓 QR Attendance</h1>
    <h2>Student Registration</h2>

    <form method="POST" action="<?= BASE_URL ?>/register" class="form">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">

        <div class="form-row">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" required placeholder="Your full name">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required placeholder="you@university.edu">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Student Number</label>
                <input type="text" name="student_number" required placeholder="e.g. STU2024001">
            </div>
            <div class="form-group">
                <label>Year of Study</label>
                <select name="year_of_study" required>
                    <option value="">– Select –</option>
                    <option value="1">Year 1</option>
                    <option value="2">Year 2</option>
                    <option value="3">Year 3</option>
                    <option value="4">Year 4</option>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Department</label>
                <select name="department_id">
                    <option value="">– Select –</option>
                    <?php foreach ($departments as $d): ?>
                        <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Phone (optional)</label>
                <input type="text" name="phone" placeholder="+265 ...">
            </div>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required minlength="8" placeholder="Min 8 characters">
        </div>

        <button type="submit" class="btn btn-primary btn-full">Create Account</button>
    </form>

    <p class="auth-footer">
        Already have an account? <a href="<?= BASE_URL ?>/login">Sign In</a>
    </p>
</div>