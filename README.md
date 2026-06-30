# QR Code Attendance Management System

A web-based attendance tracking system for academic institutions using QR codes.

## Tech Stack
- **Backend:** PHP 8.1+ (no framework)
- **Database:** MySQL 8.0+
- **Frontend:** Vanilla HTML/CSS/JS
- **Server:** Apache (XAMPP for local dev)

## Roles
| Role | Can Do |
|------|--------|
| Admin | Manage users, courses, departments, view all reports |
| Lecturer | Create sessions, generate QR codes, view attendance |
| Student | Scan QR codes to mark attendance, view own history |

## Setup (XAMPP)

```bash
# 1. Place project in htdocs
C:\xampp\htdocs\attendance_system\

# 2. Import database
# Open phpMyAdmin → Import → select database/schema.sql

# 3. Copy environment file
cp .env.example .env
# Edit .env with your DB credentials

# 4. Enable mod_rewrite in httpd.conf
# AllowOverride All  (under htdocs Directory block)

# 5. Visit
http://localhost/attendance_system/public/
```

## Default Login
- **Email:** admin@university.edu
- **Password:** admin123

## How Attendance Works
1. Lecturer creates a session for a course
2. System generates a unique QR code
3. Lecturer activates session and displays QR
4. Students scan QR with their phones
5. System validates: session active + student enrolled + not already scanned
6. Attendance recorded as Present or Late (after 15min grace period)
7. Lecturer closes session when done

## Project Structure
```
attendance_system/
├── app/
│   ├── api/          # AJAX endpoints
│   ├── config/       # Database config
│   ├── controllers/  # Route handlers
│   ├── core/         # Router, DB, Auth, Controller base
│   ├── middleware/   # Auth + Role guards
│   ├── models/       # Database query classes
│   ├── services/     # Business logic
│   └── views/        # PHP templates
├── database/
│   └── schema.sql    # Full database schema
├── public/           # Web root (point Apache here)
│   ├── assets/       # CSS, JS, images
│   └── index.php     # Single entry point
└── storage/
    ├── exports/      # Report exports
    ├── logs/         # Error logs
    └── qr_codes/     # Generated QR images
```
