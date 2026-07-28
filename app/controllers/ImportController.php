<?php
// app/controllers/ImportController.php

class ImportController extends Controller
{
    // GET /admin/import
    public function index(): void
    {
        Auth::generateCsrfToken();

        $recentLogs = $this->db->all(
            "SELECT il.*, u.name AS imported_by
               FROM import_logs il
               JOIN users u ON u.id = il.user_id
              ORDER BY il.created_at DESC LIMIT 10"
        );

        $this->view('admin/import', [
            'user'       => Auth::user(),
            'recentLogs' => $recentLogs,
            'flash'      => $this->getFlash(),
            'csrf'       => Auth::generateCsrfToken(),
        ]);
    }

    // GET /admin/import/template/:type
    public function downloadTemplate(array $params): void
    {
        $type = $params['type'] ?? 'students';

        header('Content-Type: text/csv; charset=utf-8');
        header('Pragma: no-cache');

        $out = fopen('php://output', 'w');

        switch ($type) {

            case 'students':
                header('Content-Disposition: attachment; filename="students_import_template.csv"');
                fputcsv($out, ['# STUDENT IMPORT TEMPLATE — QR Attendance System']);
                fputcsv($out, ['# Students are AUTO-ENROLLED into courses matching their department + year + semester']);
                fputcsv($out, ['# Required: full_name, email, reg_number, password, department_code, year_of_study']);
                fputcsv($out, ['# Optional: phone, semester (default=1)']);
                fputcsv($out, ['# Remove ALL comment lines (starting with #) before uploading']);
                fputcsv($out, []);
                fputcsv($out, ['full_name','email','reg_number','password','department_code','year_of_study','phone','semester']);
                fputcsv($out, ['John Banda',   'john.banda@uni.edu',   '2024/CS/001', 'changeme123', 'CS', '1', '+265991234567', '1']);
                fputcsv($out, ['Mary Phiri',   'mary.phiri@uni.edu',   '2024/CS/002', 'changeme123', 'CS', '1', '',             '1']);
                fputcsv($out, ['James Mwale',  'james.mwale@uni.edu',  '2024/IT/001', 'changeme123', 'IT', '2', '+265881234567','1']);
                fputcsv($out, ['Sarah Chirwa', 'sarah.chirwa@uni.edu', '2024/BA/001', 'changeme123', 'BA', '1', '',             '2']);
                break;

            case 'lecturers':
                header('Content-Disposition: attachment; filename="lecturers_import_template.csv"');
                fputcsv($out, ['# LECTURER IMPORT TEMPLATE — QR Attendance System']);
                fputcsv($out, ['# Required: full_name, email, staff_number, password, department_code']);
                fputcsv($out, ['# Optional: phone']);
                fputcsv($out, ['# Remove ALL comment lines before uploading']);
                fputcsv($out, []);
                fputcsv($out, ['full_name','email','staff_number','password','department_code','phone']);
                fputcsv($out, ['Dr. Ali Hassan',    'ali.hassan@uni.edu',    'STF-CS-001', 'changeme123', 'CS', '+265991111111']);
                fputcsv($out, ['Prof. Jane Doe',    'jane.doe@uni.edu',      'STF-IT-001', 'changeme123', 'IT', '+265882222222']);
                fputcsv($out, ['Mr. Peter Banda',   'peter.banda@uni.edu',   'STF-BA-001', 'changeme123', 'BA', '']);
                break;

            case 'courses':
                header('Content-Disposition: attachment; filename="courses_import_template.csv"');
                fputcsv($out, ['# COURSE IMPORT TEMPLATE — QR Attendance System']);
                fputcsv($out, ['# year_of_study determines which students get AUTO-ENROLLED']);
                fputcsv($out, ['# Required: code, name, department_code, semester, year_of_study, academic_year']);
                fputcsv($out, ['# Optional: staff_number, credit_hours']);
                fputcsv($out, ['# Remove ALL comment lines before uploading']);
                fputcsv($out, []);
                fputcsv($out, ['code','name','department_code','staff_number','semester','year_of_study','credit_hours','academic_year']);
                fputcsv($out, ['CS101', 'Introduction to Programming',  'CS', 'STF-CS-001', '1', '1', '3', '2024/2025']);
                fputcsv($out, ['CS102', 'Mathematics for Computing',    'CS', 'STF-CS-001', '1', '1', '3', '2024/2025']);
                fputcsv($out, ['CS201', 'Data Structures',              'CS', 'STF-CS-001', '1', '2', '3', '2024/2025']);
                fputcsv($out, ['IT101', 'Computer Networks',            'IT', 'STF-IT-001', '2', '1', '3', '2024/2025']);
                break;
        }

        fclose($out);
        exit;
    }

