<?php
require_once '../../config/config.php';

session_start();

// Destroy ALL session data
$_SESSION = [];
session_unset();
session_destroy();

// delete cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Redirect to landing page
header("Location: " . APP_URL . "/views/student/landing.php");
exit();

?>