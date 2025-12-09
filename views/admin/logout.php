<?php
session_start();

$_SESSION = [];
session_unset();
session_destroy();

// delete cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

header("Location: views/student/landing.php");
exit;
?>