<?php
// ===== FILE: views/admin/delete-question.php =====
require_once '../../config/config.php';
require_once '../../models/Quiz.php';

if(!isset($_SESSION['admin_id'])) {
    header("Location: " . APP_URL . "/views/admin/login.php");
    exit();
}

$questionId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$quizId = isset($_GET['quiz_id']) ? intval($_GET['quiz_id']) : 0;

if($questionId) {
    $quizModel = new Quiz();
    if($quizModel->deleteQuestion($questionId)) {
        $_SESSION['success_message'] = 'Question deleted successfully!';
    } else {
        $_SESSION['error_message'] = 'Failed to delete question.';
    }
}

header("Location: " . APP_URL . "/views/admin/view-quiz.php?id=" . $quizId);
exit();
?>