FACULTY CLASS ENGAGEMENT MANAGEMENT SYSTEM
===========================================

TECHNOLOGY
- PHP 8.x (PDO)
- MySQL / MariaDB
- HTML, CSS, JavaScript
- Designed for XAMPP on Windows

FEATURES
- Admin and faculty login roles
- Department creation and deletion
- Faculty account registration and activation/deactivation
- Admin records each faculty member's status by date and period
- Calendar date picker (HTML date input)
- Daily, weekly and monthly reports
- Faculty and department report filters for administrators
- Faculty users can view their own records only
- CSV export opens in Microsoft Excel
- Print / Save PDF using the browser's print dialog

INSTALLATION IN XAMPP
1. Copy the folder "faculty_engagement_system" into:
   C:\xampp\htdocs\
2. Start Apache and MySQL from XAMPP Control Panel.
3. Open http://localhost/phpmyadmin
4. Import schema.sql (or open the SQL tab and run its contents).
   It creates the database faculty_engagement and its tables.
5. Edit config.php if your MySQL username/password is different.
   Default XAMPP settings are root with an empty password.
6. Open http://localhost/faculty_engagement_system/install_admin.php
7. Create the first administrator username and password.
8. Sign in at http://localhost/faculty_engagement_system/login.php
9. IMPORTANT: Delete install_admin.php after creating the administrator.

FIRST USE
1. Sign in as administrator.
2. Add departments.
3. Register faculty accounts. Give each faculty member a username and initial password.
4. Open Daily period entry.
5. Choose a date and period, set each faculty member's status, and save.
6. Use Reports to generate daily, weekly or monthly reports.
7. Export Excel (CSV), or choose Print / Save PDF and select "Save as PDF".

ROLES
- Administrator: manage departments and faculty, record/edit engagement, view all reports.
- Faculty: sign in and view their own engagement records/reports. Faculty cannot enter or edit records in this version.

PERIODS
The initial list is Period 1 through Period 8. To change the list, edit the $periods array in entry.php.

IMPORTANT NOTES
- One record is allowed per faculty + date + period. Saving the same combination again updates it.
- In this workflow, admin records both Engaged and Not Engaged for each faculty/period.
- CSV is an Excel-compatible format. It is not a native .xlsx workbook.
- PDF export uses the browser print feature; choose Save as PDF in the print destination.
- Before using this on the public internet, enable HTTPS, set a strong database password, remove install_admin.php, and consider adding password-change/reset and audit-log features.
- For hosting, configure database credentials using environment variables or hosting secrets; do not upload real credentials to a public Git repository.
