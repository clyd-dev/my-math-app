<?php
// ===== FILE: views/admin/delete-student.php =====
require_once '../../config/config.php';
require_once '../../models/Student.php';

if(!isset($_SESSION['admin_id'])) {
    header("Location: " . APP_URL . "/views/admin/login.php");
    exit();
}

$studentId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if($studentId) {
    $studentModel = new Student();
    if($studentModel->delete($studentId)) {
        $_SESSION['success_message'] = 'Student deleted successfully!';
    } else {
        $_SESSION['error_message'] = 'Failed to delete student.';
    }
}

header("Location: " . APP_URL . "/views/admin/students.php");
exit();