    // POST /admin/import/students
    public function importStudents(): void
    {
        $token = $_POST['_csrf'] ?? '';
        if (empty($token) || !Auth::verifyCsrfToken($token)) {
            $this->flash('error', 'Session expired. Please try again.');
            $this->redirect('/admin/import');
        }

        if (empty($_FILES['file']['tmp_name'])) {
            $this->flash('error', 'No file uploaded.');
            $this->redirect('/admin/import');
        }

        $rows    = $this->parseFile($_FILES['file']['tmp_name'], $_FILES['file']['name']);
        $success = 0;
        $failed  = 0;
        $errors  = [];
        $autoEnrolled = 0;

        foreach ($rows as $i => $row) {
            $lineNum = $i + 2;

            if (empty($row) || (isset($row[0]) && str_starts_with(trim((string)$row[0]), '#'))) {
                continue;
            }

            $name     = trim($row['full_name']       ?? $row[0] ?? '');
            $email    = strtolower(trim($row['email']         ?? $row[1] ?? ''));
            $regNo    = trim($row['reg_number']      ?? $row[2] ?? '');
            $pass     = trim($row['password']        ?? $row[3] ?? 'changeme123');
            $deptCode = strtoupper(trim($row['department_code'] ?? $row[4] ?? ''));
            $year     = (int)($row['year_of_study']  ?? $row[5] ?? 1);
            $phone    = trim($row['phone']           ?? $row[6] ?? '');
            $semester = (int)($row['semester']       ?? $row[7] ?? 1);

            if (!$name || !$email || !$regNo || !$deptCode) {
                $errors[] = "Row $lineNum: Missing required fields (name, email, reg_number or department_code)";
                $failed++;
                continue;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Row $lineNum: Invalid email '$email'";
                $failed++;
                continue;
            }

            if (strlen($pass) < 6) {
                $errors[] = "Row $lineNum: Password too short for '$name'";
                $failed++;
                continue;
            }

            // Check duplicates
            if ($this->db->single("SELECT id FROM users WHERE email = ?", [$email])) {
                $errors[] = "Row $lineNum: Email '$email' already registered — skipped";
                $failed++;
                continue;
            }

            if ($this->db->single("SELECT id FROM students WHERE student_number = ?", [$regNo])) {
                $errors[] = "Row $lineNum: Reg number '$regNo' already exists — skipped";
                $failed++;
                continue;
            }

            // Find department
            $dept = $this->db->single("SELECT id FROM departments WHERE code = ?", [$deptCode]);
            if (!$dept) {
                $errors[] = "Row $lineNum: Department code '$deptCode' not found — skipped";
                $failed++;
                continue;
            }
            $deptId = $dept['id'];

            // Insert student
            try {
                $studentId = null;

                $this->db->transaction(function($db) use (
                    $name, $email, $pass, $regNo, $deptId, $year, $phone, &$studentId
                ) {
                    $userId = $db->insert(
                        "INSERT INTO users (name, email, password, role) VALUES (?,?,?,?)",
                        [$name, $email, password_hash($pass, PASSWORD_BCRYPT), 'student']
                    );
                    $sid = $db->insert(
                        "INSERT INTO students (user_id, student_number, department_id, year_of_study, phone)
                         VALUES (?,?,?,?,?)",
                        [$userId, $regNo, $deptId, $year ?: 1, $phone ?: null]
                    );
                    $studentId = $sid;
                });

                $success++;

                // ── AUTO-ENROLL into matching courses ──────────────────────
                // Find courses matching: same department + same year + same semester
                $matchingCourses = $this->db->all(
                    "SELECT id, code, name FROM courses
                      WHERE department_id  = ?
                        AND year_of_study  = ?
                        AND semester       = ?
                        AND is_active      = 1",
                    [$deptId, $year, $semester]
                );

                foreach ($matchingCourses as $course) {
                    // Avoid duplicate enrollment
                    $exists = $this->db->single(
                        "SELECT id FROM enrollments WHERE student_id = ? AND course_id = ?",
                        [$studentId, $course['id']]
                    );
                    if (!$exists) {
                        $this->db->insert(
                            "INSERT INTO enrollments (student_id, course_id) VALUES (?,?)",
                            [$studentId, $course['id']]
                        );
                        $autoEnrolled++;
                    }
                }

                if (empty($matchingCourses)) {
                    $errors[] = "Row $lineNum: '$name' added but NO matching courses found for Dept=$deptCode Year=$year Sem=$semester";
                }

            } catch (Throwable $e) {
                $errors[] = "Row $lineNum: Failed to save '$name' — " . $e->getMessage();
                $failed++;
            }
        }

        // Log
        $this->db->insert(
            "INSERT INTO import_logs (user_id, type, filename, total_rows, success, failed, errors)
             VALUES (?,?,?,?,?,?,?)",
            [
                Auth::id(), 'students',
                $_FILES['file']['name'],
                $success + $failed,
                $success, $failed,
                $errors ? implode("\n", $errors) : null
            ]
        );

        $msg = "✅ $success students imported, $autoEnrolled auto-enrollments created, $failed failed.";
        $this->flash($failed === 0 ? 'success' : 'error', $msg);
        require_once APP_PATH . '/services/AuditService.php';
        AuditService::record(
        'import.students',
        'import',
        "Bulk import: $success students added, $failed failed from file: " . $_FILES['file']['name']
    );
        $this->redirect('/admin/import');
    }

