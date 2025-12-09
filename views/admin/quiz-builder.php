<?php
// ===== FILE: admin/quiz-builder.php =====
require_once '../../config/config.php';
require_once '../../models/Quiz.php';
include '../../includes/header.php';

if(!isset($_SESSION['admin_id'])) {
    header("Location: " . APP_URL . "/views/admin/login.php");
    exit();
}

$quizId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$quiz = new Quiz();
$quizData = $quiz->getById($quizId);
$questions = $quiz->getQuestions($quizId);

if(!$quizData) redirect('/views/admin/quizzes.php');

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_question'])) {
    $questionText = sanitize($_POST['question_text']);
    $choiceA = sanitize($_POST['choice_a']);
    $choiceB = sanitize($_POST['choice_b']);
    $choiceC = sanitize($_POST['choice_c']);
    $choiceD = sanitize($_POST['choice_d']);
    $correctAnswer = $_POST['correct_answer'];
    
    $quiz->addQuestion($quizId, $questionText, $choiceA, $choiceB, $choiceC, $choiceD, $correctAnswer);
    redirect('/views/admin/quiz-builder.php?id=' . $quizId);
}
?>
<?php
$pageTitle = 'Quiz Builder - ' . $quizData['title'];
$isAdmin = true;
include '../../includes/admin-layout.php';
?>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h3>Add Questions to: <?php echo $quizData['title']; ?></h3>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="form-group">
                                <label>Question *</label>
                                <textarea name="question_text" class="form-control" rows="2" required></textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Choice A *</label>
                                        <input type="text" name="choice_a" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Choice B *</label>
                                        <input type="text" name="choice_b" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Choice C *</label>
                                        <input type="text" name="choice_c" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Choice D *</label>
                                        <input type="text" name="choice_d" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Correct Answer *</label>
                                <select name="correct_answer" class="form-control" required>
                                    <option value="">Select Correct Answer</option>
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                    <option value="C">C</option>
                                    <option value="D">D</option>
                                </select>
                            </div>
                            <button type="submit" name="add_question" class="btn btn-success">Add Question</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Questions Added (<?php echo count($questions); ?>)</h5>
                    </div>
                    <div class="card-body">
                        <?php if(empty($questions)): ?>
                            <p class="text-muted">No questions yet</p>
                        <?php else: ?>
                            <ol>
                                <?php foreach($questions as $q): ?>
                                    <li class="mb-2">
                                        <?php echo substr($q['question_text'], 0, 50); ?>...
                                        <span class="badge badge-success">Correct: <?php echo $q['correct_answer']; ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ol>
                        <?php endif; ?>
                        <hr>
                        <a href="<?php echo APP_URL; ?>/views/admin/view-quiz.php?id=<?php echo $quizId; ?>" class="btn btn-primary btn-block">Finish & View Quiz</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>