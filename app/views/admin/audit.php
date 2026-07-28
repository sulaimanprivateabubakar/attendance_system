<?php $pageTitle = 'Audit Logs'; ?>

<div class="page-title">
    <div>
        <h1>🔍 Audit Logs</h1>
        <p>Complete system activity trail</p>
    </div>
    <a href="<?= BASE_URL ?>/admin/audit/export?filter=<?= htmlspecialchars($filter) ?>"
       class="btn btn-secondary">
        <i class="fas fa-download"></i> Export CSV
    </a>
</div>

<!-- Stats -->
<div class="stats" style="margin-bottom:22px">
    <div class="stat-card">
        <div class="stat-info">
            <h3>Total Events</h3>
            <h1 data-counter="<?= (int)$stats['total_events'] ?>"><?= (int)$stats['total_events'] ?></h1>
            <small>In selected period</small>
        </div>
        <div class="stat-icon blue"><i class="fas fa-list"></i></div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <h3>Unique Users</h3>
            <h1 data-counter="<?= (int)$stats['unique_users'] ?>"><?= (int)$stats['unique_users'] ?></h1>
            <small>Active in period</small>
        </div>
        <div class="stat-icon green"><i class="fas fa-users"></i></div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <h3>Attendance Events</h3>
            <h1 data-counter="<?= (int)$stats['att_events'] ?>"><?= (int)$stats['att_events'] ?></h1>
            <small>Scans & manual entries</small>
        </div>
        <div class="stat-icon cyan"><i class="fas fa-qrcode"></i></div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <h3>Session Events</h3>
            <h1 data-counter="<?= (int)$stats['session_events'] ?>"><?= (int)$stats['session_events'] ?></h1>
            <small>Created, activated, closed</small>
        </div>
        <div class="stat-icon amber"><i class="fas fa-calendar"></i></div>
    </div>
</div>