    // POST /admin/import/lecturers
    public function importLecturers(): void
    {
        $token = $_POST['_csrf'] ?? '';
        if (empty($token) || !Auth::verifyCsrfToken($token)) {
            $this->flash('error', 'Session expired. Please try again.');
            $this->redirect('/admin/import');
        }

        if (empty($_FILES['file']['tmp_name'])) {
            $this->flash('error', 'No file uploaded.');
            $this->redirect('/admin/import');
        }

        $rows    = $this->parseFile($_FILES['file']['tmp_name'], $_FILES['file']['name']);
        $success = 0;
        $failed  = 0;
        $errors  = [];

        foreach ($rows as $i => $row) {
            $lineNum = $i + 2;

            if (empty($row) || (isset($row[0]) && str_starts_with(trim((string)$row[0]), '#'))) {
                continue;
            }

            $name     = trim($row['full_name']       ?? $row[0] ?? '');
            $email    = strtolower(trim($row['email']         ?? $row[1] ?? ''));
            $staffNo  = trim($row['staff_number']    ?? $row[2] ?? '');
            $pass     = trim($row['password']        ?? $row[3] ?? 'changeme123');
            $deptCode = strtoupper(trim($row['department_code'] ?? $row[4] ?? ''));
            $phone    = trim($row['phone']           ?? $row[5] ?? '');

            if (!$name || !$email || !$staffNo) {
                $errors[] = "Row $lineNum: Missing required fields (name, email or staff_number)";
                $failed++;
                continue;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Row $lineNum: Invalid email '$email'";
                $failed++;
                continue;
            }

            if ($this->db->single("SELECT id FROM users WHERE email = ?", [$email])) {
                $errors[] = "Row $lineNum: Email '$email' already registered — skipped";
                $failed++;
                continue;
            }

            if ($this->db->single("SELECT id FROM lecturers WHERE staff_number = ?", [$staffNo])) {
                $errors[] = "Row $lineNum: Staff number '$staffNo' already exists — skipped";
                $failed++;
                continue;
            }

            // Find department
            $deptId = null;
            if ($deptCode) {
                $dept = $this->db->single("SELECT id FROM departments WHERE code = ?", [$deptCode]);
                if (!$dept) {
                    $errors[] = "Row $lineNum: Department '$deptCode' not found — lecturer added without department";
                }
                $deptId = $dept['id'] ?? null;
            }

            try {
                $this->db->transaction(function($db) use ($name, $email, $pass, $staffNo, $deptId, $phone) {
                    $userId = $db->insert(
                        "INSERT INTO users (name, email, password, role) VALUES (?,?,?,?)",
                        [$name, $email, password_hash($pass, PASSWORD_BCRYPT), 'lecturer']
                    );
                    $db->insert(
                        "INSERT INTO lecturers (user_id, staff_number, department_id, phone)
                         VALUES (?,?,?,?)",
                        [$userId, $staffNo, $deptId, $phone ?: null]
                    );
                });
                $success++;
            } catch (Throwable $e) {
                $errors[] = "Row $lineNum: Failed to save '$name' — " . $e->getMessage();
                $failed++;
            }
        }

        $this->db->insert(
            "INSERT INTO import_logs (user_id, type, filename, total_rows, success, failed, errors)
             VALUES (?,?,?,?,?,?,?)",
            [
                Auth::id(), 'lecturers',
                $_FILES['file']['name'],
                $success + $failed,
                $success, $failed,
                $errors ? implode("\n", $errors) : null
            ]
        );

        $this->flash($failed === 0 ? 'success' : 'error',
            "✅ $success lecturers imported, $failed failed.");
            require_once APP_PATH . '/services/AuditService.php';
            AuditService::record(
                'import.lecturers',
                'import',
                "Bulk import: $success lecturers added, $failed failed from file: " . $_FILES['file']['name']
            );
        $this->redirect('/admin/import');
    }

