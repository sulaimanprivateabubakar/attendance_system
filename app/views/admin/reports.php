<?php $pageTitle = 'Reports'; ?>

<div class="page-title">
    <div>
        <h1>Attendance Reports</h1>
        <p>Filter, analyse and export attendance data</p>
    </div>
</div>

<!-- ── FILTER PANEL ─────────────────────────────────────────── -->
<div class="panel" style="margin-bottom:22px">
    <div class="panel-header">
        <h2><i class="fas fa-filter" style="color:var(--primary);margin-right:8px"></i>Filters</h2>
        <?php
        $hasFilter = $courseId || $deptId || $semester || $yearStudy
                  || $dateFrom !== date('Y-m-01') || $dateTo !== date('Y-m-d');
        ?>
        <?php if ($hasFilter): ?>
        <a href="<?= BASE_URL ?>/admin/reports" class="btn btn-sm btn-secondary">
            <i class="fas fa-times"></i> Clear Filters
        </a>
        <?php endif; ?>
    </div>
    <div style="padding:20px 24px">
        <form method="GET" action="<?= BASE_URL ?>/admin/reports" class="form">

            <!-- Date Range -->
            <div style="margin-bottom:18px">
                <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;
                            letter-spacing:.08em;color:var(--text-muted);margin-bottom:10px">
                    Date Range
                </div>
                <div style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap">
                    <div class="form-group" style="margin:0;flex:1;min-width:160px">
                        <label>From</label>
                        <input type="date" name="date_from"
                               value="<?= htmlspecialchars($dateFrom) ?>">
                    </div>
                    <div class="form-group" style="margin:0;flex:1;min-width:160px">
                        <label>To</label>
                        <input type="date" name="date_to"
                               value="<?= htmlspecialchars($dateTo) ?>">
                    </div>

                    <!-- Quick date buttons -->
                    <div style="display:flex;gap:6px;flex-wrap:wrap;padding-bottom:2px">
                        <?php
                        $quickRanges = [
                            'Today'      => [date('Y-m-d'), date('Y-m-d')],
                            'This Week'  => [date('Y-m-d', strtotime('monday this week')), date('Y-m-d')],
                            'This Month' => [date('Y-m-01'), date('Y-m-d')],
                            'Sem 1'      => [date('Y') . '-01-01', date('Y') . '-06-30'],
                            'Sem 2'      => [date('Y') . '-07-01', date('Y') . '-12-31'],
                            'This Year'  => [date('Y') . '-01-01', date('Y') . '-12-31'],
                        ];
                        foreach ($quickRanges as $label => [$from, $to]):
                        ?>
                        <a href="<?= BASE_URL ?>/admin/reports?date_from=<?= $from ?>&date_to=<?= $to ?><?= $courseId ? '&course_id='.$courseId : '' ?><?= $deptId ? '&dept_id='.$deptId : '' ?><?= $semester ? '&semester='.$semester : '' ?>"
                           class="btn btn-sm <?= ($dateFrom === $from && $dateTo === $to) ? 'btn-primary' : 'btn-secondary' ?>">
                            <?= $label ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Drill-down Filters -->
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));
                        gap:14px;margin-bottom:18px">

                <div class="form-group" style="margin:0">
                    <label>Department</label>
                    <select name="dept_id">
                        <option value="">All Departments</option>
                        <?php foreach ($departments as $d): ?>
                        <option value="<?= $d['id'] ?>"
                                <?= $deptId == $d['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($d['name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group" style="margin:0">
                    <label>Semester</label>
                    <select name="semester">
                        <option value="">All Semesters</option>
                        <option value="1" <?= $semester==1?'selected':'' ?>>Semester 1</option>
                        <option value="2" <?= $semester==2?'selected':'' ?>>Semester 2</option>
                    </select>
                </div>

                <div class="form-group" style="margin:0">
                    <label>Year of Study</label>
                    <select name="year_of_study">
                        <option value="">All Years</option>
                        <?php for ($y = 1; $y <= 4; $y++): ?>
                        <option value="<?= $y ?>" <?= $yearStudy==$y?'selected':'' ?>>
                            Year <?= $y ?>
                        </option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="form-group" style="margin:0">
                    <label>Course</label>
                    <select name="course_id">
                        <option value="">All Courses</option>
                        <?php foreach ($courses as $c): ?>
                        <option value="<?= $c['id'] ?>"
                                <?= $courseId==$c['id']?'selected':'' ?>>
                            <?= htmlspecialchars($c['code']) ?> — <?= htmlspecialchars($c['name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Apply Filters
            </button>

        </form>
    </div>
</div>

<!-- ── STATS ─────────────────────────────────────────────────── -->
<div class="stats" style="margin-bottom:22px">
    <div class="stat-card">
        <div class="stat-info">
            <h3>Total Sessions</h3>
            <h1 data-counter="<?= (int)($stats['total_sessions']??0) ?>">
                <?= (int)($stats['total_sessions']??0) ?>
            </h1>
            <small><?= htmlspecialchars($dateFrom) ?> — <?= htmlspecialchars($dateTo) ?></small>
        </div>
        <div class="stat-icon blue"><i class="fas fa-calendar-check"></i></div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <h3>Total Scans</h3>
            <h1 data-counter="<?= (int)($stats['total_scans']??0) ?>">
                <?= (int)($stats['total_scans']??0) ?>
            </h1>
            <small>Attendance records</small>
        </div>
        <div class="stat-icon cyan"><i class="fas fa-qrcode"></i></div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <h3>Present</h3>
            <h1 data-counter="<?= (int)($stats['present_count']??0) ?>">
                <?= (int)($stats['present_count']??0) ?>
            </h1>
            <small>On time</small>
        </div>
        <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <h3>Avg Rate</h3>
            <h1><?= number_format((float)($stats['avg_rate']??0), 1) ?>%</h1>
            <small>Across filtered courses</small>
        </div>
        <div class="stat-icon amber"><i class="fas fa-chart-line"></i></div>
    </div>
</div>

<!-- ── CHARTS ─────────────────────────────────────────────────── -->
<div class="charts-grid" style="margin-bottom:22px">

    <div class="chart-card">
        <div class="chart-title">
            <i class="fas fa-chart-bar" style="margin-right:6px;color:var(--primary)"></i>
            Attendance Rate by Course
        </div>
        <canvas id="courseChart" height="260"></canvas>
    </div>

    <div class="chart-card">
        <div class="chart-title">
            <i class="fas fa-chart-line" style="margin-right:6px;color:var(--primary)"></i>
            Daily Scan Trend
        </div>
        <canvas id="trendChart" height="260"></canvas>
    </div>

</div>

<div class="charts-grid" style="margin-bottom:22px">

    <div class="chart-card">
        <div class="chart-title">
            <i class="fas fa-chart-pie" style="margin-right:6px;color:var(--primary)"></i>
            Present vs Late vs Absent
        </div>
        <div style="max-width:260px;margin:0 auto">
            <canvas id="donutChart" height="260"></canvas>
        </div>
    </div>

    <div class="chart-card">
        <div class="chart-title">
            <i class="fas fa-building" style="margin-right:6px;color:var(--primary)"></i>
            Attendance by Department
        </div>
        <canvas id="deptChart" height="260"></canvas>
    </div>

</div>

<!-- ── COURSE TABLE ──────────────────────────────────────────── -->
<div class="panel" style="margin-bottom:22px">
    <div class="panel-header">
        <h2><i class="fas fa-table" style="color:var(--primary);margin-right:8px"></i>
            Course Breakdown
        </h2>
        <span class="badge badge-count"><?= count($courseStats) ?></span>
    </div>
    <div class="table-wrap">
        <?php if (empty($courseStats)): ?>
        <div class="empty-state">
            <div class="empty-icon">📊</div>
            <p>No data for selected filters and date range.</p>
        </div>
        <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Code</th><th>Course</th><th>Dept</th>
                    <th>Sem</th><th>Year</th><th>Lecturer</th>
                    <th>Sessions</th><th>Enrolled</th>
                    <th>Attended</th><th>Rate</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($courseStats as $cs): ?>
                <?php $rate = (float)($cs['avg_rate'] ?? 0); ?>
                <tr>
                    <td>
                        <span class="course-code-badge">
                            <?= htmlspecialchars($cs['code']) ?>
                        </span>
                    </td>
                    <td><?= htmlspecialchars($cs['course_name']) ?></td>
                    <td style="color:var(--text-muted);font-size:.8rem">
                        <?= htmlspecialchars($cs['dept_name'] ?? '—') ?>
                    </td>
                    <td style="color:var(--text-muted)">Sem <?= $cs['semester'] ?></td>
                    <td style="color:var(--text-muted)">
                        <?= $cs['year_of_study'] ? 'Year '.$cs['year_of_study'] : '—' ?>
                    </td>
                    <td style="color:var(--text-muted);font-size:.8rem">
                        <?= htmlspecialchars($cs['lecturer_name'] ?? '—') ?>
                    </td>
                    <td><?= (int)$cs['session_count'] ?></td>
                    <td><?= (int)$cs['enrolled_count'] ?></td>
                    <td><?= (int)$cs['total_attended'] ?></td>
                    <td>
                        <div style="display:flex;align-items:center;gap:8px;min-width:100px">
                            <div style="flex:1;background:rgba(255,255,255,.06);
                                        border-radius:99px;height:6px">
                                <div style="width:<?= min($rate,100) ?>%;height:6px;
                                            border-radius:99px;
                                            background:<?= $rate>=75?'var(--success)':($rate>=50?'var(--warning)':'var(--danger)') ?>">
                                </div>
                            </div>
                            <span style="font-size:.78rem;font-weight:600;
                                         color:<?= $rate>=75?'var(--success)':($rate>=50?'var(--warning)':'var(--danger)') ?>">
                                <?= number_format($rate, 1) ?>%
                            </span>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<!-- ── DEPARTMENT TABLE ──────────────────────────────────────── -->
<?php if (!empty($deptStats)): ?>
<div class="panel" style="margin-bottom:22px">
    <div class="panel-header">
        <h2><i class="fas fa-building" style="color:var(--primary);margin-right:8px"></i>
            Department Summary
        </h2>
    </div>
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Department</th><th>Code</th><th>Courses</th>
                    <th>Sessions</th><th>Students</th><th>Attended</th><th>Avg Rate</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($deptStats as $ds): ?>
                <?php $rate = (float)($ds['avg_rate'] ?? 0); ?>
                <tr>
                    <td><strong><?= htmlspecialchars($ds['dept_name']) ?></strong></td>
                    <td>
                        <span class="course-code-badge">
                            <?= htmlspecialchars($ds['dept_code']) ?>
                        </span>
                    </td>
                    <td><?= (int)$ds['course_count'] ?></td>
                    <td><?= (int)$ds['session_count'] ?></td>
                    <td><?= (int)$ds['student_count'] ?></td>
                    <td><?= (int)$ds['total_attended'] ?></td>
                    <td>
                        <span style="font-weight:700;
                                     color:<?= $rate>=75?'var(--success)':($rate>=50?'var(--warning)':'var(--danger)') ?>">
                            <?= number_format($rate,1) ?>%
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- ── EXPORT PANEL ──────────────────────────────────────────── -->
<div class="panel">
    <div class="panel-header">
        <h2><i class="fas fa-download" style="color:var(--primary);margin-right:8px"></i>
            Export Report
        </h2>
    </div>
    <div style="padding:24px">
        <p style="font-size:.84rem;color:var(--text-muted);margin-bottom:16px">
            Export will include all records matching the filters above.
            <strong style="color:var(--text)">
                Date range: <?= htmlspecialchars($dateFrom) ?> to <?= htmlspecialchars($dateTo) ?>
            </strong>
        </p>

        <form method="GET" action="<?= BASE_URL ?>/admin/reports/export">
            <!-- Pass all current filters -->
            <input type="hidden" name="date_from"    value="<?= htmlspecialchars($dateFrom) ?>">
            <input type="hidden" name="date_to"      value="<?= htmlspecialchars($dateTo) ?>">
            <input type="hidden" name="course_id"    value="<?= $courseId ?>">
            <input type="hidden" name="dept_id"      value="<?= $deptId ?>">
            <input type="hidden" name="semester"     value="<?= $semester ?>">
            <input type="hidden" name="year_of_study"value="<?= $yearStudy ?>">

            <div style="display:flex;gap:12px;align-items:center;flex-wrap:wrap">
                <div class="form-group" style="margin:0">
                    <label>Format</label>
                    <select name="format" style="padding:10px 14px;border-radius:10px;min-width:160px">
                        <option value="csv">CSV (Excel)</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary" style="margin-top:20px">
                    <i class="fas fa-download"></i>
                    Export <?= count($courseStats) > 0 ? '('.count($courseStats).' courses)' : '' ?>
                </button>
            </div>
        </form>

        <!-- Active filters summary -->
        <?php if ($hasFilter): ?>
        <div style="margin-top:16px;padding:12px 16px;background:rgba(22,163,74,.08);
                    border-radius:10px;border:1px solid rgba(22,163,74,.2);font-size:.8rem">
            <strong style="color:var(--success)">Active Filters:</strong>
            <?php if ($deptId): ?>
            <span style="margin-left:8px;background:rgba(255,255,255,.06);
                         padding:2px 8px;border-radius:6px">
                Dept: <?= htmlspecialchars($departments[array_search($deptId, array_column($departments,'id'))]['name'] ?? $deptId) ?>
            </span>
            <?php endif; ?>
            <?php if ($semester): ?>
            <span style="margin-left:6px;background:rgba(255,255,255,.06);
                         padding:2px 8px;border-radius:6px">
                Sem <?= $semester ?>
            </span>
            <?php endif; ?>
            <?php if ($yearStudy): ?>
            <span style="margin-left:6px;background:rgba(255,255,255,.06);
                         padding:2px 8px;border-radius:6px">
                Year <?= $yearStudy ?>
            </span>
            <?php endif; ?>
            <?php if ($courseId): ?>
            <span style="margin-left:6px;background:rgba(255,255,255,.06);
                         padding:2px 8px;border-radius:6px">
                Course: <?= htmlspecialchars($courses[array_search($courseId, array_column($courses,'id'))]['code'] ?? $courseId) ?>
            </span>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const isDark     = !document.body.classList.contains('light-mode');
const gridColor  = isDark ? 'rgba(255,255,255,.06)' : 'rgba(0,0,0,.06)';
const labelColor = isDark ? '#606060' : '#94a3b8';

Chart.defaults.font.family = 'Poppins, sans-serif';
Chart.defaults.color       = labelColor;

// ── Course bar chart
const courseLabels = <?= json_encode(array_column($courseStats, 'code')) ?>;
const courseRates  = <?= json_encode(array_map(fn($c) => round((float)($c['avg_rate']??0),1), $courseStats)) ?>;

new Chart(document.getElementById('courseChart'), {
    type: 'bar',
    data: {
        labels: courseLabels,
        datasets: [{
            label: 'Rate %',
            data: courseRates,
            backgroundColor: courseRates.map(r =>
                r >= 75 ? 'rgba(34,197,94,.7)' :
                r >= 50 ? 'rgba(245,158,11,.7)' :
                          'rgba(239,68,68,.7)'),
            borderRadius: 8,
            borderSkipped: false,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { color: gridColor } },
            y: { min: 0, max: 100, grid: { color: gridColor },
                 ticks: { callback: v => v + '%' } }
        }
    }
});

// ── Trend line chart
const trendLabels = <?= json_encode(array_column($dailyTrend, 'scan_date')) ?>;
const trendData   = <?= json_encode(array_map(fn($d) => (int)$d['scan_count'], $dailyTrend)) ?>;

new Chart(document.getElementById('trendChart'), {
    type: 'line',
    data: {
        labels: trendLabels.length ? trendLabels : ['No data'],
        datasets: [{
            label: 'Scans',
            data: trendData.length ? trendData : [0],
            borderColor: '#16a34a',
            backgroundColor: 'rgba(22,163,74,.1)',
            fill: true,
            tension: 0.4,
            pointRadius: 4,
            pointBackgroundColor: '#16a34a',
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { color: gridColor } },
            y: { beginAtZero: true, grid: { color: gridColor },
                 ticks: { stepSize: 1 } }
        }
    }
});

// ── Donut chart
new Chart(document.getElementById('donutChart'), {
    type: 'doughnut',
    data: {
        labels: ['Present', 'Late', 'Absent'],
        datasets: [{
            data: [
                <?= (int)($stats['present_count']??0) ?>,
                <?= (int)($stats['late_count']??0) ?>,
                <?= max(0, (int)($stats['total_scans']??0) - (int)($stats['present_count']??0) - (int)($stats['late_count']??0)) ?>
            ],
            backgroundColor: [
                'rgba(34,197,94,.8)',
                'rgba(245,158,11,.8)',
                'rgba(239,68,68,.8)',
            ],
            borderWidth: 0,
            hoverOffset: 6,
        }]
    },
    options: {
        responsive: true,
        cutout: '65%',
        plugins: {
            legend: { position: 'bottom', labels: { padding: 16 } }
        }
    }
});

// ── Department bar chart
const deptLabels = <?= json_encode(array_column($deptStats, 'dept_code')) ?>;
const deptRates  = <?= json_encode(array_map(fn($d) => round((float)($d['avg_rate']??0),1), $deptStats)) ?>;

new Chart(document.getElementById('deptChart'), {
    type: 'bar',
    data: {
        labels: deptLabels,
        datasets: [{
            label: 'Avg Rate %',
            data: deptRates,
            backgroundColor: 'rgba(22,163,74,.6)',
            borderRadius: 8,
            borderSkipped: false,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { color: gridColor } },
            y: { min: 0, max: 100, grid: { color: gridColor },
                 ticks: { callback: v => v + '%' } }
        }
    }
});
</script>