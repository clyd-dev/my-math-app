<?php
// views/admin/logout.php
require_once '../../config/config.php';
require_once '../../controllers/AdminController.php';

// Start session (required for session_destroy in model)
session_start();

$adminController = new AdminController();
$result = $adminController->logout();

// After controller/model logout, also ensure cookie is cleared
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Redirect back to landing page
header("Location: " . APP_URL . "/views/student/landing.php");
exit();
?>
