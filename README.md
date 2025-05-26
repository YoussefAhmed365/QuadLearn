# Project Title: QuadLearn

## Introduction
An educational platform called "QuadLearn" for secondary school students, where teachers and students register to facilitate distance learning through lesson explanations, video lessons, and communication through teacher-student communities.

## Features

### For Teachers:
*   Register and choose the subject they will teach.
*   Upload lesson videos.
*   Send notifications to students.
*   Manage students (accept/reject applications).
*   Upload test scores.
*   Publish posts in student communities (including files and logos).

### For Students:
*   Register and join desired teachers by searching for their teacher code.
*   Submit applications to teachers.
*   Communicate with teachers via WhatsApp.
*   Access lesson explanations and video lessons.
*   Participate in teacher-student communities.

### For Teaching Assistants:
*   Sign up to support teachers.

## Tech Stack:
*   HTML
*   CSS
*   Bootstrap
*   Vanilla Javascript
*   Node.js (For websocket only to handle real-time notifications)
*   PHP
*   MySQL

## File Structure:
```
website
│
├───assets
│   │
│   ├───css
│   │   #default-styles.css
│   │   about.css
│   │   account-recovery.css
│   │   coming_tests.css
│   │   content.css
│   │   create_test.css
│   │   login.css
│   │   manage_quizzes.css
│   │   notification.css
│   │   requests.css
│   │   scommunity.css
│   │   sdash.css
│   │   show-teachers.css
│   │   show-users.css
│   │   signup.css
│   │   skills.css
│   │   ssettings.css
│   │   student_notifications.css
│   │   student_videos.css
│   │   style.css
│   │   subjects.css
│   │   subject_files.css
│   │   tcommunity.css
│   │   tdash.css
│   │   tsettings.css
│   │   update-table.css
│   │   uploadvideos.css
│   │
│   ├───files // Uploaded files by teachers and students in community
│   │
│   ├───icons
│   │   circle-play-solid.svg
│   │
│   ├───images
│   │   │ apple-touch-icon-180x180.ico
│   │   │ Arabic.webp
│   │   │ Biology.webp
│   │   │ Chemistry.webp
│   │   │ English.webp
│   │   │ favicon-16x16.ico
│   │   │ favicon-32x32.ico
│   │   │ favicon-48x48.ico
│   │   │ French.webp
│   │   │ Geography.webp
│   │   │ Geology.webp
│   │   │ German.webp
│   │   │ History.webp
│   │   │ Italy.webp
│   │   │ Learning-bro.svg
│   │   │ logo.png
│   │   │ logo.svg
│   │   │ Mathematics.webp
│   │   │ Philosophy.webp
│   │   │ Physics.webp
│   │   │ profile-photo.png
│   │   │ signup-bg.svg
│   │   │ Spanish.webp
│   │   │ study-bg.jpg
│   │   │ waves.svg
│   │   │
│   │   └───profiles // Profile pictures & default profile photo
│   │       default.png
│   │
│   ├───js
│   │   account-recovery.js
│   │   create_test.js
│   │   handle_student_notification_panel.js
│   │   handle_teacher_notification_panel.js
│   │   login.js
│   │   manage_quizzes.js
│   │   notification.js
│   │   requests.js
│   │   scommunity.js
│   │   script.js
│   │   sdash.js
│   │   show-teachers.js
│   │   show_users.js
│   │   signup.js
│   │   ssettings.js
│   │   student_notifications.js
│   │   student_videos.js
│   │   subjects.js
│   │   subject_files.js
│   │   tcommunity.js
│   │   tdash.js
│   │   test.js
│   │   tsettings.js
│   │   uploadvideos.js
│   │   usersTable.js
│   │
│   ├───subject_files // Uploaded files by teachers and students can access them
│   └───videos
│       │   preview.mp4
│       │
│       ├───first // lessons videos for level one with thier thumbnails
│       ├───second // lessons videos for level two with thier thumbnails
│       └───third // lessons videos for level three with thier thumbnails
├───CORE
│   │   about.html
│   │   content.html
│   │   db_connect.php
│   │   delete_account_handler.php
│   │   download.php
│   │   helper_functions.php
│   │   logout.php
│   │   skills.html
│   │
│   ├───account_recover
│   │   account-recovery.php
│   │   recovery-handler.php
│   │   reset_password.php
│   │   reset_password_action.php
│   │
│   ├───dashboard
│   │   ├───student
│   │   │   │   load_student_notification.php
│   │   │   │   mark_as_read_student.php
│   │   │   │   sauth.php
│   │   │   │
│   │   │   ├───community
│   │   │   │   get_posts.php
│   │   │   │   scommunity.php
│   │   │   │   sdelete_post.php
│   │   │   │   student_add_post.php
│   │   │   │   student_delete_post.php
│   │   │   │   student_edit_post.php
│   │   │   │   student_load_posts.php
│   │   │   │   student_save_post.php
│   │   │   │
│   │   │   ├───files
│   │   │   │   show-files-student.php
│   │   │   │   subjects.php
│   │   │   │
│   │   │   ├───instructors
│   │   │   │   add_teacher.php
│   │   │   │   delete-assigned-teacher.php
│   │   │   │   load_student_subjects.php
│   │   │   │   search_teacher_ajax.php
│   │   │   │   show-teachers.php
│   │   │   │
│   │   │   ├───lessons
│   │   │   │   load_student_videos.php
│   │   │   │   student_videos.php
│   │   │   │
│   │   │   ├───main
│   │   │   │   student-dashboard.php
│   │   │   │
│   │   │   ├───notifications
│   │   │   │   student_delete_notifications.php
│   │   │   │   student_load_notifications.php
│   │   │   │   student_mark_notification_as_read.php
│   │   │   │   student_notifications.php
│   │   │   │
│   │   │   └───settings
│   │   │       ssettings.php
│   │   │       student_profile_photo_handler.php
│   │   │       update-student-account.php
│   │   │
│   │   └───teacher
│   │       │   load_home_notification.php
│   │       │   mark_as_read.php
│   │       │   tauth.php
│   │       │
│   │       ├───community
│   │       │   add_post.php
│   │       │   delete_post.php
│   │       │   edit_post.php
│   │       │   load_posts.php
│   │       │   save_post.php
│   │       │   tcommunity.php
│   │       │
│   │       ├───exam
│   │       │   coming_tests.php
│   │       │   create_test.php
│   │       │   edit_test.php
│   │       │   manage_quizzes.php
│   │       │   process_form.php
│   │       │
│   │       ├───files
│   │       │   delete_file.php
│   │       │   delete_files.php
│   │       │   edit_file.php
│   │       │   search-subject-files.php
│   │       │   show-all-files.php
│   │       │   show-files.php
│   │       │   subject_files.php
│   │       │   upload-file.php
│   │       │
│   │       ├───lessons
│   │       │   delete_video.php
│   │       │   edit_video.php
│   │       │   get_video.php
│   │       │   load_videos.php
│   │       │   upload.php
│   │       │   uploadvideos.php
│   │       │
│   │       ├───main
│   │       │   teacher-dashboard.php
│   │       │
│   │       ├───manage_users
│   │       │   add-assistant.php
│   │       │   delete-all-students.php
│   │       │   delete-assistant.php
│   │       │   delete-student.php
│   │       │   show-assistants-table.php
│   │       │   show-students-table.php
│   │       │   show-users.php
│   │       │   update-table.php
│   │       │   upload_test_degree.php
│   │       │
│   │       ├───notifications
│   │       │   notification.php
│   │       │   notification_handler.php
│   │       │
│   │       ├───requests
│   │       │   requests.php
│   │       │   search_requests.php
│   │       │   update-request.php
│   │       │
│   │       └───settings
│   │           profile_photo_handler.php
│   │           tsettings.php
│   │           update_account_handler.php
│   │
│   ├───error
│   │   400.html
│   │   401.html
│   │   403.html
│   │   404.html
│   │   500.html
│   │   503.html
│   │   error_page.php
│   │
│   ├───login
│   │   login.php
│   │
│   ├───mail
│   │   Exception.php
│   │   PHPMailer.php
│   │   SMTP.php
│   │
│   └───signup
│       signup.php
│       signup_handler.php
│
│   index.html
│   server.js
```

## Hosting & Service:
*   Developed using Xampp (Apache & MySQL).
*   Planned for production on Hostinger VPS Hosting Service (KVM 1 or KVM 2 plan).
*   Desired domain: quadlearn.online
*   The platform uses a WebSocket service for real-time notifications.

## Contact:
These emails are desired for the platform:
*   info@quad.learn
*   info@quadlearn.com
*   info@quadlearn.online
