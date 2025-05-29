# QuadLearn Platform Documentation

## Overview

**QuadLearn** is a web-based educational platform designed for secondary school students and teachers. It enables distance learning through lesson explanations, video lessons, interactive communities, and real-time notifications. The platform is optimized for scalability, responsiveness, and search engine visibility.

---

## Table of Contents

1. [Features](#features)
2. [Technology Stack](#technology-stack)
3. [Directory & File Structure](#directory--file-structure)
4. [Database Schema](#database-schema)
5. [Key Modules and Their Roles](#key-modules-and-their-roles)
6. [Real-Time Notifications](#real-time-notifications)
7. [Setup & Deployment](#setup--deployment)
8. [Extending and Contributing](#extending-and-contributing)
9. [Contact](#contact)

---

## Features

- **User Roles:** Teachers, Students, Teaching Assistants
- **Teacher Features:**
  - Register and select subjects
  - Upload lesson videos and files
  - Send notifications to students
  - Manage students and teaching assistants
  - Publish posts and share resources in communities
  - Upload test scores and manage quizzes
- **Student Features:**
  - Register and search for teachers
  - Apply to join teachers' classes
  - Access lesson explanations and videos
  - View notifications and interact in communities
  - Communicate with teachers (including via WhatsApp)
- **Teaching Assistant Features:** Support teachers in content and student management
- **Community:** Teacher-student communities for posts, files, and interactive engagement
- **Real-Time Notifications:** Delivered via WebSockets (Node.js)

---

## Technology Stack

- **Frontend:** HTML, CSS, Bootstrap, JavaScript (vanilla)
- **Backend:** PHP (main logic), MySQL (database)
- **Real-time:** Node.js (for WebSocket notifications)
- **Libraries:** PHPMailer (mail), PhpSpreadsheet (Excel/CSV import/export)
- **Responsive Design:** Mobile and desktop friendly
- **SEO:** Optimized for search engines

---

## Directory & File Structure

The platform is organized for clarity and modularity. Below is a simplified structure:

```
website/
│
├── css/                # Stylesheets for all modules/pages
├── files/              # Uploaded files by teachers/students
├── icons/              # SVG and icon assets
├── images/             # Images (subjects, logos, backgrounds, etc.)
│   └── profiles/       # Profile pictures
├── js/                 # JavaScript for interactivity and page logic
├── videos/             # Lesson and preview videos
│   ├── first/          # Level 1 videos
│   ├── second/         # Level 2 videos
│   └── third/          # Level 3 videos
│
├── about.html
├── content.html
├── db_connect.php      # Database connection logic
├── delete_account_handler.php
├── download.php
├── helper_functions.php
├── logout.php
├── skills.html
│
├── account_recover/    # Password/Account recovery logic
│
├── dashboard/
│   ├── student/        # Student dashboard (community, files, lessons, notifications, settings, etc.)
│   └── teacher/        # Teacher dashboard (community, exams, files, lessons, users, requests, settings, etc.)
│
├── error/              # Custom error pages (400, 401, 403, 404, 500, 503)
│
├── login/              # Login logic
├── mail/               # Email sending (PHPMailer)
├── signup/             # Signup logic
│
├── index.html          # Landing page
├── server.js           # Node.js WebSocket server
```

---

## Database Schema

QuadLearn uses a normalized MySQL database. Main tables include:

- **users**: Common user info (id, username, email, password, type, etc.)
- **teachers**: Teacher-specific info (subject, title)
- **students**: Student-specific info (level, guardian info)
- **assigned_students/teachers/assistants**: Relationships between users
- **community**: Community posts, files, badges, etc.
- **saved_posts**: Posts saved by users
- **notifications**: Notifications from teachers
- **student_notifications/teacher_notifications**: Tracking notification delivery/read status
- **account_recovery_otps**: OTPs for account recovery
- **tests/questions/answers/question_choices/question_scores**: Quiz & exam logic
- **sessions**: PHP session data
- **subject_files**: Files for subjects
- **requests**: Student requests to join classes, with status
- **videos**: Lesson videos with metadata

**Sample Table: `users`**
```sql
CREATE TABLE users (
    id int(11) PRIMARY KEY AUTO_INCREMENT,
    username varchar(50) NOT NULL,
    password varchar(255) NOT NULL,
    email varchar(255) NOT NULL,
    first_name varchar(50) NOT NULL,
    last_name varchar(50) NOT NULL,
    phone_number varchar(20) NOT NULL,
    account_type enum('teacher','student') NOT NULL,
    gender enum('male','female') NOT NULL,
    picture varchar(255) DEFAULT NULL,
    bio text DEFAULT NULL,
    created_at timestamp NOT NULL DEFAULT current_timestamp(),
    updated_at timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```

*See the full list of tables and their relationships in the [Database Structure section above](#database-schema).*

---

## Key Modules and Their Roles

### Backend (PHP)
- **db_connect.php**: Sets up MySQL connection.
- **helper_functions.php**: Reusable PHP functions (e.g., input sanitization, session management).
- **dashboard/student/** and **dashboard/teacher/**: Role-specific modules for all user actions.
- **mail/**: PHPMailer for sending emails.
- **account_recover/**: OTP verification and password reset logic.
- **signup/** & **login/**: User registration and authentication.

### Frontend (HTML/JS/CSS)
- **HTML files**: Each page or component has a dedicated HTML file.
- **CSS**: Modular, page/component-specific styles.
- **JavaScript**: Handles dynamic UI, AJAX, notification panels, dashboards, file/video uploads, etc.

### Real-time (Node.js)
- **server.js**: WebSocket server for pushing real-time notifications to users.

---

## Real-Time Notifications

- **Node.js WebSocket server** (`server.js`): 
  - All notification events (e.g., new messages, accepted/rejected requests) are pushed instantly to the browser.
  - PHP backend triggers events via HTTP or direct socket communication.
  - Frontend JS listens for events and shows user notifications.

---

## Setup & Deployment

### 1. Requirements
- PHP 7+
- MySQL 5.7+
- Node.js (for WebSockets)
- Web server (Apache recommended, e.g., XAMPP for development)

### 2. Installation Steps
1. **Clone the repository**  
   `git clone https://github.com/YoussefAhmed365/QuadLearn.git`
2. **Import the database**  
   - Use the provided SQL schema (see [Database Schema](#database-schema)) to create all tables.
3. **Configure database connection**  
   - Edit `db_connect.php` with your MySQL credentials.
4. **Install Node.js dependencies**  
   - In the project root, run:  
     `npm install`
5. **Start WebSocket server**  
   - `node server.js`
6. **Deploy PHP files**  
   - Place all files in your web server's root directory.
7. **(Optional) Configure mail settings**  
   - Edit `/mail/PHPMailer.php` as needed for your SMTP server.

### 3. Running the Platform
- Access via your browser (e.g., `http://localhost/website/index.html`)
- Ensure both Apache (PHP backend) and Node.js (WebSocket server) are running.

---

## Extending and Contributing

- **Modular design:** Add new features by following the existing directory structure.
- **Backend:** Add new PHP files/modules in the appropriate dashboard or logic folder.
- **Frontend:** Add new JS/CSS files in the `js` or `css` directories.
- **Database:** Update the schema by adding new tables or fields as required.
- **Testing:** (Add your preferred testing strategy here, e.g., manual, PHPUnit, etc.)
- **Coding Standards:** Use clear, descriptive names and comments. Reuse helper functions where possible.

---

## Contact

For support, questions, or contribution:
- Email: info@quadlearn.com / info@quadlearn.online

---

**QuadLearn** is a solo-developed project—feedback and pull requests are welcome!