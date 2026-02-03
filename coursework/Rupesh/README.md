# Vehicle Rental Management System (VRMS)

A premium, web-based vehicle rental platform built with PHP, MySQL, and AJAX.

## Features
- **Modern UI**: Dark mode with Glassmorphism and responsive design.
- **User Authentication**: Secure registration and login for customers and admins.
- **Vehicle Catalog**: Search and filter vehicles by type and brand.
- **Booking System**: Real-time availability check via AJAX before booking.
- **Admin Dashboard**: Comprehensive stats and Management (CRUD) for vehicles and bookings.

## Setup Instructions
1. Copy the project folder to your local server directory (e.g., `htdocs` for XAMPP).
2. Ensure MySQL is running.
3. Open `includes/config.php` and update database credentials if necessary.
4. The system will automatically create the database `vrms_db` and required tables on first run.
5. Default Admin Credentials:
   - **Username**: `admin`
   - **Password**: `admin123`

## Technology Stack
- **Frontend**: HTML5, CSS3, JavaScript (jQuery)
- **Backend**: PHP 8+
- **Database**: MySQL (PDO)
- **AJAX**: jQuery AJAX for availability checks
