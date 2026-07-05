<?php $pageTitle = 'New Payment Claim'; ?>

<div class="page-title">
    <div>
        <a href="<?= BASE_URL ?>/lecturer/claims" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Claims
        </a>
        <h1>New Payment Claim</h1>
        <p>Part-Time Teaching Payment Claim Form</p>
    </div>
</div>

<!-- Month selector -->
<div class="form-card" style="margin-bottom:20px">
    <form method="GET" class="form">
        <div style="display:flex;gap:16px;align-items:flex-end">
            <div class="form-group" style="margin:0;flex:1">
                <label>Select Month</label>
                <input type="month" name="month" value="<?= htmlspecialchars($month) ?>">
            </div>
            <button type="submit" class="btn btn-secondary">
                <i class="fas fa-search"></i> Load Sessions
            </button>
        </div>
    </form>
</div>

<?php if (empty($courseSessions)): ?>
<div class="empty-state">
    <div class="empty-icon">📋</div>
    <p>No closed sessions found for <strong><?= date('F Y', strtotime($month . '-01')) ?></strong>.</p>
    <p style="font-size:.8rem;color:var(--text-muted)">Sessions must be closed to appear in claims.</p>
</div>
<?php else: ?>

<!-- Sessions preview -->
<div class="panel" style="margin-bottom:20px">
    <div class="panel-header">
        <h2><i class="fas fa-calendar-check" style="color:var(--primary);margin-right:8px"></i>
            Sessions for <?= date('F Y', strtotime($month . '-01')) ?>
        </h2>
    </div>
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Course</th>
                    <th>Sessions</th>
                    <th>Total Hours</th>
                    <th>Students</th>
                    <th>Date Range</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $grandTotal = 0;
            foreach ($courseSessions as $cs):
                $grandTotal += $cs['total_hours'];
            ?>
                <tr>
                    <td><span class="course-code-badge"><?= htmlspecialchars($cs['code']) ?></span></td>
                    <td><?= htmlspecialchars($cs['course_name']) ?></td>
                    <td><?= (int)$cs['session_count'] ?></td>
                    <td><strong><?= number_format($cs['total_hours'], 2) ?> hrs</strong></td>
                    <td><?= (int)$cs['enrolled_count'] ?></td>
                    <td style="color:var(--text-muted);font-size:.8rem">
                        <?= $cs['first_date'] ?> — <?= $cs['last_date'] ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" style="font-weight:700;color:var(--text)">TOTAL</td>
                    <td><strong style="color:var(--primary)"><?= number_format($grandTotal, 2) ?> hrs</strong></td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<!-- Claim form -->
<div class="form-card">
    <h2 style="font-size:1rem;font-weight:600;margin-bottom:20px;color:var(--text)">
        <i class="fas fa-user" style="color:var(--primary);margin-right:8px"></i>
        Part A — Claimant Details
    </h2>

    <form method="POST" action="<?= BASE_URL ?>/lecturer/claims/create" class="form">
        <input type="hidden" name="_csrf"  value="<?= htmlspecialchars($csrf) ?>">
        <input type="hidden" name="month" value="<?= htmlspecialchars($month) ?>">

        <!-- Auto-filled info display -->
        <div style="background:rgba(255,255,255,.03);border-radius:12px;
                    padding:16px;margin-bottom:20px;border:1px solid rgba(255,255,255,.06)">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;font-size:.85rem">
                <div>
                    <span style="color:var(--text-muted)">Name:</span>
                    <strong style="color:var(--text);margin-left:8px">
                        <?= htmlspecialchars($lecturer['name']) ?>
                    </strong>
                </div>
                <div>
                    <span style="color:var(--text-muted)">Staff ID:</span>
                    <strong style="color:var(--text);margin-left:8px">
                        <?= htmlspecialchars($lecturer['staff_number']) ?>
                    </strong>
                </div>
                <div>
                    <span style="color:var(--text-muted)">Department:</span>
                    <strong style="color:var(--text);margin-left:8px">
                        <?= htmlspecialchars($lecturer['dept_name'] ?? '—') ?>
                    </strong>
                </div>
                <div>
                    <span style="color:var(--text-muted)">Month:</span>
                    <strong style="color:var(--text);margin-left:8px">
                        <?= date('F Y', strtotime($month . '-01')) ?>
                    </strong>
                </div>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Academic Year</label>
                <input type="text" name="academic_year"
                       value="<?= date('Y') . '/' . (date('Y') + 1) ?>"
                       placeholder="e.g. 2025/2026" required>
            </div>
            <div class="form-group">
                <label>Designation</label>
                <select name="designation">
                    <option value="part_time">Part-time Staff</option>
                    <option value="full_time">Full-time Staff</option>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Telephone</label>
                <input type="text" name="telephone"
                       value="<?= htmlspecialchars($lecturer['phone'] ?? '') ?>"
                       placeholder="+265 ...">
            </div>
            <div class="form-group">
                <label>Hourly Rate (K)</label>
                <input type="number" name="hourly_rate" step="0.01" min="0"
                       placeholder="e.g. 5000" required>
            </div>
        </div>

        <div style="margin:20px 0;padding:16px;border-top:1px solid rgba(255,255,255,.06)">
            <h3 style="font-size:.9rem;font-weight:600;color:var(--text);margin-bottom:16px">
                <i class="fas fa-university" style="color:var(--primary);margin-right:8px"></i>
                Bank Details
            </h3>
            <div class="form-row">
                <div class="form-group">
                    <label>Bank Name</label>
                    <input type="text" name="bank_name" placeholder="e.g. National Bank">
                </div>
                <div class="form-group">
                    <label>Branch</label>
                    <input type="text" name="bank_branch" placeholder="e.g. Blantyre Branch">
                </div>
            </div>
            <div class="form-group">
                <label>Account Number</label>
                <input type="text" name="account_number" placeholder="e.g. 1234567890">
            </div>
        </div>

        <div class="form-group">
            <label>Further Information (optional)</label>
            <textarea name="notes" rows="3"
                      placeholder="Any additional notes..."></textarea>
        </div>

        <div style="display:flex;gap:12px;margin-top:8px">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Create Claim
            </button>
            <a href="<?= BASE_URL ?>/lecturer/claims" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php endif; ?>