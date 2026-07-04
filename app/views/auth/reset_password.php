<?php $pageTitle = 'Set New Password'; ?>

<div class="auth-card">
    <h1>🔒 New Password</h1>
    <h2>Choose a strong password</h2>

    <form method="POST" action="<?= BASE_URL ?>/reset-password" class="form">
        <input type="hidden" name="_csrf"  value="<?= htmlspecialchars($csrf) ?>">
        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

        <div class="form-group">
            <label for="password">New Password</label>
            <input type="password" id="password" name="password"
                   required minlength="8" placeholder="Min 8 characters">
        </div>

        <div class="form-group">
            <label for="password_confirm">Confirm Password</label>
            <input type="password" id="password_confirm" name="password_confirm"
                   required minlength="8" placeholder="Repeat password">
        </div>

        <button type="submit" class="btn btn-primary btn-full">Set New Password</button>
    </form>

    <p class="auth-footer">
        <a href="<?= BASE_URL ?>/login">← Back to Login</a>
    </p>
</div>

<script>
document.querySelector('form').addEventListener('submit', function(e) {
    const p1 = document.getElementById('password').value;
    const p2 = document.getElementById('password_confirm').value;
    if (p1 !== p2) {
        e.preventDefault();
        alert('Passwords do not match.');
    }
});
</script>
