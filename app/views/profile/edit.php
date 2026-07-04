<?php $pageTitle = 'My Profile'; ?>

<div class="page-title">
    <div>
        <h1>My Profile</h1>
        <p>Update your personal information and password</p>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:22px">

    <!-- Personal Info -->
    <div class="form-card">
        <h2 style="font-size:1rem;font-weight:600;margin-bottom:20px;color:var(--text)">
            <i class="fas fa-user" style="color:var(--primary);margin-right:8px"></i>
            Personal Information
        </h2>

        <form method="POST" action="<?= BASE_URL ?>/profile/update" class="form">
            <input type="hidden" name="_csrf"   value="<?= htmlspecialchars($csrf) ?>">
            <input type="hidden" name="action"  value="info">

            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" required
                       value="<?= htmlspecialchars($user['name']) ?>">
            </div>

            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" required
                       value="<?= htmlspecialchars($user['email']) ?>">
            </div>

            <?php if (Auth::isStudent() || Auth::isLecturer()): ?>
            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="phone"
                       value="<?= htmlspecialchars($profile['phone'] ?? '') ?>"
                       placeholder="+265 ...">
            </div>
            <?php endif; ?>

            <?php if (Auth::isStudent()): ?>
            <div class="form-group">
                <label>Student Number</label>
                <input type="text" value="<?= htmlspecialchars($profile['student_number'] ?? '') ?>"
                       disabled>
            </div>
            <div class="form-group">
                <label>Year of Study</label>
                <select name="year_of_study">
                    <?php for ($y = 1; $y <= 4; $y++): ?>
                        <option value="<?= $y ?>" <?= ($profile['year_of_study'] ?? 0) == $y ? 'selected' : '' ?>>
                            Year <?= $y ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            <?php endif; ?>

            <?php if (Auth::isLecturer()): ?>
            <div class="form-group">
                <label>Staff Number</label>
                <input type="text" value="<?= htmlspecialchars($profile['staff_number'] ?? '') ?>" disabled>
            </div>
            <?php endif; ?>

            <div class="form-group">
                <label>Department</label>
                <input type="text" value="<?= htmlspecialchars($profile['department_name'] ?? '–') ?>" disabled>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Save Changes
            </button>
        </form>
    </div>

    <!-- Change Password -->
    <div class="form-card">
        <h2 style="font-size:1rem;font-weight:600;margin-bottom:20px;color:var(--text)">
            <i class="fas fa-lock" style="color:var(--primary);margin-right:8px"></i>
            Change Password
        </h2>

        <form method="POST" action="<?= BASE_URL ?>/profile/update" class="form">
            <input type="hidden" name="_csrf"  value="<?= htmlspecialchars($csrf) ?>">
            <input type="hidden" name="action" value="password">

            <div class="form-group">
                <label>Current Password</label>
                <input type="password" name="current_password" required
                       placeholder="Your current password">
            </div>

            <div class="form-group">
                <label>New Password</label>
                <input type="password" name="new_password" required
                       minlength="8" placeholder="Min 8 characters">
            </div>

            <div class="form-group">
                <label>Confirm New Password</label>
                <input type="password" name="confirm_password" required
                       minlength="8" placeholder="Repeat new password">
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-key"></i> Change Password
            </button>
        </form>

        <!-- Account Info -->
        <div style="margin-top:24px;padding-top:20px;border-top:1px solid rgba(255,255,255,.06)">
            <h3 style="font-size:.8rem;font-weight:600;color:var(--text-muted);
                       text-transform:uppercase;letter-spacing:.06em;margin-bottom:14px">
                Account Info
            </h3>
            <table class="table">
                <tr>
                    <td style="color:var(--text-muted)">Role</td>
                    <td><span class="badge badge-<?= $user['role'] ?>"><?= $user['role'] ?></span></td>
                </tr>
                <tr>
                    <td style="color:var(--text-muted)">Member since</td>
                    <td><?= date('M j, Y', strtotime($userRecord['created_at'])) ?></td>
                </tr>
                <tr>
                    <td style="color:var(--text-muted)">Status</td>
                    <td><span class="badge badge-active">Active</span></td>
                </tr>
            </table>
        </div>
    </div>

</div>