-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 09, 2025 at 09:42 AM
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
-- Database: `math_quiz_game`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `full_name`, `email`, `created_at`) VALUES
(1, 'admin', '$2y$10$.e2WCyDt4zV30VRvnBF0b.bDfdojyO4Hg1JPDBJQZT3bqc2Lml6nu', 'Teacher Lilibeth Bordan', 'teacher@mathquiz.com', '2025-12-04 08:46:23');

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `question_text` text NOT NULL,
  `choice_a` varchar(255) NOT NULL,
  `choice_b` varchar(255) NOT NULL,
  `choice_c` varchar(255) NOT NULL,
  `choice_d` varchar(255) NOT NULL,
  `correct_answer` enum('A','B','C','D') NOT NULL,
  `question_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `choice_a`, `choice_b`, `choice_c`, `choice_d`, `correct_answer`, `question_order`, `created_at`) VALUES
(1, 1, 'If x = 3\r\nWhat is 5x + 2x - 15', '15', '19', '20', '21', 'D', 0, '2025-12-09 01:54:30'),
(2, 1, 'If y = 5\r\nWhat is 3y - 5', '10', '15', '20', '5', 'A', 0, '2025-12-09 01:55:17'),
(3, 1, 'If 3x + 5 = 20\r\nWhat is the value of x', '1', '5', '3', '10', 'B', 0, '2025-12-09 01:56:33'),
(4, 1, 'If 10y = 30\r\nWhat is the value of y', '1', '2', '3', '4', 'C', 0, '2025-12-09 01:57:33'),
(5, 1, 'Solve: 4x + 3x = ?', '7', '19', '38', '42', 'A', 0, '2025-12-09 01:58:17'),
(6, 2, 'Solve: 2x + 3y - 4x +5y = ?', '1', '2', '3', '4', 'D', 0, '2025-12-09 02:12:07'),
(7, 2, 'Solve: What is 5% of 100?', '15', '20', '30', '40', 'A', 0, '2025-12-09 02:12:59'),
(8, 2, 'What is the center of Earth?', 'Space', 'Void', 'Core', 'None of the above', 'C', 0, '2025-12-09 02:13:58'),
(9, 3, 'What is the product of  3 and 6', '17', '16', '15', '18', 'D', 0, '2025-12-09 02:16:56'),
(10, 3, 'What is the quotient of 10 and 5', '2', '3', '4', '5', 'A', 0, '2025-12-09 02:17:53'),
(11, 3, 'What is the 50% of 1 million', '389222', '83359', '500000', '2424624', 'C', 0, '2025-12-09 02:19:10');

-- --------------------------------------------------------

--
-- Table structure for table `quizzes`
--

CREATE TABLE `quizzes` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `date` date NOT NULL,
  `topic` varchar(100) DEFAULT NULL,
  `instructions` text DEFAULT NULL,
  `share_code` varchar(50) NOT NULL,
  `created_by` int(11) NOT NULL,
  `status` enum('active','inactive','archived') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quizzes`
--

