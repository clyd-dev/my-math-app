<?php
// views/student/logout.php - UPDATED WITH CONTROLLER
require_once '../../config/config.php';
require_once '../../controllers/StudentController.php';

$studentController = new StudentController();

// Perform logout
$result = $studentController->logout();

// Set success message
if($result['success']) {
    // Start new session to store flash message
    session_start();
    $_SESSION['success_message'] = 'You have been logged out successfully. See you next time! 👋';
}

// Redirect to landing page
header("Location: " . APP_URL . "/views/student/landing.php");
exit();
?>