    // POST /admin/import/courses
    public function importCourses(): void
    {
        $token = $_POST['_csrf'] ?? '';
        if (empty($token) || !Auth::verifyCsrfToken($token)) {
            $this->flash('error', 'Session expired. Please try again.');
            $this->redirect('/admin/import');
        }

        if (empty($_FILES['file']['tmp_name'])) {
            $this->flash('error', 'No file uploaded.');
            $this->redirect('/admin/import');
        }

        $rows    = $this->parseFile($_FILES['file']['tmp_name'], $_FILES['file']['name']);
        $success = 0;
        $failed  = 0;
        $errors  = [];
        $autoEnrolled = 0;

        foreach ($rows as $i => $row) {
            $lineNum = $i + 2;

            if (empty($row) || (isset($row[0]) && str_starts_with(trim((string)$row[0]), '#'))) {
                continue;
            }

            $code     = strtoupper(trim($row['code']             ?? $row[0] ?? ''));
            $name     = trim($row['name']                        ?? $row[1] ?? '');
            $deptCode = strtoupper(trim($row['department_code']  ?? $row[2] ?? ''));
            $staffNo  = trim($row['staff_number']                ?? $row[3] ?? '');
            $semester = (int)($row['semester']                   ?? $row[4] ?? 1);
            $year     = (int)($row['year_of_study']              ?? $row[5] ?? 0);
            $credits  = (int)($row['credit_hours']               ?? $row[6] ?? 3);
            $acadYear = trim($row['academic_year']               ?? $row[7] ?? '');

            if (!$code || !$name) {
                $errors[] = "Row $lineNum: Missing code or name";
                $failed++;
                continue;
            }

            if ($this->db->single("SELECT id FROM courses WHERE code = ?", [$code])) {
                $errors[] = "Row $lineNum: Course '$code' already exists — skipped";
                $failed++;
                continue;
            }

            $deptId = null;
            if ($deptCode) {
                $dept = $this->db->single("SELECT id FROM departments WHERE code = ?", [$deptCode]);
                $deptId = $dept['id'] ?? null;
                if (!$dept) $errors[] = "Row $lineNum: Department '$deptCode' not found";
            }

            $lecturerId = null;
            if ($staffNo) {
                $lec = $this->db->single("SELECT id FROM lecturers WHERE staff_number = ?", [$staffNo]);
                $lecturerId = $lec['id'] ?? null;
                if (!$lec) $errors[] = "Row $lineNum: Staff number '$staffNo' not found";
            }

            try {
                $courseId = $this->db->insert(
                    "INSERT INTO courses
                        (code, name, department_id, lecturer_id, semester,
                         year_of_study, credit_hours, academic_year)
                     VALUES (?,?,?,?,?,?,?,?)",
                    [$code, $name, $deptId, $lecturerId,
                     $semester ?: 1, $year ?: null, $credits ?: 3, $acadYear ?: null]
                );

                $success++;

                // ── AUTO-ENROLL existing students matching dept + year + semester ──
                if ($deptId && $year && $semester) {
                    $matchingStudents = $this->db->all(
                        "SELECT s.id FROM students s
                          WHERE s.department_id = ?
                            AND s.year_of_study = ?",
                        [$deptId, $year]
                    );

                    foreach ($matchingStudents as $stu) {
                        $exists = $this->db->single(
                            "SELECT id FROM enrollments WHERE student_id = ? AND course_id = ?",
                            [$stu['id'], $courseId]
                        );
                        if (!$exists) {
                            $this->db->insert(
                                "INSERT INTO enrollments (student_id, course_id) VALUES (?,?)",
                                [$stu['id'], $courseId]
                            );
                            $autoEnrolled++;
                        }
                    }
                }

            } catch (Throwable $e) {
                $errors[] = "Row $lineNum: Failed '$code' — " . $e->getMessage();
                $failed++;
            }
        }

        $this->db->insert(
            "INSERT INTO import_logs (user_id, type, filename, total_rows, success, failed, errors)
             VALUES (?,?,?,?,?,?,?)",
            [
                Auth::id(), 'courses',
                $_FILES['file']['name'],
                $success + $failed,
                $success, $failed,
                $errors ? implode("\n", $errors) : null
            ]
        );

        $this->flash($failed === 0 ? 'success' : 'error',
            "✅ $success courses imported, $autoEnrolled auto-enrollments created, $failed failed.");
            require_once APP_PATH . '/services/AuditService.php';
            AuditService::record(
                'import.courses',
                'import',
                "Bulk import: $success courses added, $failed failed from file: " . $_FILES['file']['name']
            );
        $this->redirect('/admin/import');
    }

