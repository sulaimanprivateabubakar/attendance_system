<?php $pageTitle = 'Sign In'; ?>

<div class="auth-card">
    <div class="auth-logo">
    <img src="<?= BASE_URL ?>/assets/images/logo.webp"
         alt="IQRA'A e-Attendance Logo">
</div>
    <h1>IQRA'A e-ATTENDANCE</h1>
    <h2>Sign in to your account</h2>

    <?php if (!empty($flash)): ?>
    <div class="alert alert-<?= $flash['type'] ?>">
        <?= htmlspecialchars($flash['message']) ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="<?= BASE_URL ?>/login" class="form">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">

        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" required
                   autocomplete="email" placeholder="you@university.edu"
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label style="display:flex;justify-content:space-between;text-transform:none;letter-spacing:0">
                <span style="text-transform:uppercase;letter-spacing:.06em;font-size:.75rem">Password</span>
                <a href="<?= BASE_URL ?>/forgot-password" style="font-size:.78rem;color:var(--primary-light);font-weight:500">
                    Forgot password?
                </a>
            </label>
            <input type="password" id="password" name="password" required
                   autocomplete="current-password" placeholder="Password">
        </div>

        <button type="submit" class="btn btn-primary btn-full" style="margin-top:8px">
            <i class="fas fa-sign-in-alt"></i> Sign In
        </button>
    </form>

    <p class="auth-footer">
        Don't have an account?
        <a href="<?= BASE_URL ?>/register">Register as student</a>
    </p>
</div>