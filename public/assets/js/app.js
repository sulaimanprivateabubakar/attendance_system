// public/assets/js/app.js

// ── Flash message auto-dismiss ───────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity    = '0';
            setTimeout(() => alert.remove(), 500);
        }, 4000);
    });
});

// ── QR Fullscreen toggle ─────────────────────────────────────────
function toggleFullscreen() {
    const img = document.getElementById('qrImage');
    if (!img) return;

    if (!document.fullscreenElement) {
        img.requestFullscreen().catch(err => {
            console.warn('Fullscreen error:', err);
        });
    } else {
        document.exitFullscreen();
    }
}

// ── Confirm dialogs ──────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-confirm]').forEach(el => {
        el.addEventListener('click', e => {
            if (!confirm(el.dataset.confirm)) {
                e.preventDefault();
            }
        });
    });
});

// ── Live attendance refresh ──────────────────────────────────────
function startAttendancePolling(sessionId, baseUrl, intervalMs = 10000) {
    const listEl  = document.getElementById('attendanceList');
    const countEl = document.getElementById('attendanceCount');

    if (!listEl) return;

    setInterval(() => {
        fetch(`${baseUrl}/api/attendance.php?session_id=${sessionId}`)
            .then(r => r.json())
            .then(data => {
                if (countEl) countEl.textContent = data.count;

                if (!data.records || data.records.length === 0) {
                    listEl.innerHTML = '<p class="empty">No students have scanned yet.</p>';
                    return;
                }

                let rows = '';
                data.records.forEach((row, i) => {
                    rows += `
                        <tr>
                            <td>${i + 1}</td>
                            <td>${escHtml(row.name)}</td>
                            <td>${escHtml(row.student_number)}</td>
                            <td>${escHtml(row.scanned_at)}</td>
                            <td><span class="badge badge-${escHtml(row.status)}">${escHtml(row.status)}</span></td>
                        </tr>`;
                });

                listEl.innerHTML = `
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th><th>Name</th>
                                <th>Student No.</th><th>Time</th><th>Status</th>
                            </tr>
                        </thead>
                        <tbody>${rows}</tbody>
                    </table>`;
            })
            .catch(() => { /* silent fail – no network noise */ });
    }, intervalMs);
}

// ── HTML escape helper ───────────────────────────────────────────
function escHtml(str) {
    const div = document.createElement('div');
    div.appendChild(document.createTextNode(String(str)));
    return div.innerHTML;
}
