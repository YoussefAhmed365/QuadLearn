CREATE TABLE account_recovery_otps (
    id int(11) PRIMARY KEY AUTO_INCREMENT,
    email varchar(255) NOT NULL,
    otp varchar(10) NOT NULL,
    created_at datetime NOT NULL DEFAULT current_timestamp(),
    expires_at datetime NOT NULL,
    used tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE answers (
    id int(11) PRIMARY KEY AUTO_INCREMENT,
    question_id int(11) DEFAULT NULL,
    answer_text text DEFAULT NULL,
    CONSTRAINT answers_ibfk_1 FOREIGN KEY (question_id) REFERENCES questions (question_id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE assigned_assistants (
    id int(11) PRIMARY KEY AUTO_INCREMENT,
    teacher_id int(11) NOT NULL,
    assistant_id int(11) NOT NULL,
    assign_date timestamp NOT NULL DEFAULT current_timestamp(),
    CONSTRAINT assistant_id_ibfk1 FOREIGN KEY (assistant_id) REFERENCES teachers (id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT assistant_teacher_id_ibfk1 FOREIGN KEY (teacher_id) REFERENCES teachers (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE assigned_students (
    id int(11) PRIMARY KEY AUTO_INCREMENT,
    teacher_id int(11) NOT NULL,
    student_id int(11) NOT NULL,
    assign_date timestamp NOT NULL DEFAULT current_timestamp(),
    CONSTRAINT assigned_student_id_ibfk2 FOREIGN KEY (student_id) REFERENCES students (id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT main_teacher_id_ibfk1 FOREIGN KEY (teacher_id) REFERENCES teachers (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE assigned_teachers (
    id int(11) PRIMARY KEY AUTO_INCREMENT,
    student_id int(11) NOT NULL,
    teacher_id int(11) NOT NULL,
    assign_date timestamp NOT NULL DEFAULT current_timestamp(),
    CONSTRAINT student_id_ibfk1 FOREIGN KEY (student_id) REFERENCES students (id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT teacher_id_ibfk2 FOREIGN KEY (teacher_id) REFERENCES teachers (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE auth_tokens (
    id int(11) PRIMARY KEY AUTO_INCREMENT,
    selector varchar(255) NOT NULL,
    hashed_validator varchar(255) NOT NULL,
    user_id int(11) NOT NULL,
    expires datetime NOT NULL,
    CONSTRAINT auth_tokens_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE account_recovery_otps (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) NOT NULL,
    otp VARCHAR(10) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    expires_at DATETIME NOT NULL,
    used TINYINT(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE community (
    id int(11) PRIMARY KEY AUTO_INCREMENT,
    user_id int(11) NOT NULL,
    title varchar(255) DEFAULT NULL,
    content text NOT NULL,
    badges longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(badges)),
    uploaded_files longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(uploaded_files)),
    original_file_names longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(original_file_names)),
    created_at timestamp NOT NULL DEFAULT current_timestamp(),
    updated_at timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    CONSTRAINT ibfk_user_id FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE exams (
    id bigint(20) PRIMARY KEY AUTO_INCREMENT,
    teacher_id int(11) NOT NULL,
    title varchar(255) NOT NULL,
    created_at timestamp NOT NULL DEFAULT current_timestamp(),
    CONSTRAINT exams_ibfk_1 FOREIGN KEY (teacher_id) REFERENCES teachers (id) ON DELETE CASCADE ON UPDATE CASCADE;
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE saved_posts (
    id int(11) PRIMARY KEY AUTO_INCREMENT,
    post_id int(11) NOT NULL,
    user_id int(11) NOT NULL,
    CONSTRAINT ib_post_id FOREIGN KEY (post_id) REFERENCES community (id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT ib_user_id FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE notifications (
    id int(11) PRIMARY KEY AUTO_INCREMENT,
    title varchar(255) NOT NULL,
    content text NOT NULL,
    teacher_id int(11) NOT NULL,
    created_at timestamp NOT NULL DEFAULT current_timestamp(),
    CONSTRAINT notification_ibfk1 FOREIGN KEY (teacher_id) REFERENCES teachers (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE questions (
    question_id int(11) NOT NULL,
    test_id int(11) NOT NULL,
    question_title text NOT NULL,
    question_type enum('text','choice') NOT NULL,
    correct_answer varchar(255) DEFAULT NULL,
    CONSTRAINT question_ibfk_1 FOREIGN KEY (test_id) REFERENCES tests (test_id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE question_choices (
    id int(11) PRIMARY KEY AUTO_INCREMENT,
    question_id int(11) NOT NULL,
    choice varchar(255) NOT NULL,
    CONSTRAINT question_id_ibfk1 FOREIGN KEY (question_id) REFERENCES questions (question_id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE question_scores (
    id int(11) PRIMARY KEY AUTO_INCREMENT,
    question_id int(11) NOT NULL,
    score int(11) NOT NULL,
    CONSTRAINT question_scores_ibfk_1 FOREIGN KEY (question_id) REFERENCES questions (question_id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE requests (
    id int(11) PRIMARY KEY AUTO_INCREMENT,
    student_id int(11) NOT NULL,
    teacher_id int(11) NOT NULL,
    message text DEFAULT NULL,
    status enum('New','Pending','Accepted','Rejected') NOT NULL DEFAULT 'New',
    created_at timestamp NOT NULL DEFAULT current_timestamp(),
    updated_at timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    CONSTRAINT request_student_id_ibfk2 FOREIGN KEY (student_id) REFERENCES students (id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT request_teacher_id_ibfk1 FOREIGN KEY (teacher_id) REFERENCES teachers (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE sessions (
    id int(11) PRIMARY KEY AUTO_INCREMENT,
    data text NOT NULL,
    last_accessed timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE students (
    id int(11) PRIMARY KEY AUTO_INCREMENT,
    guardian_phone varchar(20) DEFAULT NULL,
    level enum('first','second','third') NOT NULL,
    CONSTRAINT students_ibfk_1 FOREIGN KEY (id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE student_notifications (
    id int(11) PRIMARY KEY AUTO_INCREMENT,
    student_id int(11) NOT NULL,
    notification_id int(11) NOT NULL,
    read_status tinyint(1) NOT NULL DEFAULT 0,
    CONSTRAINT student_notifications_ibfk_1 FOREIGN KEY (notification_id) REFERENCES notifications (id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT student_notifications_ibfk_2 FOREIGN KEY (student_id) REFERENCES students (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE student_score (
    id bigint(20) PRIMARY KEY AUTO_INCREMENT,
    teacher_id int(11) NOT NULL,
    exam_id bigint(20) NOT NULL,
    student_email varchar(255) NOT NULL,
    test_date timestamp NOT NULL DEFAULT current_timestamp(),
    full_test_degree decimal(5,2) NOT NULL,
    score decimal(5,2) NOT NULL,
    CONSTRAINT exam_id_score_ibfk FOREIGN KEY (exam_id) REFERENCES exams (id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT teacher_id_score_ibfk FOREIGN KEY (teacher_id) REFERENCES teachers (id) ON DELETE CASCADE ON UPDATE CASCADE;
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE subject_files (
    id int(11) PRIMARY KEY AUTO_INCREMENT,
    teacher_id int(11) NOT NULL,
    unique_file longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
    file_name longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
    created_at timestamp NOT NULL DEFAULT current_timestamp(),
    updated_at timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    CONSTRAINT ibfk_teacher_id_for_file FOREIGN KEY (teacher_id) REFERENCES teachers (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE teachers (
    id int(11) PRIMARY KEY AUTO_INCREMENT,
    title enum('teacher','assistant') NOT NULL,
    subject enum('Arabic','English','French','Spanish','German','Italian','Physics','Chemistry','Biology','Geology','Mathematics','Philosophy','History','Geography') NOT NULL,
    CONSTRAINT teachers_ibfk_1 FOREIGN KEY (id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE teacher_notifications (
    id int(11) PRIMARY KEY AUTO_INCREMENT,
    teacher_id int(11) NOT NULL,
    notification_id int(11) NOT NULL,
    read_status tinyint(1) NOT NULL DEFAULT 0,
    CONSTRAINT teacher_notifications_ibfk_1 FOREIGN KEY (notification_id) REFERENCES notifications (id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT teacher_notifications_ibfk_2 FOREIGN KEY (teacher_id) REFERENCES teachers (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE tests (
    test_id int(11) NOT NULL,
    teacher_id int(11) NOT NULL,
    title varchar(255) NOT NULL,
    description text DEFAULT NULL,
    level enum('first','second','third') NOT NULL,
    created_at timestamp NOT NULL DEFAULT current_timestamp(),
    status enum('published','suspended','expired') NOT NULL,
    CONSTRAINT teacher_id_ibfk1 FOREIGN KEY (teacher_id) REFERENCES teachers (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

CREATE TABLE videos (
    id int(11) PRIMARY KEY AUTO_INCREMENT,
    teacher_id int(11) NOT NULL,
    name varchar(255) NOT NULL,
    fileName varchar(255) NOT NULL,
    thumbnail varchar(50) NOT NULL,
    level enum('first','second','third') NOT NULL,
    created_at timestamp NOT NULL DEFAULT current_timestamp(),
    updated_at timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    CONSTRAINT fk_teacher_id FOREIGN KEY (teacher_id) REFERENCES teachers (id) ON DELETE CASCADE ON UPDATE CASCADE;
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;