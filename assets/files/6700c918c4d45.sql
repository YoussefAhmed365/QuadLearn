-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 03, 2024 at 08:48 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `datainfo`
--

-- --------------------------------------------------------

--
-- Table structure for table `answers`
--

CREATE TABLE `answers` (
  `id` int(11) NOT NULL,
  `question_id` int(11) DEFAULT NULL,
  `answer_text` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `assigned_assistants`
--

CREATE TABLE `assigned_assistants` (
  `id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `assistant_id` int(11) NOT NULL,
  `assign_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assigned_assistants`
--

INSERT INTO `assigned_assistants` (`id`, `teacher_id`, `assistant_id`, `assign_date`) VALUES
(1, 54819994, 23783174, '2024-09-17 19:31:56');

-- --------------------------------------------------------

--
-- Table structure for table `assigned_students`
--

CREATE TABLE `assigned_students` (
  `id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `assign_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assigned_students`
--

INSERT INTO `assigned_students` (`id`, `teacher_id`, `student_id`, `assign_date`) VALUES
(1, 54819994, 77827695, '2024-09-17 20:23:01'),
(2, 72926709, 60208519, '2024-09-18 06:22:01'),
(3, 54819994, 40823548, '2024-09-26 12:27:29'),
(5, 28174066, 77827695, '2024-09-27 20:53:39'),
(8, 72926709, 77827695, '2024-09-29 12:16:09');

-- --------------------------------------------------------

--
-- Table structure for table `assigned_teachers`
--

CREATE TABLE `assigned_teachers` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `assign_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assigned_teachers`
--

INSERT INTO `assigned_teachers` (`id`, `student_id`, `teacher_id`, `assign_date`) VALUES
(1, 77827695, 54819994, '2024-09-17 20:23:00'),
(2, 60208519, 72926709, '2024-09-18 06:22:01'),
(5, 77827695, 28174066, '2024-09-27 20:53:39'),
(6, 40823548, 54819994, '2024-09-28 18:35:58'),
(8, 77827695, 72926709, '2024-09-29 12:16:09');

-- --------------------------------------------------------

--
-- Table structure for table `auth_tokens`
--

CREATE TABLE `auth_tokens` (
  `id` int(11) NOT NULL,
  `selector` varchar(255) NOT NULL,
  `hashed_validator` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `expires` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `community`
--

CREATE TABLE `community` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content` text NOT NULL,
  `badges` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`badges`)),
  `uploaded_files` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`uploaded_files`)),
  `original_file_names` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`original_file_names`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `community`
--

INSERT INTO `community` (`id`, `user_id`, `title`, `content`, `badges`, `uploaded_files`, `original_file_names`, `created_at`, `updated_at`) VALUES
(1, 54819994, 'مرحباً جميعا! كيف حالكم؟', 'السلام عليكم ورحمة الله وبركاته.\r\nهلا والله بالرجال، هالحين صار وقتكم', '[{\"name\":{\"name\":\"\\u064a\\u0624\\u0621\\u0626\",\"color\":\"green\"},\"color\":null},{\"name\":{\"name\":\"\\u0633\\u064a\\u0621\",\"color\":\"red\"},\"color\":null},{\"name\":{\"name\":\"\\u062b\\u0628\\u064a\\u0633\\u0642\\u0644\\u0627\",\"color\":\"yellow\"},\"color\":null}]', '[\"66fd4e68dfaa9.webp\",\"66fd4e68dfc6d.webp\",\"66fd4e68dfe77.webp\"]', '[\"Arabic.webp\",\"Biology.webp\",\"Chemistry.webp\"]', '2024-10-02 13:45:12', '2024-10-02 14:26:54'),
(2, 54819994, 'حاجة جديدة', 'فاخر من الآخر\r\nولا أييه', '[{\"name\":{\"name\":\"\\u064a\\u0644\\u0627 \\u0628\\u064a\\u0646\\u0627\",\"color\":\"yellow\"},\"color\":null}]', '[\"66fd8808bf1bf.webp\"]', '[\"French.webp\"]', '2024-10-02 17:51:04', '2024-10-02 17:51:04');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `title`, `content`, `teacher_id`, `created_at`) VALUES
(21, 'xzjhhj jk', 'sayhkjcsa', 72926709, '2024-09-29 07:00:37'),
(35, 'مرحبتين', 'مرحباً', 54819994, '2024-09-30 05:35:46'),
(37, 'جديد', 'خالص', 23783174, '2024-09-30 06:17:14'),
(38, 'igh', 'd', 54819994, '2024-09-30 06:17:36');

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `question_id` int(11) NOT NULL,
  `test_id` int(11) NOT NULL,
  `question_title` text NOT NULL,
  `question_type` enum('text','choice') NOT NULL,
  `correct_answer` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`question_id`, `test_id`, `question_title`, `question_type`, `correct_answer`) VALUES
(3, 2, 'نات', 'text', 'من'),
(4, 2, 'تالتن', 'choice', '');

-- --------------------------------------------------------

--
-- Table structure for table `question_choices`
--

CREATE TABLE `question_choices` (
  `id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `choice` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `question_scores`
--

CREATE TABLE `question_scores` (
  `id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `score` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `question_scores`
--

INSERT INTO `question_scores` (`id`, `question_id`, `score`) VALUES
(3, 3, 1),
(4, 4, 4);

-- --------------------------------------------------------

--
-- Table structure for table `requests`
--

CREATE TABLE `requests` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `message` text DEFAULT NULL,
  `status` enum('New','Pending','Accepted','Rejected') NOT NULL DEFAULT 'New',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `requests`
--

INSERT INTO `requests` (`id`, `student_id`, `teacher_id`, `message`, `status`, `created_at`, `updated_at`) VALUES
(1, 77827695, 54819994, NULL, 'Accepted', '2024-09-17 19:32:24', '2024-09-29 12:16:09'),
(3, 60208519, 72926709, NULL, 'Accepted', '2024-09-18 06:18:54', '2024-09-18 06:22:01'),
(6, 77827695, 28174066, NULL, 'Accepted', '2024-09-27 20:49:49', '2024-09-29 12:16:09'),
(9, 77827695, 72926709, NULL, 'Accepted', '2024-09-29 12:15:49', '2024-09-29 12:16:09');

-- --------------------------------------------------------

--
-- Table structure for table `saved_posts`
--

CREATE TABLE `saved_posts` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` int(11) NOT NULL,
  `data` text NOT NULL,
  `last_accessed` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `guardian_phone` varchar(20) DEFAULT NULL,
  `level` enum('first','second','third') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `guardian_phone`, `level`) VALUES
(40823548, '01014904561', 'second'),
(60208519, '01001601337', 'second'),
(77827695, NULL, 'first'),
(78458849, '01124583698', 'third');

-- --------------------------------------------------------

--
-- Table structure for table `student_notifications`
--

CREATE TABLE `student_notifications` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `notification_id` int(11) NOT NULL,
  `read_status` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_notifications`
--

INSERT INTO `student_notifications` (`id`, `student_id`, `notification_id`, `read_status`) VALUES
(15, 60208519, 21, 0),
(16, 77827695, 21, 1),
(35, 77827695, 35, 1),
(36, 40823548, 35, 0),
(39, 77827695, 37, 1),
(40, 40823548, 37, 0),
(41, 77827695, 38, 1),
(42, 40823548, 38, 0);

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `id` int(11) NOT NULL,
  `title` enum('teacher','assistant') NOT NULL,
  `subject` enum('Arabic','English','French','Spanish','German','Italian','Physics','Chemistry','Biology','Geology','Mathematics','Philosophy','History','Geography') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`id`, `title`, `subject`) VALUES
(23783174, 'assistant', 'Spanish'),
(25732859, 'teacher', 'Physics'),
(28174066, 'teacher', 'Mathematics'),
(52224227, 'teacher', 'Physics'),
(54819994, 'teacher', 'Mathematics'),
(62542314, 'assistant', 'Mathematics'),
(72926709, 'teacher', 'Spanish');

-- --------------------------------------------------------

--
-- Table structure for table `teacher_notifications`
--

CREATE TABLE `teacher_notifications` (
  `id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `notification_id` int(11) NOT NULL,
  `read_status` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teacher_notifications`
--

INSERT INTO `teacher_notifications` (`id`, `teacher_id`, `notification_id`, `read_status`) VALUES
(20, 72926709, 21, 1),
(41, 54819994, 35, 1),
(42, 23783174, 35, 1),
(45, 54819994, 37, 1),
(46, 23783174, 37, 0),
(47, 54819994, 38, 1),
(48, 23783174, 38, 0);

-- --------------------------------------------------------

--
-- Table structure for table `tests`
--

CREATE TABLE `tests` (
  `test_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `level` enum('first','second','third') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('published','suspended','expired') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tests`
--

INSERT INTO `tests` (`test_id`, `teacher_id`, `title`, `description`, `level`, `created_at`, `status`) VALUES
(2, 54819994, 'معب', 'نات', 'first', '2024-10-01 05:37:26', 'published');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `account_type` enum('teacher','student') NOT NULL,
  `gender` enum('male','female') NOT NULL,
  `picture` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `first_name`, `last_name`, `phone_number`, `account_type`, `gender`, `picture`, `bio`, `created_at`, `updated_at`) VALUES
(23783174, 'ahmed.ashraf', '$2y$10$UzYoyTB.ERnnWqUmDNkwEucZ9v3D30bTkcFZEEN5Q8TnbgN3lGcRa', 'ahmedashraf@gmaill.com', 'أحمد', 'أشرف', '01157374593', 'teacher', 'male', NULL, NULL, '2024-09-17 18:55:25', '2024-09-22 15:32:49'),
(25732859, 'fatimaEssam12', '$2y$10$JkSp.g7APBLffa10jWzVyOA9VYcSnmNBsv38p3fio16W51VuU/gPW', 'fatima@gmail.com', 'فاطمة', 'عصام', '01230504789', 'teacher', 'female', NULL, NULL, '2024-09-26 06:05:42', '2024-09-26 06:05:42'),
(28174066, 'AlaaAshraf', '$2y$10$cEoVsl8gZop8iJVQvBh91uO1Gfn8LFEwzCkraFOxuryG2K2ViMRR2', 'ALAA-ASHRAF@Gmail.com', 'آلاء', 'أشرف', '01154325299', 'teacher', 'female', NULL, NULL, '2024-09-27 20:19:09', '2024-09-27 20:19:09'),
(40823548, 'Noor1234', '$2y$10$jQdbC3uGmKb5EaNj.A1NEuNJwSfxuwh0m6uqH93fMqA7l3fxvlICu', 'noor@gmail.com', 'نور', 'طارق', '01022154561', 'student', 'female', NULL, NULL, '2024-09-26 06:34:15', '2024-09-26 07:52:48'),
(52224227, 'mohamed12', '$2y$10$d2hASpjOXxp5fu0co1.Jq.kONxYzOs8UKo.7FAhbW9NULvjFiTcUe', 'mohamed.ebrahim@gmail.com', 'محمد', 'إبراهيم', '01012354689', 'teacher', 'male', NULL, NULL, '2024-09-26 05:41:35', '2024-09-26 05:47:32'),
(54819994, 'youssef12', '$2y$10$z8B49o4LMwXqcc5Cxo/IXemm2VYIzv3gHt18TQ.IS6F200/.xG7GG', 'youssef.ahmed5002@gmail.com', 'يوسف', 'أحمد', '01014904561', 'teacher', 'male', '54819994.webp', NULL, '2024-09-17 18:33:28', '2024-09-22 15:32:55'),
(60208519, 'mahmod12', '$2y$10$4enkZ4eDVE8GwX4SKiDND.8.Y08y23hnVqQKj7/6EnMI0wjsgIeB6', 'mahmod.ahmed5001@gmail.com', 'محمود', 'أحمد', '01012204531', 'student', 'male', NULL, NULL, '2024-09-17 18:38:57', '2024-09-22 15:19:15'),
(62542314, 'malak123', '$2y$10$Ik0yB36PIZdV5WZdH1e9f.tK.5xb4uVHC6BWesI/dhT4vZoLKj7ja', 'malak@gmail.com', 'ملك', 'محمد', '01014904562', 'teacher', 'female', NULL, NULL, '2024-09-25 06:23:28', '2024-09-25 06:23:28'),
(72926709, 'ahmed', '$2y$10$ugOMuje44qTFl8OtpBl37eQjC21eL28BVkrsGnU9E4EUieogPMUdi', 'ahmedibrahim@gmail.com', 'احمد', 'ابراهيم', '01120881636', 'teacher', 'male', NULL, NULL, '2024-09-17 15:04:29', '2024-09-22 15:33:00'),
(77827695, 'menna12', '$2y$10$V7jXoNs/JgW8vZqm0PMhkuKy0HvGtS4bWuQfggpOQ/GHNVoja4yIG', 'mennahassan12345@gmail.com', 'منة', 'حسن', '01514326787', 'student', 'female', NULL, NULL, '2024-09-17 19:07:25', '2024-09-22 15:19:21'),
(78458849, 'youssefhassan12', '$2y$10$ni8oZ2Huee1O9U69NObMNeEqa2rnK6A1iVsISisXLSeq7ccWUVduq', 'youssefhassan@gmail.com', 'يوسف', 'حسن يوسف عمار', '01014205785', 'student', 'male', NULL, NULL, '2024-09-25 09:35:22', '2024-09-26 07:53:20');

-- --------------------------------------------------------

--
-- Table structure for table `videos`
--

CREATE TABLE `videos` (
  `id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `fileName` varchar(255) NOT NULL,
  `level` enum('first','second','third') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `videos`
--

INSERT INTO `videos` (`id`, `teacher_id`, `name`, `fileName`, `level`, `created_at`, `updated_at`) VALUES
(4, 54819994, 'kjchjds', '66f8fb6cc90d0.mp4', 'second', '2024-09-29 07:02:04', '2024-09-29 07:02:04'),
(5, 54819994, 'kjchjdskj  jk انتست س89', '66f8fb9d9226b.mp4', 'first', '2024-09-29 07:02:53', '2024-09-29 07:02:53'),
(6, 54819994, 'yuttr هختبي - نتيمي 23212', '66f8fc916162a.mp4', 'first', '2024-09-29 07:06:57', '2024-09-29 07:06:57'),
(7, 54819994, 'خنتالبلً', '66f8fd94797b5.mp4', 'first', '2024-09-29 07:11:16', '2024-09-29 07:11:16'),
(8, 54819994, 'نتمتمىمً', '66f8fe83e1f34.mp4', 'first', '2024-09-29 07:15:15', '2024-09-29 07:15:15'),
(9, 54819994, 'لتبنتاا', '66f8feff740b9.mp4', 'first', '2024-09-29 07:17:19', '2024-09-29 07:17:19'),
(10, 54819994, 'تالهعنلعت', '66f8ffad94731.mp4', 'third', '2024-09-29 07:20:13', '2024-09-29 07:20:13'),
(11, 72926709, 'أحمد إبراهيم', '66f947e533c67.mp4', 'first', '2024-09-29 12:28:21', '2024-09-29 12:28:21');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `answers`
--
ALTER TABLE `answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `answers_ibfk_1` (`question_id`);

--
-- Indexes for table `assigned_assistants`
--
ALTER TABLE `assigned_assistants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `assistant_teacher_id_ibfk1` (`teacher_id`),
  ADD KEY `assistant_id_ibfk1` (`assistant_id`);

--
-- Indexes for table `assigned_students`
--
ALTER TABLE `assigned_students`
  ADD PRIMARY KEY (`id`),
  ADD KEY `main_teacher_id_ibfk1` (`teacher_id`),
  ADD KEY `assigned_student_id_ibfk2` (`student_id`);

--
-- Indexes for table `assigned_teachers`
--
ALTER TABLE `assigned_teachers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id_ibfk1` (`student_id`),
  ADD KEY `teacher_id_ibfk2` (`teacher_id`);

--
-- Indexes for table `auth_tokens`
--
ALTER TABLE `auth_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `selector` (`selector`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `community`
--
ALTER TABLE `community`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ibfk_user_id` (`user_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notification_ibfk1` (`teacher_id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`question_id`),
  ADD KEY `question_ibfk_1` (`test_id`);

--
-- Indexes for table `question_choices`
--
ALTER TABLE `question_choices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `question_id_ibfk1` (`question_id`);

--
-- Indexes for table `question_scores`
--
ALTER TABLE `question_scores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `requests`
--
ALTER TABLE `requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `request_teacher_id_ibfk1` (`teacher_id`),
  ADD KEY `request_student_id_ibfk2` (`student_id`);

--
-- Indexes for table `saved_posts`
--
ALTER TABLE `saved_posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ib_post_id` (`post_id`),
  ADD KEY `ib_user_id` (`user_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `student_notifications`
--
ALTER TABLE `student_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notification_id` (`notification_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `teacher_notifications`
--
ALTER TABLE `teacher_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notification_id` (`notification_id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- Indexes for table `tests`
--
ALTER TABLE `tests`
  ADD PRIMARY KEY (`test_id`),
  ADD KEY `teacher_id_ibfk1` (`teacher_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_users_username` (`username`),
  ADD KEY `idx_users_email` (`email`);

--
-- Indexes for table `videos`
--
ALTER TABLE `videos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_teacher_id` (`teacher_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `answers`
--
ALTER TABLE `answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `assigned_assistants`
--
ALTER TABLE `assigned_assistants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `assigned_students`
--
ALTER TABLE `assigned_students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `assigned_teachers`
--
ALTER TABLE `assigned_teachers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `auth_tokens`
--
ALTER TABLE `auth_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `community`
--
ALTER TABLE `community`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `question_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `question_choices`
--
ALTER TABLE `question_choices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `question_scores`
--
ALTER TABLE `question_scores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `requests`
--
ALTER TABLE `requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `saved_posts`
--
ALTER TABLE `saved_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sessions`
--
ALTER TABLE `sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `student_notifications`
--
ALTER TABLE `student_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `teacher_notifications`
--
ALTER TABLE `teacher_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `tests`
--
ALTER TABLE `tests`
  MODIFY `test_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99625072;

--
-- AUTO_INCREMENT for table `videos`
--
ALTER TABLE `videos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `answers`
--
ALTER TABLE `answers`
  ADD CONSTRAINT `answers_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `questions` (`question_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `assigned_assistants`
--
ALTER TABLE `assigned_assistants`
  ADD CONSTRAINT `assistant_id_ibfk1` FOREIGN KEY (`assistant_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `assistant_teacher_id_ibfk1` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `assigned_students`
--
ALTER TABLE `assigned_students`
  ADD CONSTRAINT `assigned_student_id_ibfk2` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `main_teacher_id_ibfk1` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `assigned_teachers`
--
ALTER TABLE `assigned_teachers`
  ADD CONSTRAINT `student_id_ibfk1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `teacher_id_ibfk2` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `auth_tokens`
--
ALTER TABLE `auth_tokens`
  ADD CONSTRAINT `auth_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `community`
--
ALTER TABLE `community`
  ADD CONSTRAINT `ibfk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notification_ibfk1` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `question_ibfk_1` FOREIGN KEY (`test_id`) REFERENCES `tests` (`test_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `question_choices`
--
ALTER TABLE `question_choices`
  ADD CONSTRAINT `question_id_ibfk1` FOREIGN KEY (`question_id`) REFERENCES `questions` (`question_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `question_scores`
--
ALTER TABLE `question_scores`
  ADD CONSTRAINT `question_scores_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `questions` (`question_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `requests`
--
ALTER TABLE `requests`
  ADD CONSTRAINT `request_student_id_ibfk2` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `request_teacher_id_ibfk1` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `saved_posts`
--
ALTER TABLE `saved_posts`
  ADD CONSTRAINT `ib_post_id` FOREIGN KEY (`post_id`) REFERENCES `community` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ib_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_notifications`
--
ALTER TABLE `student_notifications`
  ADD CONSTRAINT `student_notifications_ibfk_1` FOREIGN KEY (`notification_id`) REFERENCES `notifications` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `student_notifications_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `teachers`
--
ALTER TABLE `teachers`
  ADD CONSTRAINT `teachers_ibfk_1` FOREIGN KEY (`id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `teacher_notifications`
--
ALTER TABLE `teacher_notifications`
  ADD CONSTRAINT `teacher_notifications_ibfk_1` FOREIGN KEY (`notification_id`) REFERENCES `notifications` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `teacher_notifications_ibfk_2` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tests`
--
ALTER TABLE `tests`
  ADD CONSTRAINT `teacher_id_ibfk1` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `videos`
--
ALTER TABLE `videos`
  ADD CONSTRAINT `fk_teacher_id` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
