<?php
// views/admin/delete-student.php - UPDATED WITH CONTROLLER
require_once '../../config/config.php';
require_once '../../controllers/StudentController.php';

// Check admin authentication
if(!isset($_SESSION['admin_id'])) {
    header("Location: " . APP_URL . "/views/admin/login.php");
    exit();
}

// Initialize controller
$studentController = new StudentController();

// Get student ID from URL
$studentId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Validate student ID
if($studentId <= 0) {
    $_SESSION['error_message'] = 'Invalid student ID';
    header("Location: " . APP_URL . "/views/admin/students.php");
    exit();
}

// Check if student exists
if(!$studentController->studentExists($studentId)) {
    $_SESSION['error_message'] = 'Student not found';
    header("Location: " . APP_URL . "/views/admin/students.php");
    exit();
}

// Get student info before deletion (for logging/message)
$student = $studentController->getStudent($studentId);
$studentName = $student['name'];

// Attempt to delete student using controller
$result = $studentController->deleteStudent($studentId);

// Handle result
if($result['success']) {
    $_SESSION['success_message'] = 'Student "' . htmlspecialchars($studentName) . '" has been successfully deleted along with all their quiz responses.';
} else {
    $_SESSION['error_message'] = $result['message'];
}

// Redirect back to students page
header("Location: " . APP_URL . "/views/admin/students.php");
exit();
?>