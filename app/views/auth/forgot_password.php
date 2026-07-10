<?php $pageTitle = 'Reset Password'; ?>

<div class="auth-card">
    <div class="auth-logo">🔒</div>
    <h1>Forgot Password</h1>
    <h2>Enter your email to get a reset link</h2>

    <form method="POST" action="<?= BASE_URL ?>/forgot-password" class="form">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">
        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" required
                   placeholder="you@university.edu">
        </div>
        <button type="submit" class="btn btn-primary btn-full">
            Send Reset Link
        </button>
    </form>

    <p class="auth-footer">
        <a href="<?= BASE_URL ?>/login">← Back to Login</a>
    </p>
</div>