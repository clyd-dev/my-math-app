<?php
// models/Student.php - UPDATED VERSION

class Student {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Register new student (removed grade parameter)
     */
    public function register($name, $section, $password) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("INSERT INTO students (name, section, password) VALUES (?, ?, ?)");
        return $stmt->execute([$name, $section, $hashedPassword]);
    }
    
    /**
     * Student login
     */
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
    
    /**
     * Create guest student (removed grade parameter)
     */
    public function createGuest($name, $section) {
        $stmt = $this->db->prepare("INSERT INTO students (name, section, is_guest) VALUES (?, ?, 1)");
        $stmt->execute([$name, $section]);
        return $this->db->lastInsertId();
    }
    
    /**
     * Get student by ID
     */
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM students WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Get student by name
     */
    public function getByName($name) {
        $stmt = $this->db->prepare("SELECT * FROM students WHERE name = ? LIMIT 1");
        $stmt->execute([$name]);
        return $stmt->fetch();
    }
    
    /**
     * Check if student exists by name
     */
    public function existsByName($name) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM students WHERE name = ? AND is_guest = 0");
        $stmt->execute([$name]);
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }
    
    /**
     * Get all students
     */
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM students ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }
    
    /**
     * Update student (removed grade parameter)
     */
    public function update($id, $name, $section) {
        $stmt = $this->db->prepare("UPDATE students SET name=?, section=? WHERE id=?");
        return $stmt->execute([$name, $section, $id]);
    }
    
    /**
     * Delete student
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM students WHERE id=?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Get student quiz history
     */
    public function getQuizHistory($studentId) {
        $stmt = $this->db->prepare("SELECT qr.*, q.title, q.date 
                                     FROM quiz_responses qr 
                                     JOIN quizzes q ON qr.quiz_id = q.id 
                                     WHERE qr.student_id = ? 
                                     ORDER BY qr.submitted_at DESC");
        $stmt->execute([$studentId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get students by section
     */
    public function getBySection($section) {
        $stmt = $this->db->prepare("SELECT * FROM students WHERE section = ? ORDER BY name");
        $stmt->execute([$section]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get registered students only
     */
    public function getRegistered() {
        $stmt = $this->db->query("SELECT * FROM students WHERE is_guest = 0 ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }
    
    /**
     * Get guest students only
     */
    public function getGuests() {
        $stmt = $this->db->query("SELECT * FROM students WHERE is_guest = 1 ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }
    
    /**
     * Count total students
     */
    public function count() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM students");
        $result = $stmt->fetch();
        return $result['total'];
    }
    
    /**
     * Count registered students
     */
    public function countRegistered() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM students WHERE is_guest = 0");
        $result = $stmt->fetch();
        return $result['total'];
    }
    
    /**
     * Count guest students
     */
    public function countGuests() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM students WHERE is_guest = 1");
        $result = $stmt->fetch();
        return $result['total'];
    }
    
    /**
     * Search students by name
     */
    public function search($searchTerm) {
        $searchTerm = "%{$searchTerm}%";
        $stmt = $this->db->prepare("SELECT * FROM students WHERE name LIKE ? ORDER BY name");
        $stmt->execute([$searchTerm]);
        return $stmt->fetchAll();
    }
    
    /**
     * Check if student exists
     */
    public function exists($id) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM students WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }
    
    /**
     * Get student with quiz statistics
     */
    public function getWithStats($id) {
        $student = $this->getById($id);
        if (!$student) {
            return null;
        }
        
        // Get quiz statistics
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total_quizzes,
                AVG(percentage) as avg_percentage,
                MAX(percentage) as highest_percentage,
                MIN(percentage) as lowest_percentage,
                SUM(score) as total_score,
                SUM(total_questions) as total_questions
            FROM quiz_responses 
            WHERE student_id = ?
        ");
        $stmt->execute([$id]);
        $stats = $stmt->fetch();
        
        $student['stats'] = $stats;
        return $student;
    }
    
    /**
     * Get recent activity for student
     */
    public function getRecentActivity($studentId, $limit = 5) {
        $stmt = $this->db->prepare("
            SELECT qr.*, q.title, q.date 
            FROM quiz_responses qr 
            JOIN quizzes q ON qr.quiz_id = q.id 
            WHERE qr.student_id = ? 
            ORDER BY qr.submitted_at DESC 
            LIMIT ?
        ");
        $stmt->execute([$studentId, $limit]);
        return $stmt->fetchAll();
    }
    
    /**
     * Update student password
     */
    public function updatePassword($id, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("UPDATE students SET password = ? WHERE id = ?");
        return $stmt->execute([$hashedPassword, $id]);
    }
    
    /**
     * Verify student password
     */
    public function verifyPassword($id, $password) {
        $stmt = $this->db->prepare("SELECT password FROM students WHERE id = ?");
        $stmt->execute([$id]);
        $student = $stmt->fetch();
        
        if ($student) {
            return password_verify($password, $student['password']);
        }
        return false;
    }
}
?>