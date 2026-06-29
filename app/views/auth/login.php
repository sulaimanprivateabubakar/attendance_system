<?php $pageTitle = 'Login – QR Attendance'; ?>

<div class="auth-card">
    <h1>🎓 QR Attendance</h1>
    <h2>Sign In</h2>

    <form method="POST" action="<?= BASE_URL ?>/login" class="form">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">

        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" required
                   placeholder="you@university.edu"
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required
                   placeholder="••••••••">
        </div>

        <button type="submit" class="btn btn-primary btn-full">Sign In</button>
    </form>

    <p class="auth-footer">
        Don't have an account? <a href="<?= BASE_URL ?>/register">Register as Student</a>
    </p>
</div>