/*==========================================================
 QR ATTENDANCE SYSTEM — app.js
==========================================================*/

document.addEventListener('DOMContentLoaded', function () {

    /* =============================================
       SIDEBAR TOGGLE (MOBILE)
    ============================================= */
    const menuBtn = document.querySelector('.menu-btn');
    const sidebar = document.querySelector('.sidebar');

    if (menuBtn && sidebar) {
        menuBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            sidebar.classList.toggle('open');
        });
        document.addEventListener('click', function(e) {
            if (sidebar.classList.contains('open') &&
                !e.target.closest('.sidebar') &&
                !e.target.closest('.menu-btn')) {
                sidebar.classList.remove('open');
            }
        });
    }

    /* =============================================
       ACTIVE SIDEBAR LINK
    ============================================= */
    const currentPath = window.location.pathname;
    document.querySelectorAll('.sidebar a').forEach(function(link) {
        const href = link.getAttribute('href');
        if (href && href !== '#' && currentPath.indexOf(href) !== -1) {
            const li = link.closest('li');
            if (li) li.classList.add('active');
        }
    });

    /* =============================================
       LIVE CLOCK
    ============================================= */
    function updateClock() {
        const clock = document.getElementById('clock');
        if (!clock) return;
        const now  = new Date();
        let h      = now.getHours();
        let m      = now.getMinutes();
        const ampm = h >= 12 ? 'PM' : 'AM';
        h = h % 12 || 12;
        m = m < 10 ? '0' + m : m;
        clock.textContent = h + ':' + m + ' ' + ampm;
    }
    updateClock();
    setInterval(updateClock, 1000);

/* =============================================
   DARK / LIGHT MODE TOGGLE
============================================= */
const themeBtn   = document.getElementById('themeToggle');
const savedTheme = localStorage.getItem('qr_theme') || 'dark';

if (savedTheme === 'light') {
    document.body.classList.add('light-mode');
}

function updateThemeIcon() {
    if (!themeBtn) return;
    const isLight = document.body.classList.contains('light-mode');
    themeBtn.innerHTML = isLight
        ? '<i class="fas fa-moon"></i>'
        : '<i class="fas fa-sun"></i>';
    themeBtn.title = isLight ? 'Switch to Dark Mode' : 'Switch to Light Mode';
}