<!-- Filters -->
<div class="panel" style="margin-bottom:22px">
    <div class="panel-body" style="padding:16px 20px">
        <form method="GET" action="<?= BASE_URL ?>/admin/audit"
              style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap">

            <!-- Period filter tabs -->
            <div>
                <div style="font-size:.72rem;font-weight:600;color:var(--text-muted);
                            text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px">
                    Period
                </div>
                <div style="display:flex;gap:6px">
                    <?php foreach (['today'=>'Today','week'=>'7 Days','month'=>'30 Days','year'=>'1 Year','all'=>'All Time'] as $val => $label): ?>
                    <a href="<?= BASE_URL ?>/admin/audit?filter=<?= $val ?>&module=<?= urlencode($module) ?>&search=<?= urlencode($search) ?>"
                       class="btn btn-sm <?= $filter === $val ? 'btn-primary' : 'btn-secondary' ?>">
                        <?= $label ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Module filter -->
            <div class="form-group" style="margin:0;min-width:150px">
                <div style="font-size:.72rem;font-weight:600;color:var(--text-muted);
                            text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px">
                    Module
                </div>
                <select name="module" onchange="this.form.submit()"
                        style="padding:8px 12px;border-radius:9px;font-size:.82rem">
                    <option value="">All Modules</option>
                    <?php foreach ($modules as $m): ?>
                    <option value="<?= $m['module'] ?>" <?= $module === $m['module'] ? 'selected' : '' ?>>
                        <?= ucfirst($m['module']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <input type="hidden" name="filter" value="<?= htmlspecialchars($filter) ?>">
            </div>

            <!-- Search -->
            <div class="form-group" style="margin:0;flex:1;min-width:220px">
                <div style="font-size:.72rem;font-weight:600;color:var(--text-muted);
                            text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px">
                    Search
                </div>
                <div style="display:flex;gap:8px">
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
                           placeholder="Search user, action, description..."
                           style="padding:8px 14px;border-radius:9px;font-size:.82rem;
                                  flex:1">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-search"></i>
                    </button>
                    <?php if ($search || $module): ?>
                    <a href="<?= BASE_URL ?>/admin/audit?filter=<?= $filter ?>"
                       class="btn btn-secondary btn-sm">
                        <i class="fas fa-times"></i>
                    </a>
                    <?php endif; ?>
                </div>
            </div>

        </form>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 300px;gap:22px;align-items:start">

    <!-- Logs Table -->
    <div class="panel">
        <div class="panel-header">
            <h2>
                <i class="fas fa-list" style="color:var(--primary);margin-right:8px"></i>
                Activity Log
            </h2>
            <span class="badge badge-count"><?= number_format($total) ?> events</span>
        </div>
        <div class="table-wrap">
            <?php if (empty($logs)): ?>
            <div class="empty-state">
                <div class="empty-icon">🔍</div>
                <p>No audit events found for the selected filters.</p>
            </div>
            <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Module</th>
                        <th>Description</th>
                        <th>IP</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td style="white-space:nowrap;font-size:.75rem;color:var(--text-muted)">
                            <?= date('M j', strtotime($log['created_at'])) ?><br>
                            <strong><?= date('H:i:s', strtotime($log['created_at'])) ?></strong>
                        </td>
                        <td>
                            <div style="display:flex;align-items:center;gap:8px">
                                <div style="width:28px;height:28px;border-radius:7px;
                                            background:linear-gradient(135deg,var(--primary),var(--primary-light));
                                            display:flex;align-items:center;justify-content:center;
                                            font-size:.65rem;font-weight:700;color:#fff;flex-shrink:0">
                                    <?= strtoupper(substr($log['user_name'] ?? 'S', 0, 2)) ?>
                                </div>
                                <div>
                                    <div style="font-size:.8rem;font-weight:600;color:var(--text)">
                                        <?= htmlspecialchars($log['user_name'] ?? 'System') ?>
                                    </div>
                                    <span class="badge badge-<?= $log['user_role'] ?? 'closed' ?>"
                                          style="font-size:.62rem;padding:2px 6px">
                                        <?= $log['user_role'] ?? '—' ?>
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <?php
                            $actionColor = match(true) {
                                str_starts_with($log['action'], 'auth.')       => 'var(--info)',
                                str_starts_with($log['action'], 'attendance.') => 'var(--success)',
                                str_starts_with($log['action'], 'user.')       => 'var(--warning)',
                                str_starts_with($log['action'], 'session.')    => 'var(--primary-light)',
                                str_starts_with($log['action'], 'import.')     => '#a78bfa',
                                str_starts_with($log['action'], 'claim.')      => '#f0abfc',
                                default                                         => 'var(--text-muted)',
                            };
                            ?>
                            <code style="font-size:.72rem;color:<?= $actionColor ?>;
                                         background:rgba(255,255,255,.06);
                                         padding:3px 8px;border-radius:6px">
                                <?= htmlspecialchars($log['action']) ?>
                            </code>
                        </td>
                        <td>
                            <span style="font-size:.75rem;color:var(--text-muted)">
                                <?= htmlspecialchars($log['module']) ?>
                            </span>
                        </td>
                        <td style="font-size:.8rem;color:var(--text-light);max-width:280px">
                            <?= htmlspecialchars($log['description'] ?? '') ?>
                        </td>
                        <td style="font-size:.72rem;color:var(--text-muted);white-space:nowrap">
                            <?= htmlspecialchars($log['ip_address'] ?? '—') ?>
                        </td>
                        <td>
                            <?php if ($log['old_value'] || $log['new_value']): ?>
                            <a href="<?= BASE_URL ?>/admin/audit/<?= $log['id'] ?>"
                               class="btn btn-sm btn-secondary" title="View details">
                                <i class="fas fa-eye"></i>
                            </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
            <div style="padding:16px 20px;display:flex;justify-content:space-between;
                        align-items:center;border-top:1px solid rgba(255,255,255,.05)">
                <span style="font-size:.8rem;color:var(--text-muted)">
                    Showing <?= number_format(($page-1)*$perPage+1) ?>–<?= number_format(min($page*$perPage,$total)) ?>
                    of <?= number_format($total) ?> events
                </span>
                <div style="display:flex;gap:6px">
                    <?php if ($page > 1): ?>
                    <a href="?filter=<?= $filter ?>&module=<?= urlencode($module) ?>&search=<?= urlencode($search) ?>&page=<?= $page-1 ?>"
                       class="btn btn-sm btn-secondary">← Prev</a>
                    <?php endif; ?>
                    <?php
                    $start = max(1, $page-2);
                    $end   = min($totalPages, $page+2);
                    for ($p = $start; $p <= $end; $p++):
                    ?>
                    <a href="?filter=<?= $filter ?>&module=<?= urlencode($module) ?>&search=<?= urlencode($search) ?>&page=<?= $p ?>"
                       class="btn btn-sm <?= $p === $page ? 'btn-primary' : 'btn-secondary' ?>">
                        <?= $p ?>
                    </a>
                    <?php endfor; ?>
                    <?php if ($page < $totalPages): ?>
                    <a href="?filter=<?= $filter ?>&module=<?= urlencode($module) ?>&search=<?= urlencode($search) ?>&page=<?= $page+1 ?>"
                       class="btn btn-sm btn-secondary">Next →</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php endif; ?>
        </div>
    </div>

    <!-- Right sidebar -->
    <div style="display:flex;flex-direction:column;gap:16px">

        <!-- Top Users -->
        <div class="panel">
            <div class="panel-header">
                <h2 style="font-size:.9rem">Top Active Users</h2>
            </div>
            <div style="padding:12px 16px">
                <?php if (empty($topUsers)): ?>
                <p class="empty">No activity yet</p>
                <?php else: ?>
                <?php foreach ($topUsers as $i => $u): ?>
                <div style="display:flex;justify-content:space-between;align-items:center;
                            padding:10px 0;border-bottom:1px solid rgba(255,255,255,.04)">
                    <div style="display:flex;align-items:center;gap:10px">
                        <span style="font-size:.75rem;color:var(--text-muted);
                                     width:18px;text-align:center">
                            <?= $i + 1 ?>
                        </span>
                        <div>
                            <div style="font-size:.82rem;font-weight:600;color:var(--text)">
                                <?= htmlspecialchars($u['user_name']) ?>
                            </div>
                            <span class="badge badge-<?= $u['user_role'] ?>"
                                  style="font-size:.62rem;padding:1px 6px">
                                <?= $u['user_role'] ?>
                            </span>
                        </div>
                    </div>
                    <span class="badge badge-count"><?= $u['event_count'] ?></span>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Activity by Hour Chart -->
        <?php if ($filter === 'today' && !empty($hourlyActivity)): ?>
        <div class="panel">
            <div class="panel-header">
                <h2 style="font-size:.9rem">Activity by Hour (Today)</h2>
            </div>
            <div style="padding:16px">
                <canvas id="hourlyChart" height="200"></canvas>
            </div>
        </div>
        <?php endif; ?>

        <!-- Module Breakdown -->
        <div class="panel">
            <div class="panel-header">
                <h2 style="font-size:.9rem">Module Breakdown</h2>
            </div>
            <div style="padding:12px 16px">
                <?php
                $moduleStats = [
                    'auth'       => ['🔐', 'Auth',       (int)$stats['auth_events']],
                    'attendance' => ['📋', 'Attendance',  (int)$stats['att_events']],
                    'sessions'   => ['📅', 'Sessions',    (int)$stats['session_events']],
                    'users'      => ['👥', 'Users',        (int)$stats['user_events']],
                ];
                $maxVal = max(array_column(array_values($moduleStats), 2)) ?: 1;
                foreach ($moduleStats as [$icon, $label, $count]):
                    $pct = round($count / $maxVal * 100);
                ?>
                <div style="margin-bottom:12px">
                    <div style="display:flex;justify-content:space-between;
                                font-size:.78rem;margin-bottom:4px">
                        <span style="color:var(--text-light)"><?= $icon ?> <?= $label ?></span>
                        <span style="color:var(--text-muted)"><?= $count ?></span>
                    </div>
                    <div style="background:rgba(255,255,255,.06);border-radius:99px;height:5px">
                        <div style="width:<?= $pct ?>%;height:5px;border-radius:99px;
                                    background:var(--primary);transition:width .6s">
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>
</div>

<?php if ($filter === 'today' && !empty($hourlyActivity)): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const hours   = <?= json_encode(array_column($hourlyActivity, 'hour')) ?>;
const counts  = <?= json_encode(array_map(fn($r) => (int)$r['count'], $hourlyActivity)) ?>;

new Chart(document.getElementById('hourlyChart'), {
    type: 'bar',
    data: {
        labels: hours.map(h => h + ':00'),
        datasets: [{
            label: 'Events',
            data: counts,
            backgroundColor: 'rgba(22,163,74,.6)',
            borderRadius: 4,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { color: 'rgba(255,255,255,.05)' }, ticks: { color: '#606060', font: { size: 10 } } },
            y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,.05)' }, ticks: { color: '#606060', stepSize: 1 } }
        }
    }
});
</script>
<?php endif; ?>