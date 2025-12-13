<?php
// controllers/StudentController.php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Student.php';

class StudentController {
    private $studentModel;
    
    public function __construct() {
        $this->studentModel = new Student();
    }
    
    /**
     * Handle student registration
     */
    public function register($name, $section, $password) {
        // Validate inputs
        if (empty($name) || empty($section) || empty($password)) {
            return ['success' => false, 'message' => 'All fields are required'];
        }
        
        // Validate name
        if (strlen($name) < 2) {
            return ['success' => false, 'message' => 'Name must be at least 2 characters'];
        }
        
        // Validate password
        if (strlen($password) < 6) {
            return ['success' => false, 'message' => 'Password must be at least 6 characters'];
        }
        
        // Check if name already exists (for registered students)
        if ($this->studentModel->existsByName($name)) {
            return ['success' => false, 'message' => 'A student with this name already exists. Please use a different name.'];
        }
        
        // Register student
        if ($this->studentModel->register($name, $section, $password)) {
            return ['success' => true, 'message' => 'Registration successful! Please login.'];
        }
        
        return ['success' => false, 'message' => 'Registration failed. Please try again.'];
    }
    
    /**
     * Handle student login
     */
    public function login($name, $password) {
        if (empty($name) || empty($password)) {
            return ['success' => false, 'message' => 'Please enter your name and password'];
        }
        
        if ($this->studentModel->login($name, $password)) {
            return ['success' => true, 'message' => 'Login successful'];
        }
        
        return ['success' => false, 'message' => 'Invalid name or password'];
    }
    
    /**
     * Handle student logout
     */
    public function logout() {
        // Destroy all session data
        $_SESSION = [];
        session_unset();
        session_destroy();
        
        // Delete session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        
        return ['success' => true, 'message' => 'Logged out successfully'];
    }
    
    /**
     * Create guest student
     */
    public function createGuest($name, $section) {
        if (empty($name) || empty($section)) {
            return ['success' => false, 'message' => 'Name and section are required'];
        }
        
        // Validate name
        if (strlen($name) < 2) {
            return ['success' => false, 'message' => 'Name must be at least 2 characters'];
        }
        
        $studentId = $this->studentModel->createGuest($name, $section);
        
        if ($studentId) {
            // Set session for guest
            $_SESSION['student_id'] = $studentId;
            $_SESSION['student_name'] = $name;
            $_SESSION['student_section'] = $student['section'];
            $_SESSION['is_guest'] = true;
            
            return ['success' => true, 'student_id' => $studentId, 'message' => 'Guest account created'];
        }
        
        return ['success' => false, 'message' => 'Failed to create guest account'];
    }
    
    /**
     * Get student by ID
     */
    public function getStudent($id) {
        if (empty($id) || !is_numeric($id)) {
            return null;
        }
        
        return $this->studentModel->getById($id);
    }
    
    /**
     * Get all students
     */
    public function getAllStudents() {
        return $this->studentModel->getAll();
    }
    
    /**
     * Update student
     */
    public function updateStudent($id, $name, $section) {
        if (empty($id) || empty($name) || empty($section)) {
            return ['success' => false, 'message' => 'All fields are required'];
        }
        
        // Validate name
        if (strlen($name) < 2) {
            return ['success' => false, 'message' => 'Name must be at least 2 characters'];
        }
        
        // Check if name exists for another student
        $existingStudent = $this->studentModel->getByName($name);
        if ($existingStudent && $existingStudent['id'] != $id) {
            return ['success' => false, 'message' => 'Another student with this name already exists'];
        }
        
        if ($this->studentModel->update($id, $name, $section)) {
            return ['success' => true, 'message' => 'Student updated successfully'];
        }
        
        return ['success' => false, 'message' => 'Failed to update student'];
    }
    
    /**
     * Delete student
     */
    public function deleteStudent($id) {
        if (empty($id) || !is_numeric($id)) {
            return ['success' => false, 'message' => 'Invalid student ID'];
        }
        
        // Check if student exists
        $student = $this->studentModel->getById($id);
        if (!$student) {
            return ['success' => false, 'message' => 'Student not found'];
        }
        
        if ($this->studentModel->delete($id)) {
            return ['success' => true, 'message' => 'Student deleted successfully'];
        }
        
        return ['success' => false, 'message' => 'Failed to delete student'];
    }
    
    /**
     * Get student quiz history
     */
    public function getQuizHistory($studentId) {
        if (empty($studentId) || !is_numeric($studentId)) {
            return [];
        }
        
        return $this->studentModel->getQuizHistory($studentId);
    }
    
    /**
     * Check if student is logged in
     */
    public function isLoggedIn() {
        return isset($_SESSION['student_id']);
    }
    
