<?php
// views/admin/create-quiz.php
require_once '../../config/config.php';
require_once '../../models/Quiz.php';
require_once '../../models/Section.php';

if(!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$quizModel = new Quiz();
$sectionModel = new Section();
$sections = $sectionModel->getAll();
$section = !empty($_POST['section']) ? trim($_POST['section']) : null;

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = sanitize($_POST['title']);
    $date = sanitize($_POST['date']);
    $topic = sanitize($_POST['topic'] ?? '');
    $instructions = sanitize($_POST['instructions']);
    $section = !empty($_POST['section']) ? $_POST['section'] : null;

    $quizId = $quizModel->create($title, $date, $topic, $instructions, $_SESSION['admin_id'], $section);
    
    if($quizId) {
        $_SESSION['success_message'] = "Quiz created! Now add questions.";
        header("Location: quiz-builder.php?id=" . $quizId);
        exit();
    }
}

$pageTitle = 'Create New Quiz';
include '../../includes/admin-layout.php';
?>

<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
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
                    <label>Topic (optional)</label>
                    <input type="text" name="topic" class="form-control">
                </div>
                <div class="form-group">
                    <label>Assign to Section (optional)</label>
                    <select name="section" class="form-control">
                        <option value="">All Sections (Public)</option>
                        <?php foreach($sections as $s): ?>
                            <option value="<?php echo htmlspecialchars($s['name']); ?>">
                                <?php echo htmlspecialchars($s['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small class="text-muted">Only students from selected section will see this quiz</small>
                </div>
                <div class="form-group">
                    <label>Instructions *</label>
                    <textarea name="instructions" class="form-control" rows="4" required></textarea>
                </div>
                <button type="submit" class="btn btn-success btn-lg">Create Quiz & Add Questions</button>
                <a href="dashboard.php" class="btn btn-secondary btn-lg">Cancel</a>
            </form>
        </div>
    </div>
</div>

<?php include '../../includes/admin-layout-footer.php'; ?>