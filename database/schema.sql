-- ============================================================
--  QR Attendance System - Full Database Schema
--  Engine: MySQL 8.0+
-- ============================================================

CREATE DATABASE IF NOT EXISTS qr_attendance
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE qr_attendance;

-- ------------------------------------------------------------
-- 1. USERS  (single auth table, role-based)
-- ------------------------------------------------------------
CREATE TABLE users (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name         VARCHAR(120)  NOT NULL,
    email        VARCHAR(180)  NOT NULL UNIQUE,
    password     VARCHAR(255)  NOT NULL,          -- bcrypt hash
    role         ENUM('admin','lecturer','student') NOT NULL,
    is_active    TINYINT(1)    NOT NULL DEFAULT 1,
    created_at   TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ------------------------------------------------------------
-- 2. DEPARTMENTS
-- ------------------------------------------------------------
CREATE TABLE departments (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name         VARCHAR(120) NOT NULL UNIQUE,
    code         VARCHAR(20)  NOT NULL UNIQUE,
    created_at   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- ------------------------------------------------------------
-- 3. LECTURERS  (extends users)
-- ------------------------------------------------------------
CREATE TABLE lecturers (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id         INT UNSIGNED NOT NULL UNIQUE,
    department_id   INT UNSIGNED,
    staff_number    VARCHAR(50)  NOT NULL UNIQUE,
    phone           VARCHAR(30),
    created_at      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_lecturer_user   FOREIGN KEY (user_id)       REFERENCES users(id)       ON DELETE CASCADE,
    CONSTRAINT fk_lecturer_dept   FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL
);

-- ------------------------------------------------------------
-- 4. STUDENTS  (extends users)
-- ------------------------------------------------------------
CREATE TABLE students (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id         INT UNSIGNED NOT NULL UNIQUE,
    department_id   INT UNSIGNED,
    student_number  VARCHAR(50)  NOT NULL UNIQUE,
    year_of_study   TINYINT UNSIGNED,
    phone           VARCHAR(30),
    created_at      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_student_user  FOREIGN KEY (user_id)       REFERENCES users(id)       ON DELETE CASCADE,
    CONSTRAINT fk_student_dept  FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL
);

-- ------------------------------------------------------------
-- 5. COURSES
-- ------------------------------------------------------------
CREATE TABLE courses (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    department_id   INT UNSIGNED,
    lecturer_id     INT UNSIGNED,
    code            VARCHAR(30)  NOT NULL UNIQUE,
    name            VARCHAR(180) NOT NULL,
    description     TEXT,
    credit_hours    TINYINT UNSIGNED DEFAULT 3,
    semester        TINYINT UNSIGNED,              -- 1 or 2
    academic_year   VARCHAR(10),                   -- e.g. 2024/2025
    is_active       TINYINT(1)   NOT NULL DEFAULT 1,
    created_at      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_course_dept     FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL,
    CONSTRAINT fk_course_lecturer FOREIGN KEY (lecturer_id)   REFERENCES lecturers(id)   ON DELETE SET NULL
);

-- ------------------------------------------------------------
-- 6. COURSE ENROLLMENTS  (students <-> courses)
-- ------------------------------------------------------------
CREATE TABLE enrollments (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_id  INT UNSIGNED NOT NULL,
    course_id   INT UNSIGNED NOT NULL,
    enrolled_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY uq_enrollment (student_id, course_id),
    CONSTRAINT fk_enroll_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    CONSTRAINT fk_enroll_course  FOREIGN KEY (course_id)  REFERENCES courses(id)  ON DELETE CASCADE
);

-- ------------------------------------------------------------
-- 7. SESSIONS  (each individual class meeting)
-- ------------------------------------------------------------
CREATE TABLE sessions (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    course_id       INT UNSIGNED  NOT NULL,
    lecturer_id     INT UNSIGNED  NOT NULL,
    title           VARCHAR(180),                  -- e.g. "Week 3 – Lecture"
    session_date    DATE          NOT NULL,
    start_time      TIME          NOT NULL,
    end_time        TIME          NOT NULL,
    qr_token        VARCHAR(64)   NOT NULL UNIQUE, -- signed token embedded in QR
    qr_expires_at   TIMESTAMP     NOT NULL,        -- when scanning closes
    qr_image_path   VARCHAR(255),                  -- path under storage/qr_codes/
    status          ENUM('pending','active','closed') NOT NULL DEFAULT 'pending',
    created_at      TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_session_course   FOREIGN KEY (course_id)   REFERENCES courses(id)   ON DELETE CASCADE,
    CONSTRAINT fk_session_lecturer FOREIGN KEY (lecturer_id) REFERENCES lecturers(id) ON DELETE CASCADE,

    INDEX idx_session_date    (session_date),
    INDEX idx_session_status  (status),
    INDEX idx_session_token   (qr_token)
);

-- ------------------------------------------------------------
-- 8. ATTENDANCE  (one row per student per session)
-- ------------------------------------------------------------
CREATE TABLE attendance (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    session_id    INT UNSIGNED  NOT NULL,
    student_id    INT UNSIGNED  NOT NULL,
    scanned_at    TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ip_address    VARCHAR(45),                     -- IPv4 or IPv6
    device_info   VARCHAR(255),                    -- User-Agent snippet
    status        ENUM('present','late','excused') NOT NULL DEFAULT 'present',
    notes         VARCHAR(255),

    UNIQUE KEY uq_attendance (session_id, student_id),  -- no double-scanning
    CONSTRAINT fk_att_session FOREIGN KEY (session_id) REFERENCES sessions(id)  ON DELETE CASCADE,
    CONSTRAINT fk_att_student FOREIGN KEY (student_id) REFERENCES students(id)  ON DELETE CASCADE,

    INDEX idx_att_student  (student_id),
    INDEX idx_att_session  (session_id),
    INDEX idx_att_scanned  (scanned_at)
);

-- ------------------------------------------------------------
-- 9. PASSWORD RESET TOKENS
-- ------------------------------------------------------------
CREATE TABLE password_resets (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     INT UNSIGNED  NOT NULL,
    token       VARCHAR(100)  NOT NULL UNIQUE,
    expires_at  TIMESTAMP     NOT NULL,
    used        TINYINT(1)    NOT NULL DEFAULT 0,
    created_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_reset_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ------------------------------------------------------------
-- 10. AUDIT LOG  (optional but recommended)
-- ------------------------------------------------------------
CREATE TABLE audit_logs (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     INT UNSIGNED,
    action      VARCHAR(100) NOT NULL,            -- e.g. 'session.created'
    description TEXT,
    ip_address  VARCHAR(45),
    created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_audit_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_audit_user   (user_id),
    INDEX idx_audit_action (action)
);

-- ============================================================
--  SEED DATA – default admin account
--  Password: Admin@1234  (change immediately after first login)
-- ============================================================
INSERT INTO users (name, email, password, role) VALUES
('System Admin', 'admin@university.edu',
 '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- bcrypt of 'Admin@1234'
 'admin');

INSERT INTO departments (name, code) VALUES
('Computer Science', 'CS'),
('Information Technology', 'IT'),
('Business Administration', 'BA');
