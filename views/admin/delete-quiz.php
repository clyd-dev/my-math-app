<?php
// ===== FILE: views/admin/delete-quiz.php =====
require_once '../../config/config.php';
require_once '../../models/Quiz.php';

if(!isset($_SESSION['admin_id'])) {
    header("Location: " . APP_URL . "/views/admin/login.php");
    exit();
}

$quizId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if($quizId) {
    $quizModel = new Quiz();
    if($quizModel->delete($quizId)) {
        $_SESSION['success_message'] = 'Quiz deleted successfully!';
    } else {
        $_SESSION['error_message'] = 'Failed to delete quiz.';
    }
}

header("Location: " . APP_URL . "/views/admin/quizzes.php");
exit();
?>