// Apply immediately on load
if (themeBtn) {
    updateThemeIcon();
    themeBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        document.body.classList.toggle('light-mode');
        const isLight = document.body.classList.contains('light-mode');
        localStorage.setItem('qr_theme', isLight ? 'light' : 'dark');
        updateThemeIcon();
    });
}

    /* =============================================
       FLASH MESSAGE AUTO-DISMISS
    ============================================= */
    document.querySelectorAll('.alert').forEach(function(alert) {
        const close = document.createElement('button');
        close.style.cssText = 'margin-left:auto;background:none;border:none;' +
                              'cursor:pointer;font-size:1rem;opacity:.7;' +
                              'color:inherit;padding:0 4px;flex-shrink:0';
        close.innerHTML = '&times;';
        close.addEventListener('click', function() { fadeOut(alert); });
        alert.style.display = 'flex';
        alert.appendChild(close);
        setTimeout(function() { fadeOut(alert); }, 5000);
    });

    function fadeOut(el) {
        if (!el || !el.parentNode) return;
        el.style.transition = 'opacity .35s, transform .35s';
        el.style.opacity    = '0';
        el.style.transform  = 'translateY(-6px)';
        setTimeout(function() {
            if (el.parentNode) el.parentNode.removeChild(el);
        }, 350);
    }

    /* =============================================
       ANIMATED COUNTERS
    ============================================= */
    document.querySelectorAll('[data-counter]').forEach(function(el) {
        const target = parseInt(el.getAttribute('data-counter'));
        if (isNaN(target) || target === 0) return;
        let count    = 0;
        const step   = Math.ceil(target / 60) || 1;
        const timer  = setInterval(function() {
            count += step;
            if (count >= target) { count = target; clearInterval(timer); }
            el.textContent = count.toLocaleString();
        }, 20);
    });

    /* =============================================
       PROGRESS BAR ANIMATION
    ============================================= */
    document.querySelectorAll('.progress-bar').forEach(function(bar) {
        const target = bar.style.width;
        bar.style.width = '0';
        setTimeout(function() { bar.style.width = target; }, 300);
    });

    /* =============================================
       CONFIRM DIALOGS
    ============================================= */
    document.querySelectorAll('[data-confirm]').forEach(function(el) {
        el.addEventListener('click', function(e) {
            if (!confirm(el.getAttribute('data-confirm'))) e.preventDefault();
        });
    });

    /* =============================================
       CARD STAGGER ANIMATION
    ============================================= */
    document.querySelectorAll('.stat-card').forEach(function(el, i) {
        el.style.animationDelay = (i * 0.08) + 's';
    });

    /* =============================================
       LIVE TABLE SEARCH
    ============================================= */
    const searchInput = document.querySelector('.search-box input');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query  = this.value.toLowerCase().trim();
            const tables = document.querySelectorAll('.table tbody');

            tables.forEach(function(tbody) {
                const rows   = tbody.querySelectorAll('tr:not(.search-no-result)');
                let visible  = 0;

                rows.forEach(function(row) {
                    const match = row.textContent.toLowerCase().indexOf(query) !== -1;
                    row.style.display = match ? '' : 'none';
                    if (match) visible++;
                });

                // Remove old no-result row
                const old = tbody.querySelector('.search-no-result');
                if (old) old.parentNode.removeChild(old);

                // Add no-result row if needed
                if (visible === 0 && query !== '') {
                    const noRow = document.createElement('tr');
                    noRow.className = 'search-no-result';
                    noRow.innerHTML = '<td colspan="20" style="text-align:center;' +
                        'padding:24px;color:var(--text-muted)">No results for "<strong>' +
                        escHtml(query) + '</strong>"</td>';
                    tbody.appendChild(noRow);
                }
            });
        });
    }

    /* =============================================
       RESIZABLE SIDEBAR
    ============================================= */
    const sidebarEl   = document.querySelector('.sidebar');
    const mainEl      = document.querySelector('.main');
    const SIDEBAR_MIN = 60;
    const SIDEBAR_MAX = 400;
    const SIDEBAR_KEY = 'sidebar_width';

    if (sidebarEl && mainEl) {
        // Restore saved width
        const savedWidth = localStorage.getItem(SIDEBAR_KEY);
        if (savedWidth) {
            const w = parseInt(savedWidth);
            sidebarEl.style.width   = w + 'px';
            mainEl.style.marginLeft = w + 'px';
            mainEl.style.width      = 'calc(100% - ' + w + 'px)';
            if (w <= SIDEBAR_MIN + 10) sidebarEl.classList.add('sidebar-collapsed');
        }

        // Create drag handle
        const handle = document.createElement('div');
        handle.className = 'sidebar-resize-handle';
        handle.title     = 'Drag to resize · Double-click to reset';
        sidebarEl.appendChild(handle);

        let isResizing = false;
        let startX     = 0;
        let startWidth = 0;

        handle.addEventListener('mousedown', function(e) {
            isResizing = true;
            startX     = e.clientX;
            startWidth = sidebarEl.offsetWidth;
            handle.classList.add('dragging');
            document.body.style.cursor     = 'col-resize';
            document.body.style.userSelect = 'none';
            e.preventDefault();
        });

        document.addEventListener('mousemove', function(e) {
            if (!isResizing) return;
            let w = startWidth + (e.clientX - startX);
            if (w < SIDEBAR_MIN + 20) {
                w = SIDEBAR_MIN;
                sidebarEl.classList.add('sidebar-collapsed');
            } else {
                sidebarEl.classList.remove('sidebar-collapsed');
            }
            if (w > SIDEBAR_MAX) w = SIDEBAR_MAX;
            sidebarEl.style.width   = w + 'px';
            mainEl.style.marginLeft = w + 'px';
            mainEl.style.width      = 'calc(100% - ' + w + 'px)';
        });

        document.addEventListener('mouseup', function() {
            if (!isResizing) return;
            isResizing = false;
            handle.classList.remove('dragging');
            document.body.style.cursor     = '';
            document.body.style.userSelect = '';
            localStorage.setItem(SIDEBAR_KEY, sidebarEl.offsetWidth.toString());
        });

        handle.addEventListener('dblclick', function() {
            const def = 260;
            sidebarEl.style.width   = def + 'px';
            mainEl.style.marginLeft = def + 'px';
            mainEl.style.width      = 'calc(100% - ' + def + 'px)';
            sidebarEl.classList.remove('sidebar-collapsed');
            localStorage.setItem(SIDEBAR_KEY, def.toString());
        });
    }

    /* =============================================
       NOTIFICATIONS PANEL
       — build only once, find bell by ID
    ============================================= */

    // Remove any existing panel from a previous page load
    const existingPanel = document.getElementById('notifPanel');
    if (existingPanel) existingPanel.parentNode.removeChild(existingPanel);

    const notifBellBtn = document.getElementById('notifBellBtn');
    const notifBadgeEl = document.querySelector('.notif-badge');

    if (notifBellBtn) {
        // Build panel once
        const panel = document.createElement('div');
        panel.id    = 'notifPanel';
        panel.style.cssText =
            'position:fixed;top:70px;right:20px;width:340px;' +
            'background:var(--card);border:1px solid var(--border);' +
            'border-radius:16px;box-shadow:0 20px 60px rgba(0,0,0,.5);' +
            'z-index:99999;display:none;overflow:hidden;';

        panel.innerHTML =
            '<div style="padding:16px 20px;border-bottom:1px solid rgba(255,255,255,.06);' +
            'display:flex;justify-content:space-between;align-items:center">' +
            '<span style="font-weight:600;font-size:.95rem;color:var(--text)">Notifications</span>' +
            '<button id="closeNotifBtn" style="background:none;border:none;color:var(--text-muted);' +
            'cursor:pointer;font-size:1.3rem;line-height:1;padding:0 4px">&times;</button>' +
            '</div>' +
            '<div id="notifList" style="max-height:380px;overflow-y:auto">' +
            '<div style="padding:24px;text-align:center;color:var(--text-muted);font-size:.85rem">' +
            'Loading...</div></div>' +
            '<div style="padding:12px 20px;border-top:1px solid rgba(255,255,255,.06);text-align:center">' +
            '<a href="' + (window.BASE_URL || '') + '/admin/reports" ' +
            'style="font-size:.8rem;color:var(--primary-light);font-weight:600">' +
            'View Reports →</a></div>';

        document.body.appendChild(panel);

        // Close button
        document.getElementById('closeNotifBtn').addEventListener('click', function(e) {
            e.stopPropagation();
            panel.style.display = 'none';
        });

        // Fetch and render notifications
        function loadNotifications() {
            const list = document.getElementById('notifList');
            if (!list) return;
            list.innerHTML = '<div style="padding:24px;text-align:center;' +
                'color:var(--text-muted);font-size:.85rem">Loading...</div>';

            fetch((window.BASE_URL || '') + '/api/notifications.php')
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (!data.notifications || !data.notifications.length) {
                        list.innerHTML = '<div style="padding:24px;text-align:center;' +
                            'color:var(--text-muted)">No notifications</div>';
                        return;
                    }
                    list.innerHTML = data.notifications.map(function(n) {
                        return '<div style="padding:14px 20px;' +
                            'border-bottom:1px solid rgba(255,255,255,.04);' +
                            'display:flex;gap:13px;align-items:flex-start">' +
                            '<div style="width:38px;height:38px;border-radius:11px;' +
                            'background:' + n.color + ';display:flex;align-items:center;' +
                            'justify-content:center;font-size:1.1rem;flex-shrink:0">' +
                            n.icon + '</div>' +
                            '<div style="flex:1;min-width:0">' +
                            '<div style="font-size:.84rem;font-weight:600;color:var(--text);' +
                            'margin-bottom:3px;line-height:1.4">' + escHtml(n.title) + '</div>' +
                            '<div style="font-size:.74rem;color:var(--text-muted);line-height:1.4">' +
                            escHtml(n.message) + '</div>' +
                            (n.time ? '<div style="font-size:.7rem;color:var(--primary-light);' +
                            'margin-top:4px;font-weight:600">' + escHtml(n.time) + '</div>' : '') +
                            '</div></div>';
                    }).join('');
                })
                .catch(function() {
                    const list2 = document.getElementById('notifList');
                    if (list2) list2.innerHTML = '<div style="padding:24px;text-align:center;' +
                        'color:var(--text-muted)">Could not load notifications</div>';
                });
        }

        // Toggle panel
        notifBellBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            const isOpen = panel.style.display === 'block';
            panel.style.display = isOpen ? 'none' : 'block';
            if (!isOpen) {
                loadNotifications();
                if (notifBadgeEl) {
                    notifBadgeEl.style.display = 'none';
                    notifBadgeEl.textContent   = '0';
                }
                localStorage.setItem('notif_viewed', Date.now().toString());
            }
        });

        // Close on outside click
        document.addEventListener('click', function(e) {
            if (panel.style.display !== 'block') return;
            if (panel.contains(e.target)) return;
            if (e.target === notifBellBtn || notifBellBtn.contains(e.target)) return;
            panel.style.display = 'none';
        });

        // Show badge on page load if not recently viewed
        const lastViewed     = localStorage.getItem('notif_viewed');
        const fiveMin        = 5 * 60 * 1000;
        const recentlyViewed = lastViewed &&
            (Date.now() - parseInt(lastViewed)) < fiveMin;

        if (!recentlyViewed) {
            fetch((window.BASE_URL || '') + '/api/notifications.php')
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (notifBadgeEl && data.count > 0) {
                        notifBadgeEl.textContent   = data.count;
                        notifBadgeEl.style.display = 'flex';
                    }
                })
                .catch(function() {});
        } else {
            if (notifBadgeEl) notifBadgeEl.style.display = 'none';
        }
    }

});

