<?php
// models/Admin.php
class Admin {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function login($username, $password) {
        $stmt = $this->db->prepare("SELECT * FROM admins WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();
        
        if($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['full_name'];
            return true;
        }
        return false;
    }
    
    public function logout() {
        session_destroy();
        return true;
    }
    
    public function getDashboardStats() {
        $stats = [];
        
        // Total students
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM students");
        $stats['total_students'] = $stmt->fetch()['total'];
        
        // Total quizzes
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM quizzes WHERE status='active'");
        $stats['total_quizzes'] = $stmt->fetch()['total'];
        
        // Quizzes answered today
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM quiz_responses WHERE DATE(submitted_at) = CURDATE()");
        $stats['today_responses'] = $stmt->fetch()['total'];
        
        // Latest responses
        $stmt = $this->db->query("SELECT qr.*, s.name, q.title 
                                  FROM quiz_responses qr 
                                  JOIN students s ON qr.student_id = s.id 
                                  JOIN quizzes q ON qr.quiz_id = q.id 
                                  ORDER BY qr.submitted_at DESC LIMIT 5");
        $stats['latest_responses'] = $stmt->fetchAll();
        
        return $stats;
    }
}

?>