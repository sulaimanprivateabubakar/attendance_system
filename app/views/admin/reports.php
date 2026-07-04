<?php $pageTitle = 'Reports'; ?>

<div class="page-title">
    <div>
        <h1>Attendance Reports</h1>
        <p>Visual analytics and data export</p>
    </div>
</div>

<!-- Stats -->
<div class="stats">
    <div class="stat-card">
        <div class="stat-info">
            <h3>Total Sessions</h3>
            <h1 data-counter="<?= (int)$stats['total_sessions'] ?>"><?= (int)$stats['total_sessions'] ?></h1>
            <small>Closed sessions</small>
        </div>
        <div class="stat-icon blue"><i class="fas fa-calendar-check"></i></div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <h3>Total Scans</h3>
            <h1 data-counter="<?= (int)$stats['total_scans'] ?>"><?= (int)$stats['total_scans'] ?></h1>
            <small>Attendance records</small>
        </div>
        <div class="stat-icon cyan"><i class="fas fa-qrcode"></i></div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <h3>Present</h3>
            <h1 data-counter="<?= (int)$stats['present_count'] ?>"><?= (int)$stats['present_count'] ?></h1>
            <small class="success">On time</small>
        </div>
        <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <h3>Avg Rate</h3>
            <h1><?= number_format($stats['avg_rate'], 1) ?>%</h1>
            <small>Across all courses</small>
        </div>
        <div class="stat-icon amber"><i class="fas fa-chart-line"></i></div>
    </div>
</div>

<!-- Charts -->
<div class="charts-grid">
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
            Daily Scan Trend (Last 14 Days)
        </div>
        <canvas id="trendChart" height="260"></canvas>
    </div>
</div>

<div class="charts-grid">
    <div class="chart-card">
        <div class="chart-title">
            <i class="fas fa-chart-pie" style="margin-right:6px;color:var(--primary)"></i>
            Overall Breakdown
        </div>
        <div style="max-width:260px;margin:0 auto">
            <canvas id="donutChart" height="260"></canvas>
        </div>
    </div>

    <!-- Export + Summary -->
    <div class="chart-card">
        <div class="chart-title">
            <i class="fas fa-download" style="margin-right:6px;color:var(--primary)"></i>
            Export Report
        </div>
        <form method="GET" action="<?= BASE_URL ?>/admin/reports/export" class="form">
            <div class="form-group">
                <label>Select Course</label>
                <select name="course_id" required>
                    <option value="">– Select a Course –</option>
                    <?php foreach ($courses as $c): ?>
                        <option value="<?= $c['id'] ?>">
                            <?= htmlspecialchars($c['code']) ?> – <?= htmlspecialchars($c['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Format</label>
                <select name="format">
                    <option value="csv">📊 CSV (Excel)</option>
                    <option value="pdf">🖨 PDF / Print</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-download"></i> Export
            </button>
        </form>

        <?php if (!empty($courseStats)): ?>
        <div style="margin-top:20px">
            <div class="chart-title" style="margin-bottom:12px">Course Summary</div>
            <table class="table">
                <thead>
                    <tr><th>Course</th><th>Sessions</th><th>Rate</th></tr>
                </thead>
                <tbody>
                <?php foreach ($courseStats as $cs): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($cs['code']) ?></strong></td>
                        <td><?= (int)$cs['session_count'] ?></td>
                        <td>
                            <span class="<?= $cs['avg_rate'] >= 75 ? 'text-success' : ($cs['avg_rate'] >= 50 ? 'text-warn' : 'text-danger') ?>">
                                <?= number_format($cs['avg_rate'], 1) ?>%
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const isDark = !document.body.classList.contains('light-mode');
const gridColor  = isDark ? 'rgba(255,255,255,.06)' : 'rgba(0,0,0,.06)';
const labelColor = isDark ? '#94a3b8' : '#64748b';

Chart.defaults.font.family = 'Poppins, sans-serif';
Chart.defaults.color = labelColor;

const courseLabels = <?= json_encode(array_column($courseStats, 'code')) ?>;
const courseRates  = <?= json_encode(array_map(fn($c) => round((float)$c['avg_rate'], 1), $courseStats)) ?>;

new Chart(document.getElementById('courseChart'), {
    type: 'bar',
    data: {
        labels: courseLabels,
        datasets: [{
            label: 'Attendance %',
            data: courseRates,
            backgroundColor: courseRates.map(r =>
                r >= 75 ? 'rgba(34,197,94,.7)' :
                r >= 50 ? 'rgba(245,158,11,.7)' :
                          'rgba(239,68,68,.7)'
            ),
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

const trendLabels = <?= json_encode(array_column($dailyTrend, 'scan_date')) ?>;
const trendData   = <?= json_encode(array_map(fn($d) => (int)$d['scan_count'], $dailyTrend)) ?>;

new Chart(document.getElementById('trendChart'), {
    type: 'line',
    data: {
        labels: trendLabels,
        datasets: [{
            label: 'Scans',
            data: trendData,
            borderColor: '#2563eb',
            backgroundColor: 'rgba(37,99,235,.1)',
            fill: true,
            tension: 0.4,
            pointRadius: 4,
            pointBackgroundColor: '#2563eb',
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { color: gridColor } },
            y: { beginAtZero: true, grid: { color: gridColor }, ticks: { stepSize: 1 } }
        }
    }
});

new Chart(document.getElementById('donutChart'), {
    type: 'doughnut',
    data: {
        labels: ['Present', 'Late', 'Absent'],
        datasets: [{
            data: [
                <?= (int)$stats['present_count'] ?>,
                <?= (int)$stats['late_count'] ?>,
                <?= (int)$stats['absent_count'] ?>
            ],
            backgroundColor: [
                'rgba(34,197,94,.8)',
                'rgba(245,158,11,.8)',
                'rgba(239,68,68,.8)'
            ],
            borderWidth: 0,
            hoverOffset: 6,
        }]
    },
    options: {
        responsive: true,
        cutout: '65%',
        plugins: { legend: { position: 'bottom', labels: { padding: 16 } } }
    }
});
</script>