    /**
     * Get current student info
     */
    public function getCurrentStudent() {
        if (!isset($_SESSION['student_id'])) {
            return null;
        }
        
        return [
            'id' => $_SESSION['student_id'],
            'name' => $_SESSION['student_name'],
             'section'  => $_SESSION['student_section'],
            'is_guest' => isset($_SESSION['is_guest']) ? $_SESSION['is_guest'] : false
        ];
    }
    
    /**
     * Check if current user is a guest
     */
    public function isGuest() {
        return isset($_SESSION['is_guest']) && $_SESSION['is_guest'] === true;
    }
    
    /**
     * Get student statistics
     */
    public function getStudentStats($studentId) {
        if (empty($studentId) || !is_numeric($studentId)) {
            return null;
        }
        
        $quizHistory = $this->studentModel->getQuizHistory($studentId);
        
        $stats = [
            'total_quizzes' => count($quizHistory),
            'average_score' => 0,
            'highest_score' => 0,
            'lowest_score' => 0,
            'total_percentage' => 0
        ];
        
        if (!empty($quizHistory)) {
            $totalPercentage = 0;
            $scores = [];
            
            foreach ($quizHistory as $quiz) {
                $totalPercentage += $quiz['percentage'];
                $scores[] = $quiz['percentage'];
            }
            
            $stats['average_score'] = $totalPercentage / count($quizHistory);
            $stats['highest_score'] = max($scores);
            $stats['lowest_score'] = min($scores);
            $stats['total_percentage'] = $totalPercentage;
        }
        
        return $stats;
    }
    
    /**
     * Search students by name
     */
    public function searchStudents($searchTerm) {
        if (empty($searchTerm)) {
            return $this->getAllStudents();
        }
        
        $allStudents = $this->getAllStudents();
        $results = [];
        
        foreach ($allStudents as $student) {
            if (stripos($student['name'], $searchTerm) !== false) {
                $results[] = $student;
            }
        }
        
        return $results;
    }
    
    /**
     * Get students by section
     */
    public function getStudentsBySection($section) {
        if (empty($section)) {
            return [];
        }
        
        $allStudents = $this->getAllStudents();
        $results = [];
        
        foreach ($allStudents as $student) {
            if (strcasecmp($student['section'], $section) === 0) {
                $results[] = $student;
            }
        }
        
        return $results;
    }
    
    /**
     * Get registered students only (exclude guests)
     */
    public function getRegisteredStudents() {
        $allStudents = $this->getAllStudents();
        $results = [];
        
        foreach ($allStudents as $student) {
            if (!$student['is_guest']) {
                $results[] = $student;
            }
        }
        
        return $results;
    }
    
    /**
     * Get guest students only
     */
    public function getGuestStudents() {
        $allStudents = $this->getAllStudents();
        $results = [];
        
        foreach ($allStudents as $student) {
            if ($student['is_guest']) {
                $results[] = $student;
            }
        }
        
        return $results;
    }
    
    /**
     * Count total students
     */
    public function countStudents() {
        $students = $this->getAllStudents();
        return count($students);
    }
    
    /**
     * Count registered students
     */
    public function countRegisteredStudents() {
        $students = $this->getRegisteredStudents();
        return count($students);
    }
    
    /**
     * Count guest students
     */
    public function countGuestStudents() {
        $students = $this->getGuestStudents();
        return count($students);
    }
    
    /**
     * Validate student exists
     */
    public function studentExists($id) {
        if (empty($id) || !is_numeric($id)) {
            return false;
        }
        
        $student = $this->studentModel->getById($id);
        return $student !== false && $student !== null;
    }
    
    /**
     * Check if student can access quiz
     */
    public function canAccessQuiz($studentId, $quizId) {
        // Students can access any active quiz
        // This can be extended with more complex logic if needed
        return $this->studentExists($studentId);
    }
    
    /**
     * Get student's rank based on average score
     */
    public function getStudentRank($studentId) {
        $allStudents = $this->getAllStudents();
        $rankings = [];
        
        foreach ($allStudents as $student) {
            $stats = $this->getStudentStats($student['id']);
            $rankings[] = [
                'id' => $student['id'],
                'name' => $student['name'],
                'average' => $stats['average_score']
            ];
        }
        
        // Sort by average score descending
        usort($rankings, function($a, $b) {
            return $b['average'] <=> $a['average'];
        });
        
        // Find current student's rank
        foreach ($rankings as $rank => $student) {
            if ($student['id'] == $studentId) {
                return [
                    'rank' => $rank + 1,
                    'total' => count($rankings),
                    'percentile' => (1 - ($rank / count($rankings))) * 100
                ];
            }
        }
        
        return null;
    }
}
?>