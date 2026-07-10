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

    <!-- Email -->
    <div class="form-group">
        <label for="email">Email Address</label>
        <input type="email"
               id="email"
               name="email"
               required
               autocomplete="email"
               placeholder="you@university.edu"
               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
    </div>

    <!-- Password -->
    <div class="form-group">
        <label for="password">Password</label>

        <div class="password-wrapper">
            <input type="password"
                   id="password"
                   name="password"
                   required
                   autocomplete="current-password"
                   placeholder="Password">

            <button type="button" id="togglePassword" class="toggle-password">
                <i class="fas fa-eye"></i>
            </button>
        </div>

        <div class="forgot-password">
            <a href="<?= BASE_URL ?>/forgot-password">Forgot password?</a>
        </div>
    </div>

    <!-- Login Button -->
    <button type="submit" class="btn btn-primary btn-full">
        <i class="fas fa-sign-in-alt"></i> Sign In
    </button>
</form>
    <p class="auth-footer">
        Striving For Excellence
    </p>
</div>