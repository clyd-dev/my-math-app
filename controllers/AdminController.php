<?php
// controllers/AdminController.php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Admin.php';

class AdminController {
    private $adminModel;
    
    public function __construct() {
        $this->adminModel = new Admin();
    }
    
    /**
     * Handle admin login
     */
    public function login($username, $password) {
        if (empty($username) || empty($password)) {
            return ['success' => false, 'message' => 'Please enter username and password'];
        }
        
        if ($this->adminModel->login($username, $password)) {
            return ['success' => true, 'message' => 'Login successful'];
        }
        
        return ['success' => false, 'message' => 'Invalid username or password'];
    }
    
    /**
     * Handle admin logout
     */
    public function logout() {
        $this->adminModel->logout();
        return ['success' => true, 'message' => 'Logged out successfully'];
    }
    
    /**
     * Get dashboard statistics
     */
    public function getDashboardStats() {
        if (!isset($_SESSION['admin_id'])) {
            return ['success' => false, 'message' => 'Unauthorized'];
        }
        
        $stats = $this->adminModel->getDashboardStats();
        return ['success' => true, 'data' => $stats];
    }
    
    /**
     * Check if admin is logged in
     */
    public function isLoggedIn() {
        return isset($_SESSION['admin_id']);
    }
    
    /**
     * Get admin info
     */
    public function getAdminInfo() {
        if (!isset($_SESSION['admin_id'])) {
            return null;
        }
        
        return [
            'id' => $_SESSION['admin_id'],
            'name' => $_SESSION['admin_name']
        ];
    }
}
?>