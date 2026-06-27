
# 🚗 Auto École Pro

A complete management system for driving schools. Built with PHP, MySQL, Bootstrap 5, and real-time communication features.

![Version](https://img.shields.io/badge/version-1.0-blue)
![PHP](https://img.shields.io/badge/PHP-8.2-purple)
![MySQL](https://img.shields.io/badge/MySQL-8.0-orange)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-blueviolet)
![License](https://img.shields.io/badge/license-MIT-green)

---

## ✨ Features

### 📊 Dashboard & Analytics
- Real-time KPI cards (students, instructors, vehicles, revenue)
- Monthly revenue bar charts (Chart.js)
- Exam eligibility doughnut charts
- Recent payments & upcoming lessons widgets
- Quick action buttons

### 👥 User Management
- 6 role-based access levels: Admin, Director, Secretary, Cashier, Instructor, Trainee
- Granular permissions per role
- Account creation, modification, renewal, blocking/unblocking
- Auto-lock after 5 failed login attempts (15 minutes)
- Two-factor authentication (2FA) via email OTP
- Password reset via email tokens
- Profile photo upload with auto-resize
- Session timeout (20 minutes inactivity)

### 👤 Student Management
- CRUD operations with soft delete (trash bin)
- Student profiles with payment history, lesson history, comments
- Auto-generated matricule numbers
- Advanced search with autocomplete suggestions
- Sortable table columns
- Pagination (10 per page)
- Archive students (+6 months deleted)

### 👨‍🏫 Instructor Management
- CRUD operations with archive support
- Experience tracking (cannot be reduced)
- Phone number validation
- Auto-uppercase names
- Sortable, searchable, paginated list

### 🚘 Vehicle Management
- CRUD operations with archive support
- Availability toggling
- Unique immatriculation validation
- Sortable, searchable, paginated list

### 📅 Lesson Scheduling
- Schedule lessons (student + instructor + vehicle + date/time)
- Conflict detection: prevents double-booking within 1-hour window
- Mark as completed / cancel / delete
- Filter by status (scheduled, completed, cancelled)
- FullCalendar weekly/monthly/daily view
- Sortable, searchable, paginated list

### 💰 Payment Management
- Record payments (amount, date, method)
- Printable payment receipts with school branding
- Revenue statistics (collected vs expected vs outstanding)
- Payment method tracking (Cash, Mobile Money, Bank Card, Transfer)
- Archive old payments (+12 months)
- Sortable, searchable, paginated list

### 📝 Exam Tracking
- Eligibility tracking (≥3 completed lessons)
- Pass/fail results recording
- Score tracking (/100)
- Exam center and comments
- Per-formation eligibility statistics

### 📁 Document Management
- Upload documents (PDF, JPG, PNG, DOCX — 5MB max)
- Automatic versioning (v1, v2, v3...)
- Download with original filename
- Share via 7-day expiring token links
- AJAX search with autocomplete suggestions
- Filter by document type and student
- Archive and restore documents

### 💬 Real-Time Chat & Calls
- Instant messaging with AJAX polling (3-second refresh)
- WebRTC audio/video calls (peer-to-peer)
- WebSocket signaling server (Node.js on port 8080)
- Typing indicators
- Message reactions (👍❤️😂😮😢🙏)
- Delete for me / delete for all (10-minute window)
- Unread message counter
- File sharing in chat (images, documents — 20MB max)
- User profile modal from chat header

### 📊 Reports & Statistics
- Monthly revenue report with year selector
- Custom date range report with formation filter
- Comparative analysis by formation (chart + table)
- Statistical forecasts (3/6/12 month projections)
- Excel/CSV export
- Printable PDF reports
- Revenue charts with Chart.js

### 🔔 Alerts & Notifications
- Internal notification system for admins
- Automatic alerts: unpaid balances, expired accounts, upcoming lessons
- Unread notification counter in sidebar
- Email notifications (SMTP Gmail compatible)

### 💾 Backup & Archives
- Manual database backup via mysqldump
- Download backup files (.sql)
- Restore from uploaded SQL file
- Auto-purge old backups (keeps last 7)
- Archive system: students, lessons, payments, documents
- Archive restoration

### 🌙 UI/UX
- Responsive design (mobile, tablet, desktop)
- Dark mode toggle (persisted in localStorage)
- Fixed sidebar with collapsible mobile menu
- Bootstrap 5 cards, badges, modals, tooltips
- Sortable table columns (click header to sort)
- Pagination on all list pages (10-20 per page)
- Empty states with icons and messages
- Breadcrumb navigation
- PWA manifest (installable on mobile)

### 🔐 Security
- CSRF protection on all POST forms
- Bcrypt password hashing
- Session timeout (20 min auto-logout)
- X-Frame-Options, X-Content-Type-Options headers
- .env file for credentials (excluded from Git)
- Auto-lock after failed attempts
- 2FA via email OTP
- Input sanitization (htmlspecialchars)
- SQL injection prevention (prepared statements)

### 🗄️ Database Architecture
- All SELECT queries use SQL Views only
- All INSERT/UPDATE/DELETE use Stored Procedures
- Triggers for data integrity
- Events for scheduled tasks
- Soft delete with trash bin
- Audit trails for CRUD operations
- Connection journaling

---

## 🏗️ Tech Stack

| Layer | Technology |
|-------|------------|
| **Backend** | PHP 8.2, MySQL 8.0 |
| **Frontend** | Bootstrap 5.3, Bootstrap Icons, Chart.js, FullCalendar |
| **Real-time** | WebSocket (Node.js + ws), WebRTC |
| **Libraries** | jQuery, DataTables |
| **Email** | PHP mail() / SMTP Gmail |
| **Charts** | Chart.js 4 |
| **Calendar** | FullCalendar 6 |
| **PDF** | Browser print to PDF |
| **Excel** | CSV export |

---

## 📋 Requirements

- PHP 8.0+
- MySQL 5.7+ / MariaDB 10.3+
- Apache with mod_rewrite enabled
- Node.js 18+ (for WebSocket server — chat & calls)
- Composer (optional, for PHPMailer)
- npm (for frontend dependencies)

---

## 🚀 Quick Start

### 1. Clone the repository
```bash
git clone https://github.com/yourusername/project_auto.git
cd auto-ecole-pro
```

### 2. Install dependencies
```bash
npm install
```

### 3. Configure environment
```bash
cp .env.example .env
# Edit .env with your database credentials
```

### 4. Import database
```bash
mysql -u root -p your_database < database.sql
```

### 5. Set permissions
```bash
chmod -R 777 uploads/ logs/ backups/
```

### 6. Start WebSocket server (for chat & calls)
```bash
node server.js
```

### 7. Access the application
```
http://localhost/project_auto/pages/login.php
```

**Default admin credentials:**
- Username: `admin`
- Password: `your password hashed stored in your db`

---

## 📁 Project Structure

```
project_auto/
├── assets/
│   ├── css/
│   │   └── style.css
│   ├── images/
│   └── js/
├── config/
│   └── database.php
├── includes/
│   ├── auth.php
│   ├── footer.php
│   ├── header.php
│   ├── mailer.php
│   └── sidebar.php
├── pages/
│   ├── actions/
│   │   ├── calendar_events.php
│   │   ├── chat_actions.php
│   │   ├── download_document.php
│   │   ├── logout.php
│   │   └── shared_doc.php
│   ├── 404.php
│   ├── aide.php
│   ├── alertes.php
│   ├── archives.php
│   ├── backup.php
│   ├── calendar.php
│   ├── chat.php
│   ├── corbeille.php
│   ├── documents.php
│   ├── enrollments.php
│   ├── exams.php
│   ├── export.php
│   ├── forgot_password.php
│   ├── instructors.php
│   ├── lessons.php
│   ├── login.php
│   ├── notifications.php
│   ├── payments.php
│   ├── presentation.php
│   ├── profile.php
│   ├── rapport_pdf.php
│   ├── rapports.php
│   ├── recu_paiement.php
│   ├── reset_password.php
│   ├── search.php
│   ├── settings.php
│   ├── student_profile.php
│   ├── students.php
│   ├── uml.php
│   ├── vehicles.php
│   └── verify_2fa.php
├── uploads/
│   ├── documents/
│   ├── profiles/
│   └── backups/
├── .env
├── .env.example
├── .htaccess
├── backup.sh
├── index.php
├── manifest.json
├── package.json
├── server.js
└── README.md
```

---

## 🔑 Default Roles & Permissions

| Role | Permissions |
|------|-------------|
| 🔴 **Admin** | Full access: manage accounts, settings, all CRUD, reports |
| 🟤 **Director** | Like Admin except user account management |
| 🔵 **Secretary** | Students, lessons, payments, enrollments, documents |
| 🟢 **Cashier** | Payments only + read access |
| 🟡 **Instructor** | Lessons only + read access |
| ⚫ **Trainee** | Read-only access to all modules |

---

## 🛠️ Configuration

### School Settings
Navigate to **Settings → School Configuration** to customize:
- School name
- Phone number
- Email address
- Physical address
- Currency symbol
- SMTP credentials (for email sending)

### 2FA Activation
```sql
-- Enable
UPDATE config_systeme SET valeur = '1' WHERE cle = '2fa_active';

-- Disable
UPDATE config_systeme SET valeur = '0' WHERE cle = '2fa_active';
```

### Dark Mode
Click the 🌓 toggle button in the sidebar header. Preference is saved in localStorage.

---

## 📱 Mobile Access

The application is fully responsive and can be accessed from any device on the same network:

```bash
# Find server IP
ip addr show

# Access from mobile browser
http://192.168.x.x/project_auto
```

For WebRTC calls on mobile (HTTP only):
1. Open Chrome
2. Go to `chrome://flags/#unsafely-treat-insecure-origin-as-secure`
3. Add `http://YOUR_IP:8080`
4. Enable and restart Chrome

---

## 🔧 Deployment (alwaysdata)

1. Create site in alwaysdata panel (Apache/PHP 8.2)
2. Create MySQL database
3. Upload files via rsync (exclude `node_modules`)
4. Set `DB_HOST=/var/run/mysqld/mysqld.sock` in `.env`
5. Import database via phpMyAdmin
6. Recreate stored procedures without DEFINER
7. Correct collations: `utf8mb4_unicode_ci`
8. Set permissions: `chmod -R 777 uploads/ logs/`

---

## 📄 License

MIT License — feel free to use, modify, and distribute.

---

## 👨‍💻 Author

Developed by Monami Jerome with ❤️ for driving schools.

---

## 🆘 Support

For issues, questions, or contributions:
- Check the **Guide** page in the application
- View **UML Diagrams** for system architecture
- Consult the **About** page for tech stack details