    // GET /admin/import/logs
    public function logs(): void
    {
        $logs = $this->db->all(
            "SELECT il.*, u.name AS imported_by
               FROM import_logs il
               JOIN users u ON u.id = il.user_id
              ORDER BY il.created_at DESC"
        );

        $this->view('admin/import_logs', [
            'user'  => Auth::user(),
            'logs'  => $logs,
            'flash' => $this->getFlash(),
        ]);
    }

    // ── File Parser ───────────────────────────────────────────────────────────

    private function parseFile(string $tmpPath, string $originalName): array
    {
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        return ($ext === 'xlsx' || $ext === 'xls')
            ? $this->parseXlsx($tmpPath)
            : $this->parseCsv($tmpPath);
    }

    private function parseCsv(string $path): array
    {
        $rows    = [];
        $headers = [];
        $handle  = fopen($path, 'r');
        if (!$handle) return [];

        while (($data = fgetcsv($handle)) !== false) {
            if (isset($data[0]) && str_starts_with(trim((string)$data[0]), '#')) continue;
            if (empty(array_filter($data, fn($v) => $v !== null && $v !== ''))) continue;

            if (empty($headers)) {
                $headers = array_map('trim', $data);
                continue;
            }

            $row = [];
            foreach ($headers as $i => $header) {
                $row[$header] = trim($data[$i] ?? '');
            }
            $rows[] = $row;
        }

        fclose($handle);
        return $rows;
    }

    private function parseXlsx(string $path): array
    {
        if (class_exists('PhpOffice\PhpSpreadsheet\IOFactory')) {
            try {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($path);
                $sheet       = $spreadsheet->getActiveSheet();
                $data        = $sheet->toArray(null, true, true, false);
                $headers     = [];
                $rows        = [];

                foreach ($data as $row) {
                    if (isset($row[0]) && str_starts_with(trim((string)$row[0]), '#')) continue;
                    if (empty(array_filter($row, fn($v) => $v !== null && $v !== ''))) continue;

                    if (empty($headers)) {
                        $headers = array_map('trim', $row);
                        continue;
                    }

                    $mapped = [];
                    foreach ($headers as $j => $header) {
                        $mapped[$header] = trim((string)($row[$j] ?? ''));
                    }
                    $rows[] = $mapped;
                }
                return $rows;
            } catch (Throwable $e) {
                error_log('XLSX error: ' . $e->getMessage());
            }
        }
        return $this->parseCsv($path);
    }
}