/* =============================================
   QR FULLSCREEN
============================================= */
function toggleFullscreen() {
    const img = document.getElementById('qrImage');
    if (!img) return;
    if (!document.fullscreenElement) {
        (img.requestFullscreen || img.webkitRequestFullscreen).call(img);
    } else {
        (document.exitFullscreen || document.webkitExitFullscreen).call(document);
    }
}

/* =============================================
   LIVE ATTENDANCE POLLING
============================================= */
function startAttendancePolling(sessionId, baseUrl, interval) {
    const listEl  = document.getElementById('attendanceList');
    const countEl = document.getElementById('attendanceCount');
    if (!listEl) return;

    function poll() {
        fetch(baseUrl + '/api/attendance.php?session_id=' + sessionId)
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (countEl) countEl.textContent = data.count;
                if (!data.records || !data.records.length) {
                    listEl.innerHTML = '<p class="empty">No students have scanned yet.</p>';
                    return;
                }
                let rows = '';
                data.records.forEach(function(r, i) {
                    rows += '<tr>' +
                        '<td>' + (i + 1) + '</td>' +
                        '<td><strong>' + escHtml(r.name) + '</strong></td>' +
                        '<td>' + escHtml(r.student_number) + '</td>' +
                        '<td>' + escHtml(r.scanned_at) + '</td>' +
                        '<td><span class="badge badge-' + escHtml(r.status) + '">' +
                        escHtml(r.status) + '</span></td></tr>';
                });
                listEl.innerHTML =
                    '<table class="table"><thead><tr>' +
                    '<th>#</th><th>Name</th><th>Student No.</th>' +
                    '<th>Time</th><th>Status</th></tr></thead>' +
                    '<tbody>' + rows + '</tbody></table>';
            })
            .catch(function() {});
    }

    poll();
    setInterval(poll, interval || 10000);
}

/* =============================================
   HTML ESCAPE HELPER
============================================= */
function escHtml(str) {
    const d = document.createElement('div');
    d.appendChild(document.createTextNode(String(str)));
    return d.innerHTML;
}