INSERT INTO `quizzes` (`id`, `title`, `date`, `topic`, `instructions`, `share_code`, `created_by`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Quiz 1', '2025-12-09', 'Polynomials', 'Answer this quiz as your attendance for today.', 'F0C6C3DE', 1, 'active', '2025-12-09 01:47:56', '2025-12-09 01:47:56'),
(2, 'Quiz 2', '2025-12-10', 'Polynomials 2', 'Answer this activity for attendance tomorrow.', 'C236E5C6', 1, 'active', '2025-12-09 02:11:03', '2025-12-09 02:11:03'),
(3, 'Quiz 3', '2025-12-11', 'Math Adventure Quiz 3', 'Solve the problem.', '786C6D22', 1, 'active', '2025-12-09 02:16:01', '2025-12-09 02:16:01');

-- --------------------------------------------------------

--
-- Table structure for table `quiz_responses`
--

CREATE TABLE `quiz_responses` (
  `id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `score` int(11) NOT NULL,
  `total_questions` int(11) NOT NULL,
  `percentage` decimal(5,2) NOT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quiz_responses`
--

INSERT INTO `quiz_responses` (`id`, `quiz_id`, `student_id`, `score`, `total_questions`, `percentage`, `submitted_at`) VALUES
(1, 1, 1, 1, 5, 20.00, '2025-12-09 02:06:46'),
(2, 3, 2, 1, 3, 33.33, '2025-12-09 02:29:29');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `section` varchar(50) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `is_guest` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `name`, `section`, `password`, `is_guest`, `created_at`, `updated_at`) VALUES
(1, 'Juan Tamad', 'Diamond', NULL, 1, '2025-12-09 02:00:02', '2025-12-09 02:00:02'),
(2, 'Clyd Marcus', 'Marble', NULL, 1, '2025-12-09 02:29:16', '2025-12-09 02:29:16'),
(3, 'Mark Zucker', 'Diamond', '$2y$10$jr.OAK8OfYnEv8wgMAG1Vuxm.iB3Vukzo1hbWzeaV.rpzOG5xapk.', 0, '2025-12-09 03:18:29', '2025-12-09 03:18:29'),
(4, 'Juan Tamad', 'Diamond', '$2y$10$//V5iF8NakWXNI6J.So93.un67UIK71Y.5Q8kiwuExjU0Adon9ZUi', 0, '2025-12-09 04:19:51', '2025-12-09 04:19:51'),
(5, 'Clyd Marcus', 'Gold', '$2y$10$ftm4zy82tOy4OpZGWcnBruTaZ8yqOL2YWc7myvS4Oy16dwGfwSiSa', 0, '2025-12-09 04:26:34', '2025-12-09 04:26:34'),
(6, 'Frederick Navarro', 'Diamond', '$2y$10$9IvdV2RpzwiGVsA3NjvmROb6idx8bhJ0vG5abqyDZrnXqcrqmV.tu', 0, '2025-12-09 05:14:23', '2025-12-09 05:14:23');

-- --------------------------------------------------------

--
-- Table structure for table `student_answers`
--

CREATE TABLE `student_answers` (
  `id` int(11) NOT NULL,
  `response_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `student_answer` enum('A','B','C','D') NOT NULL,
  `is_correct` tinyint(1) NOT NULL,
  `answered_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_answers`
--

INSERT INTO `student_answers` (`id`, `response_id`, `question_id`, `student_answer`, `is_correct`, `answered_at`) VALUES
(1, 1, 1, 'D', 1, '2025-12-09 02:06:46'),
(2, 2, 9, 'D', 1, '2025-12-09 02:29:29');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quiz_id` (`quiz_id`);

--
-- Indexes for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `share_code` (`share_code`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_quiz_share_code` (`share_code`),
  ADD KEY `idx_quiz_status` (`status`);

--
-- Indexes for table `quiz_responses`
--
ALTER TABLE `quiz_responses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_quiz_student` (`quiz_id`,`student_id`),
  ADD KEY `idx_response_quiz` (`quiz_id`),
  ADD KEY `idx_response_student` (`student_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_student_name` (`name`);

--
-- Indexes for table `student_answers`
--
ALTER TABLE `student_answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `response_id` (`response_id`),
  ADD KEY `question_id` (`question_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `quiz_responses`
--
ALTER TABLE `quiz_responses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `student_answers`
--
ALTER TABLE `student_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD CONSTRAINT `quizzes_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `admins` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quiz_responses`
--
ALTER TABLE `quiz_responses`
  ADD CONSTRAINT `quiz_responses_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quiz_responses_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_answers`
--
ALTER TABLE `student_answers`
  ADD CONSTRAINT `student_answers_ibfk_1` FOREIGN KEY (`response_id`) REFERENCES `quiz_responses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_answers_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
