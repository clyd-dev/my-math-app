<?php
// ===== FILE: admin/create-quiz.php =====
require_once '../../config/config.php';
require_once '../../models/Quiz.php';
include '../../includes/header.php';

if(!isset($_SESSION['admin_id'])) {
    header("Location: " . APP_URL . "/views/admin/login.php");
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = sanitize($_POST['title']);
    $date = sanitize($_POST['date']);
    $topic = sanitize($_POST['topic']);
    $instructions = sanitize($_POST['instructions']);
    
    $quiz = new Quiz();
    $quizId = $quiz->create($title, $date, $topic, $instructions, $_SESSION['admin_id']);
    
    if($quizId) {
        redirect('/views/admin/quiz-builder.php?id=' . $quizId);
    }
}
?>
<?php
$pageTitle = 'Create New Quiz';
$isAdmin = true;
include '../../includes/admin-layout.php';
?>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h3>Create New Quiz</h3>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="form-group">
                        <label>Quiz Title *</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Date *</label>
                        <input type="date" name="date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Topic</label>
                        <input type="text" name="topic" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Instructions *</label>
                        <textarea name="instructions" class="form-control" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Create Quiz & Add Questions</button>
                    <a href="<?php echo APP_URL; ?>/views/admin/dashboard.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
    <?php include '../../includes/admin-layout-footer.php'; ?>
</body>
</html>