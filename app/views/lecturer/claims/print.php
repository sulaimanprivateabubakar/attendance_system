<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Claim — <?= htmlspecialchars($claim['lecturer_name']) ?> — <?= date('F Y', strtotime($claim['month'] . '-01')) ?></title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #000;
            background: #fff;
            padding: 20px 30px;
        }

        /* Header */
        .form-header {
            text-align: right;
            margin-bottom: 12px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .form-header h1 {
            font-size: 15px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .form-header h2 {
            font-size: 13px;
            font-weight: 600;
        }

        .notice {
            font-size: 10.5px;
            line-height: 1.6;
            margin-bottom: 18px;
            border: 1px solid #ccc;
            padding: 10px;
            background: #fafafa;
        }

        /* Section headers */
        .section-title {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            margin: 16px 0 10px;
            border-bottom: 1.5px solid #000;
            padding-bottom: 4px;
        }

        /* Part A grid */
        .part-a {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px 20px;
            margin-bottom: 14px;
        }

        .field-row {
            display: flex;
            align-items: flex-end;
            gap: 6px;
            margin-bottom: 8px;
        }

        .field-label {
            font-size: 11.5px;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .field-line {
            flex: 1;
            border-bottom: 1px solid #000;
            min-height: 18px;
            font-size: 11.5px;
            padding-bottom: 1px;
        }

        /* Part B table */
        .claim-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .claim-table th {
            border: 1px solid #000;
            padding: 6px 8px;
            text-align: center;
            font-size: 11px;
            font-weight: 700;
            background: #f0f0f0;
        }

        .claim-table td {
            border: 1px solid #000;
            padding: 5px 8px;
            font-size: 11px;
            vertical-align: top;
        }

        .claim-table .empty-row td { height: 22px; }

        .claim-table tfoot td {
            font-weight: 700;
            background: #f5f5f5;
        }

        /* Part C */
        .part-c-row {
            display: flex;
            align-items: flex-end;
            margin-bottom: 10px;
            gap: 8px;
        }

        /* Signature rows */
        .sig-row {
            display: grid;
            grid-template-columns: 200px 1fr 180px;
            gap: 20px;
            margin-bottom: 16px;
            align-items: flex-end;
        }

        .sig-field {
            border-bottom: 1px solid #000;
            min-height: 30px;
        }

        .sig-label {
            font-size: 11px;
            color: #444;
            margin-top: 3px;
        }

        /* Print */
        @media print {
            body { padding: 10px 20px; }
            .no-print { display: none; }
            @page { margin: 1cm; }
        }
    </style>
</head>
<body>

<!-- Print button -->
<div class="no-print" style="margin-bottom:16px;display:flex;gap:10px">
    <button onclick="window.print()"
            style="padding:10px 20px;background:#2563eb;color:#fff;border:none;
                   border-radius:8px;cursor:pointer;font-size:13px;font-weight:600">
        🖨 Print / Save as PDF
    </button>
    <button onclick="window.close()"
            style="padding:10px 20px;background:#f1f5f9;color:#334155;border:1px solid #e2e8f0;
                   border-radius:8px;cursor:pointer;font-size:13px">
        ✕ Close
    </button>
</div>

<!-- Form Header -->
<div class="form-header">
    <h1>Part-Time Teaching Appointments</h1>
    <h2>Payment Claim Form</h2>
    <p style="font-size:11px;font-style:italic">A contract must be in place for staff claiming payments on this form</p>
</div>

<!-- Notice -->
<div class="notice">
    Part-time claims must be submitted for classes and lectures which have already been held. It is required that the claim
    form reach the Accounts Office on the 19th of the month so that it can be processed for payment on the 26th of the
    month. Claims submitted later than the 19th of the month will be paid on the 26th of the succeeding month. The
    Accounts Office will not be responsible for any inconvenience caused by late claims.
</div>

<!-- PART A -->
<div class="section-title">Part A — Details of Claimant</div>

<div class="part-a">
    <div>
        <div class="field-row">
            <span class="field-label">Name:</span>
            <span class="field-line"><?= htmlspecialchars($claim['lecturer_name']) ?></span>
        </div>
        <div class="field-row">
            <span class="field-label">Staff ID No. (If applicable):</span>
            <span class="field-line"><?= htmlspecialchars($claim['staff_number']) ?></span>
        </div>
        <div class="field-row">
            <span class="field-label">Faculty/Department:</span>
            <span class="field-line"><?= htmlspecialchars($claim['dept_name'] ?? '') ?></span>
        </div>
        <div class="field-row">
            <span class="field-label">Bank Account Details: Bank:</span>
            <span class="field-line"><?= htmlspecialchars($claim['bank_name'] ?? '') ?></span>
        </div>
        <div class="field-row" style="padding-left:20px">
            <span class="field-label">Branch:</span>
            <span class="field-line"><?= htmlspecialchars($claim['bank_branch'] ?? '') ?></span>
            <span class="field-label" style="margin-left:8px">A/c No.:</span>
            <span class="field-line"><?= htmlspecialchars($claim['account_number'] ?? '') ?></span>
        </div>
    </div>
    <div>
        <div class="field-row">
            <span class="field-label">Designation:</span>
            <span style="margin-left:6px;font-size:11px">
                Full time staff
                <span style="border:1px solid #000;display:inline-block;width:12px;height:12px;
                             text-align:center;line-height:11px;margin:0 4px">
                    <?= $claim['designation'] === 'full_time' ? '✓' : '' ?>
                </span>
                &nbsp; Part-time Staff
                <span style="border:1px solid #000;display:inline-block;width:12px;height:12px;
                             text-align:center;line-height:11px;margin:0 4px">
                    <?= $claim['designation'] === 'part_time' ? '✓' : '' ?>
                </span>
            </span>
        </div>
        <div class="field-row">
            <span class="field-label">Telephone No.:</span>
            <span class="field-line"><?= htmlspecialchars($claim['telephone'] ?? '') ?></span>
        </div>
        <div class="field-row">
            <span class="field-label">Academic Year:</span>
            <span class="field-line"><?= htmlspecialchars($claim['academic_year']) ?></span>
        </div>
    </div>
</div>

<!-- PART B -->
<div class="section-title">Part B — Details of Claim</div>

<table class="claim-table">
    <thead>
        <tr>
            <th style="width:80px">Module<br>Code</th>
            <th>Module Description</th>
            <th style="width:100px">Dates</th>
            <th style="width:60px">Total<br>Hours</th>
            <th style="width:80px">@ Hourly<br>Rate</th>
            <th style="width:90px">Amount<br>Due</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($courses as $c):
            $amount = $c['total_hours'] * $claim['hourly_rate'];
        ?>
        <tr>
            <td><?= htmlspecialchars($c['code']) ?></td>
            <td><?= htmlspecialchars($c['course_name']) ?></td>
            <td style="font-size:10px">
                <?= $c['first_date'] ?><br>to<br><?= $c['last_date'] ?>
            </td>
            <td style="text-align:center"><?= number_format($c['total_hours'], 2) ?></td>
            <td style="text-align:right">K <?= number_format($claim['hourly_rate'], 2) ?></td>
            <td style="text-align:right">K <?= number_format($amount, 2) ?></td>
        </tr>
        <?php endforeach; ?>

        <!-- Empty rows to match physical form -->
        <?php for ($i = count($courses); $i < 7; $i++): ?>
        <tr class="empty-row">
            <td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td>
        </tr>
        <?php endfor; ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="5" style="text-align:right;padding-right:10px">Total Payment</td>
            <td style="text-align:right">K <?= number_format($data['totalAmount'], 2) ?></td>
        </tr>
    </tfoot>
</table>

<div class="field-row">
    <span class="field-label">Further information if applicable:</span>
    <span class="field-line" style="min-height:40px">
        <?= htmlspecialchars($claim['notes'] ?? '') ?>
    </span>
</div>

<p style="font-size:10.5px;font-style:italic;margin:14px 0 20px">
    For each module listed on this Claim Form, please attach a class attendance sheet, indicating the hours taught
    and duly signed by the class representative.
</p>

<!-- Signature of Claimant -->
<div style="display:grid;grid-template-columns:1fr 1fr;gap:40px;margin-bottom:30px">
    <div>
        <div style="border-bottom:1px solid #000;height:35px"></div>
        <div class="sig-label">Signature of Claimant</div>
    </div>
    <div>
        <div style="border-bottom:1px solid #000;height:35px"></div>
        <div class="sig-label">Date (DD/MM/YYYY)</div>
    </div>
</div>

<!-- PART C -->
<div class="section-title">Part C — Claim Summary <em style="font-weight:400;font-size:11px">(To be filled by Head of Department)</em></div>

<div style="margin-bottom:16px">
    <div class="part-c-row">
        <span class="field-label" style="width:260px">No. of Students Enrolled</span>
        <span class="field-line" style="width:180px"><?= (int)$data['totalStudents'] ?></span>
    </div>
    <div class="part-c-row">
        <span class="field-label" style="width:260px">Lecturer's Course Load</span>
        <span class="field-line" style="width:180px"><?= number_format($data['totalHours'], 2) ?> hours</span>
    </div>
    <div class="part-c-row">
        <span class="field-label" style="width:260px">Lecturer's Overload Hours Applicable<br><em>(For Full-time Lecturers only)</em></span>
        <span class="field-line" style="width:180px"></span>
    </div>
</div>

<div class="sig-row">
    <div>
        <div class="sig-field"></div>
        <div class="sig-label">Head of Department</div>
    </div>
    <div>
        <div class="sig-field"></div>
        <div class="sig-label">Signature</div>
    </div>
    <div>
        <div class="sig-field"></div>
        <div class="sig-label">Date (DD/MM/YYYY)</div>
    </div>
</div>

<!-- PART D -->
<div class="section-title">Part D — Approving Officers</div>

<?php
$officers = ['Head of Academics', 'Registrar', 'Vice Chancellor/Principal'];
foreach ($officers as $officer):
?>
<div class="sig-row">
    <div>
        <div class="sig-field"></div>
        <div class="sig-label"><?= $officer ?></div>
    </div>
    <div>
        <div class="sig-field"></div>
        <div class="sig-label">Signature</div>
    </div>
    <div>
        <div class="sig-field"></div>
        <div class="sig-label">Date (DD/MM/YYYY)</div>
    </div>
</div>
<?php endforeach; ?>

<!-- PART E -->
<div class="section-title">Part E — Accounts Payable <em style="font-weight:400;font-size:11px">(To be filled by Accounts Office)</em></div>

<div style="margin-bottom:16px">
    <div class="part-c-row">
        <span class="field-label" style="width:200px">Payable No. of Hours</span>
        <span class="field-line" style="width:200px"></span>
    </div>
    <div class="part-c-row">
        <span class="field-label" style="width:200px">Hourly Rate</span>
        <span style="margin-right:4px">K</span>
        <span class="field-line" style="width:200px"></span>
    </div>
    <div class="part-c-row">
        <span class="field-label" style="width:200px"><strong>TOTAL</strong></span>
        <span style="margin-right:4px">K</span>
        <span class="field-line" style="width:200px"></span>
    </div>
</div>

<div style="border-top:2px solid #000;margin:20px 0;padding-top:16px">
    <div class="sig-row">
        <div>
            <div class="sig-field"></div>
            <div class="sig-label">Prepared by <strong>ASSIST. ACCOUNTANT</strong></div>
        </div>
        <div>
            <div class="sig-field"></div>
            <div class="sig-label">Signature</div>
        </div>
        <div>
            <div class="sig-field"></div>
            <div class="sig-label">Date (DD/MM/YYYY)</div>
        </div>
    </div>
    <div class="sig-row" style="margin-top:12px">
        <div>
            <div class="sig-field"></div>
            <div class="sig-label">Checked by <strong>ACCOUNTANT</strong></div>
        </div>
        <div>
            <div class="sig-field"></div>
            <div class="sig-label">Signature</div>
        </div>
        <div>
            <div class="sig-field"></div>
            <div class="sig-label">Date (DD/MM/YYYY)</div>
        </div>
    </div>
</div>

<!-- Auto-generated notice -->
<div style="margin-top:20px;padding:10px;background:#f9f9f9;border:1px solid #ddd;
            font-size:10px;color:#666;text-align:center" class="no-print">
    Generated by QR Attendance System on <?= date('d/m/Y H:i') ?> |
    <?= htmlspecialchars($claim['lecturer_name']) ?> — <?= date('F Y', strtotime($claim['month'] . '-01')) ?>
</div>

</body>
</html>