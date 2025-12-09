-- Math Adventure Quiz Game Database Schema
-- Database: math_quiz_game

CREATE DATABASE IF NOT EXISTS math_quiz_game;
USE math_quiz_game;

-- Admin Table
CREATE TABLE admins (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Students Table
CREATE TABLE students (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    grade VARCHAR(20) NOT NULL,
    section VARCHAR(50) NOT NULL,
    password VARCHAR(255),
    is_guest BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Quizzes Table
CREATE TABLE quizzes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    date DATE NOT NULL,
    topic VARCHAR(100),
    instructions TEXT,
    share_code VARCHAR(50) UNIQUE NOT NULL,
    created_by INT NOT NULL,
    status ENUM('active', 'inactive', 'archived') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES admins(id) ON DELETE CASCADE
);

-- Questions Table
CREATE TABLE questions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    quiz_id INT NOT NULL,
    question_text TEXT NOT NULL,
    choice_a VARCHAR(255) NOT NULL,
    choice_b VARCHAR(255) NOT NULL,
    choice_c VARCHAR(255) NOT NULL,
    choice_d VARCHAR(255) NOT NULL,
    correct_answer ENUM('A', 'B', 'C', 'D') NOT NULL,
    question_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE
);

-- Quiz Responses Table
CREATE TABLE quiz_responses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    quiz_id INT NOT NULL,
    student_id INT NOT NULL,
    score INT NOT NULL,
    total_questions INT NOT NULL,
    percentage DECIMAL(5,2) NOT NULL,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    UNIQUE KEY unique_quiz_student (quiz_id, student_id)
);

-- Student Answers Table
CREATE TABLE student_answers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    response_id INT NOT NULL,
    question_id INT NOT NULL,
    student_answer ENUM('A', 'B', 'C', 'D') NOT NULL,
    is_correct BOOLEAN NOT NULL,
    answered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (response_id) REFERENCES quiz_responses(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
);

-- Insert default admin account
-- Username: admin
-- Password: admin123 (hashed)
INSERT INTO admins (username, password, full_name, email)
VALUES ('admin', '$2y$10$.e2WCyDt4zV30VRvnBF0b.bDfdojyO4Hg1JPDBJQZT3bqc2Lml6nu', 'Teacher Lilibeth Bordan', 'teacher@mathquiz.com');

-- Create indexes for better performance
CREATE INDEX idx_quiz_share_code ON quizzes(share_code);
CREATE INDEX idx_student_name ON students(name);
CREATE INDEX idx_quiz_status ON quizzes(status);
CREATE INDEX idx_response_quiz ON quiz_responses(quiz_id);
CREATE INDEX idx_response_student ON quiz_responses(student_id);