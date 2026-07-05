<?php $pageTitle = 'Register'; ?>

<div class="auth-card" style="max-width:520px">
    <div class="auth-logo">🎓</div>
    <h1>Create Account</h1>
    <h2>Register as a student</h2>

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
    <label>Registration Number</label>
    <input type="text" name="student_number" required
           placeholder="Enter your official reg number e.g. 2024/CS/001">
    <div class="form-hint" style="margin-top:6px;font-size:.75rem;color:var(--text-muted)">
        <i class="fas fa-info-circle"></i>
        Use the registration number provided by your institution.
    </div>
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
                <label>Phone <span style="font-weight:400;text-transform:none;letter-spacing:0">(optional)</span></label>
                <input type="text" name="phone" placeholder="+265 ...">
            </div>
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required
                   minlength="8" placeholder="Min 8 characters">
        </div>

        <button type="submit" class="btn btn-primary btn-full">
            <i class="fas fa-user-plus"></i> Create Account
        </button>
    </form>

    <p class="auth-footer">
        Already have an account?
        <a href="<?= BASE_URL ?>/login">Sign In</a>
    </p>
</div>