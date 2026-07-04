<?php $pageTitle = 'Reset Password'; ?>

<div class="auth-card">
    <h1>🔒 Reset Password</h1>
    <h2>Enter your email address</h2>

    <form method="POST" action="<?= BASE_URL ?>/forgot-password" class="form">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">

        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" required
                   placeholder="you@university.edu"
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>

        <button type="submit" class="btn btn-primary btn-full">Send Reset Link</button>
    </form>

    <p class="auth-footer">
        <a href="<?= BASE_URL ?>/login">← Back to Login</a>
    </p>
</div>
