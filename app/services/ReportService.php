<?php
// app/services/ReportService.php

class ReportService
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // ── Data fetch ───────────────────────────────────────────────────────────

    private function getCourseInfo(int $courseId): ?array
    {
        return $this->db->single(
            "SELECT c.*, u.name AS lecturer_name, d.name AS dept_name
               FROM courses c
               LEFT JOIN lecturers  l ON l.id = c.lecturer_id
               LEFT JOIN users      u ON u.id = l.user_id
               LEFT JOIN departments d ON d.id = c.department_id
              WHERE c.id = ?",
            [$courseId]
        );
    }

    private function getSessions(int $courseId): array
    {
        return $this->db->all(
            "SELECT id, session_date, start_time, title
               FROM sessions
              WHERE course_id = ? AND status = 'closed'
              ORDER BY session_date, start_time",
            [$courseId]
        );
    }

    private function getEnrolledStudents(int $courseId): array
    {
        return $this->db->all(
            "SELECT s.id, s.student_number, u.name
               FROM enrollments e
               JOIN students s ON s.id = e.student_id
               JOIN users    u ON u.id = s.user_id
              WHERE e.course_id = ?
              ORDER BY u.name",
            [$courseId]
        );
    }

    private function getAttendanceMap(int $courseId): array
    {
        $rows = $this->db->all(
            "SELECT a.student_id, a.session_id, a.status
               FROM attendance a
               JOIN sessions s ON s.id = a.session_id
              WHERE s.course_id = ?",
            [$courseId]
        );

        $map = [];
        foreach ($rows as $r) {
            $map[$r['student_id']][$r['session_id']] = $r['status'];
        }
        return $map;
    }

    // ── CSV export ───────────────────────────────────────────────────────────

    public function exportCsv(int $courseId): void
    {
        $course   = $this->getCourseInfo($courseId);
        $sessions = $this->getSessions($courseId);
        $students = $this->getEnrolledStudents($courseId);
        $attMap   = $this->getAttendanceMap($courseId);

        $filename = 'attendance_' . ($course['code'] ?? $courseId) . '_' . date('Ymd') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');

        $out = fopen('php://output', 'w');

        // Course info header
        fputcsv($out, ['Course:', $course['code'] . ' – ' . $course['name']]);
        fputcsv($out, ['Lecturer:', $course['lecturer_name'] ?? '']);
        fputcsv($out, ['Department:', $course['dept_name'] ?? '']);
        fputcsv($out, ['Generated:', date('Y-m-d H:i')]);
        fputcsv($out, []);

        // Column headers: Student No | Name | Session1 | Session2 | … | Total | %
        $header = ['Student No', 'Name'];
        foreach ($sessions as $s) {
            $header[] = $s['session_date'] . ' ' . substr($s['start_time'], 0, 5);
        }
        $header[] = 'Total Attended';
        $header[] = 'Attendance %';
        fputcsv($out, $header);

        // Data rows
        foreach ($students as $stu) {
            $row     = [$stu['student_number'], $stu['name']];
            $total   = 0;

            foreach ($sessions as $s) {
                $status = $attMap[$stu['id']][$s['id']] ?? 'absent';
                $row[]  = $status;
                if ($status !== 'absent') $total++;
            }

            $pct   = count($sessions) > 0
                     ? round($total / count($sessions) * 100, 1)
                     : 0;
            $row[] = $total;
            $row[] = $pct . '%';
            fputcsv($out, $row);
        }

        fclose($out);
        exit;
    }

    // ── PDF export (pure PHP, no library required) ──────────────────────────

    /**
     * Generates an HTML page styled for printing, then triggers browser print.
     * For a real PDF, install dompdf: composer require dompdf/dompdf
     */
    public function exportPdf(int $courseId): void
    {
        $course   = $this->getCourseInfo($courseId);
        $sessions = $this->getSessions($courseId);
        $students = $this->getEnrolledStudents($courseId);
        $attMap   = $this->getAttendanceMap($courseId);

        // If dompdf is available, use it
        if (class_exists('Dompdf\Dompdf')) {
            $this->exportPdfViaDompdf($course, $sessions, $students, $attMap);
            return;
        }

        // Fallback: print-ready HTML
        $this->exportPrintableHtml($course, $sessions, $students, $attMap);
    }

    private function exportPdfViaDompdf(array $course, array $sessions, array $students, array $attMap): void
    {
        $html = $this->buildReportHtml($course, $sessions, $students, $attMap);

        $dompdf = new \Dompdf\Dompdf(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => false]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $filename = 'attendance_' . ($course['code'] ?? 'report') . '_' . date('Ymd') . '.pdf';
        $dompdf->stream($filename, ['Attachment' => true]);
        exit;
    }

    private function exportPrintableHtml(array $course, array $sessions, array $students, array $attMap): void
    {
        header('Content-Type: text/html; charset=utf-8');
        echo $this->buildReportHtml($course, $sessions, $students, $attMap, printMode: true);
        exit;
    }

    private function buildReportHtml(
        array $course, array $sessions, array $students, array $attMap,
        bool $printMode = false
    ): string {
        $rows = '';
        foreach ($students as $i => $stu) {
            $cells = '<td>' . ($i + 1) . '</td>'
                   . '<td>' . htmlspecialchars($stu['student_number']) . '</td>'
                   . '<td>' . htmlspecialchars($stu['name']) . '</td>';

            $total = 0;
            foreach ($sessions as $s) {
                $status = $attMap[$stu['id']][$s['id']] ?? 'absent';
                $color  = match($status) {
                    'present' => '#16a34a',
                    'late'    => '#d97706',
                    default   => '#dc2626',
                };
                $letter = strtoupper(substr($status, 0, 1)); // P / L / A
                $cells .= "<td style='color:{$color};font-weight:600;text-align:center'>{$letter}</td>";
                if ($status !== 'absent') $total++;
            }

            $pct    = count($sessions) > 0 ? round($total / count($sessions) * 100, 1) : 0;
            $cells .= "<td style='text-align:center'>{$total}</td>"
                    . "<td style='text-align:center;font-weight:600'>{$pct}%</td>";

            $rows .= "<tr>{$cells}</tr>\n";
        }

        $sessionHeaders = '';
        foreach ($sessions as $s) {
            $sessionHeaders .= '<th style="writing-mode:vertical-lr;transform:rotate(180deg);padding:4px 6px;font-size:10px">'
                             . htmlspecialchars($s['session_date']) . '</th>';
        }

        $printScript = $printMode
            ? '<script>window.onload=function(){window.print();}</script>'
            : '';

        $generated = date('Y-m-d H:i');

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Attendance Report – {$course['code']}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }
        h1   { font-size: 16px; margin-bottom: 4px; }
        .meta { color: #555; margin-bottom: 16px; font-size: 11px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ccc; padding: 4px 8px; }
        th { background: #f3f4f6; font-weight: 600; }
        tr:nth-child(even) { background: #fafafa; }
        .legend { margin-top: 12px; font-size: 11px; color: #555; }
        @media print { button { display: none; } }
    </style>
    {$printScript}
</head>
<body>
    <h1>Attendance Report: {$course['code']} – {$course['name']}</h1>
    <div class="meta">
        Lecturer: {$course['lecturer_name']} &nbsp;|&nbsp;
        Department: {$course['dept_name']} &nbsp;|&nbsp;
        Generated: {$generated}
    </div>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Stu. No.</th>
                <th>Name</th>
                {$sessionHeaders}
                <th>Total</th>
                <th>%</th>
            </tr>
        </thead>
        <tbody>{$rows}</tbody>
    </table>
    <div class="legend">P = Present &nbsp; L = Late &nbsp; A = Absent</div>
    <br>
    <button onclick="window.print()">🖨 Print / Save as PDF</button>
</body>
</html>
HTML;
    }
}
