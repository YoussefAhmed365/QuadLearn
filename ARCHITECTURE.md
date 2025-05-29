# QuadLearn Code Documentation

## Overview

QuadLearn is an educational web platform for secondary school students and teachers. It facilitates distance learning through video lessons, explanations, real-time notifications, and community features. The platform is built primarily using PHP for backend logic, MySQL as the database, and leverages Node.js (with websockets) for real-time notifications. The front-end is built with HTML, CSS, Bootstrap, and vanilla JavaScript.

---

## Core Features

- **Teacher Accounts:** Register, select subject, upload lesson videos, send notifications, manage students, upload test scores, and post in communities.
- **Student Accounts:** Register, search and apply for teachers, join communities, view lessons, interact via WhatsApp.
- **Teaching Assistants:** Support teachers in various platform activities.
- **Real-Time Notifications:** Handled via a Node.js WebSocket server.
- **Scalability & SEO:** Responsive design with optimized loading and resource handling.

---

## Main Directories & Files

### 1. `assets/`
Contains all static assets for the platform.
- **css/**: Stylesheets for various pages/components.
- **files/**: Uploaded files by users.
- **icons/**: SVG and other icon assets.
- **images/**: Images used throughout the site (includes subject images, logos, etc.).
- **js/**: JavaScript files for page interactivity and logic.
- **subject_files/**: Documents related to subjects.
- **videos/**: Preview and lesson videos.

### 2. `CORE/`
Key PHP and HTML files for main site functionality.
- **about.html, content.html, skills.html**: Static content pages.
- **db_connect.php**: Database connection logic.
- **helper_functions.php**: Commonly used PHP functions.
- **logout.php**: User logout logic.

### 3. `CORE/account_recover/`
Account recovery logic, including password reset flows.

### 4. `CORE/dashboard/`
Implements user dashboards.

- **student/**: Student dashboard logic and modules (notifications, lessons, communities, instructors, files, settings).
- **teacher/**: Teacher dashboard (notifications, communities, exams, files, lessons, user management, requests, settings).

### 5. `CORE/error/`
Custom error pages and error handling scripts.

### 6. `CORE/login/`, `CORE/signup/`
Login and signup logic and handlers.

### 7. `CORE/mail/`
PHPMailer library for sending emails.

---

## Key Backend Files

- **db_connect.php**: Handles MySQL database connections.
- **helper_functions.php**: Contains reusable utility functions shared across the application.
- **server.js**: Node.js WebSocket server for real-time notifications.

---

## Real-Time Notifications

- **server.js** (root): 
  - Implements websocket server using Node.js and the `ws` library.
  - Sends real-time notifications to students and teachers (e.g., new posts, application status).
  - Clients connect via JavaScript on the front-end.

---

## Page Routing and User Roles

- Students and teachers have separate dashboards and community modules, implemented under `CORE/dashboard/student/` and `CORE/dashboard/teacher/`.
- Each dashboard contains logic for notifications, posts, user management, test scores, and lesson videos.

---

## Database

- MySQL used as the database backend.
- **db_connect.php** is the entry point for all database interactions.
- Likely tables: users, lessons, videos, notifications, posts, test_scores, files, applications.

---

## Third-Party Libraries

- **PhpSpreadsheet**: Used for working with Excel/CSV files (import/export).
- **PHPMailer**: For sending notification emails.
- **ws**: Node.js WebSocket library.

---

## Example File Documentation

### `db_connect.php`
Handles all database connections. Uses mysqli or PDO for establishing secure connections to the MySQL database.

### `helper_functions.php`
A collection of PHP functions used throughout the platform for tasks such as input sanitization, session management, etc.

### `server.js`
Sets up a WebSocket server for real-time communication. Handles connection events, message broadcasting, and notification delivery.

---

## Front-End

- **HTML/CSS/Bootstrap**: For layouts and responsive UI.
- **Vanilla JavaScript**: For dynamic UI updates, AJAX calls, and WebSocket client logic.

---

## Setup & Deployment

- Developed locally using XAMPP (Apache & MySQL).
- Planned for deployment on a VPS (e.g., Hostinger).
- Web domain: `quadlearn.online`.
- Real-time WebSocket notifications require Node.js server to be running alongside the PHP backend.

---

## Contribution Guidelines

- Modular codebase: Each feature (e.g., notifications, video upload, test management) has its own directory/module.
- Use helper functions for code reuse.
- Maintain separation of concerns between front-end, backend, and WebSocket logic.

---

## Contact

For questions or contributions, contact:
- info@quadlearn.com
- info@quadlearn.online

---

## Additional Notes

- All icons and images are either custom-designed or royalty-free.
- The codebase is well-structured for scalability and further enhancements.
- Good SEO practices and fast page loading are prioritized throughout the platform.

---

## For Developers

To extend or modify QuadLearn:
- Start by exploring `CORE/` for main logic and flow.
- Look into `assets/js/` for client-side interactivity.
- `server.js` is the entry for real-time capabilities.
- Use `helper_functions.php` for any repeated backend logic.
- For new modules, follow the directory and naming conventions already established.
