<?php

// models/Student.php
class Student {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function register($name, $section, $password) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("INSERT INTO students (name, section, password) VALUES (?, ?, ?)");
        return $stmt->execute([$name, $section, $hashedPassword]);
    }
    
    public function login($name, $password) {
        $stmt = $this->db->prepare("SELECT * FROM students WHERE name = ? AND is_guest = 0");
        $stmt->execute([$name]);
        $student = $stmt->fetch();
        
        if($student && password_verify($password, $student['password'])) {
            $_SESSION['student_id'] = $student['id'];
            $_SESSION['student_name'] = $student['name'];
            return true;
        }
        return false;
    }
    
    public function createGuest($name, $section) {
        $stmt = $this->db->prepare("INSERT INTO students (name, section, is_guest) VALUES (?, ?, 1)");
        $stmt->execute([$name, $section]);
        return $this->db->lastInsertId();
    }
    
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM students WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM students ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }
    
    public function update($id, $name, $section) {
        $stmt = $this->db->prepare("UPDATE students SET name=?, section=? WHERE id=?");
        return $stmt->execute([$name, $section, $id]);
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM students WHERE id=?");
        return $stmt->execute([$id]);
    }
    
    public function getQuizHistory($studentId) {
        $stmt = $this->db->prepare("SELECT qr.*, q.title, q.date 
                                     FROM quiz_responses qr 
                                     JOIN quizzes q ON qr.quiz_id = q.id 
                                     WHERE qr.student_id = ? 
                                     ORDER BY qr.submitted_at DESC");
        $stmt->execute([$studentId]);
        return $stmt->fetchAll();
    }
}


?>