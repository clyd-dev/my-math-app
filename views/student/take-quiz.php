<?php
// ===== FILE: student/take-quiz.php =====
require_once '../../config/config.php';
require_once '../../models/Quiz.php';
include '../../includes/header.php';

$quizId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$quiz = new Quiz();
$quizData = $quiz->getById($quizId);
$questions = $quiz->getQuestions($quizId);

if(!$quizData) {
    header("Location: " . APP_URL . "/views/student/dashboard.php");
   exit();
}
// Check if already answered
if(isset($_SESSION['student_id'])) {
    if($quiz->hasStudentAnswered($quizId, $_SESSION['student_id'])) {
        redirect('views/student/quiz-result.php?id=' . $quizId);
    }
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $answers = $_POST['answers'];
    $studentId = $_SESSION['student_id'];
    
    $responseId = $quiz->submitResponse($quizId, $studentId, $answers);
    
    if($responseId) {
        redirect('views/student/quiz-result.php?response=' . $responseId);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $quizData['title']; ?></title>
    <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 20px; }
        .quiz-container { max-width: 900px; margin: 0 auto; }
        .question-card { background: white; border-radius: 15px; padding: 30px; margin-bottom: 20px; }
        .choice-btn { padding: 15px; margin: 5px 0; text-align: left; font-size: 16px; }
        .choice-btn.selected { background-color: #667eea !important; color: white; }
    </style>
</head>
<body>
    <div class="quiz-container">
        <div class="card mb-4">
            <div class="card-body text-center">
                <h2><?php echo $quizData['title']; ?></h2>
                <p class="lead"><?php echo $quizData['instructions']; ?></p>
                <p><strong>Total Questions:</strong> <?php echo count($questions); ?></p>
            </div>
        </div>
        
        <form method="POST" id="quizForm">
            <?php foreach($questions as $index => $q): ?>
                <div class="question-card">
                    <h4 class="mb-4">Question <?php echo $index + 1; ?>: <?php echo $q['question_text']; ?></h4>
                    <div class="choices">
                        <?php foreach(['A' => $q['choice_a'], 'B' => $q['choice_b'], 'C' => $q['choice_c'], 'D' => $q['choice_d']] as $key => $value): ?>
                            <button type="button" class="btn btn-outline-primary btn-block choice-btn" 
                                    onclick="selectAnswer(<?php echo $q['id']; ?>, '<?php echo $key; ?>', this)">
                                <strong><?php echo $key; ?>.</strong> <?php echo $value; ?>
                            </button>
                            <input type="radio" name="answers[<?php echo $q['id']; ?>]" value="<?php echo $key; ?>" style="display:none;" id="answer_<?php echo $q['id']; ?>_<?php echo $key; ?>">
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <div class="text-center">
                <button type="submit" class="btn btn-success btn-lg" onclick="return confirm('Are you sure you want to submit?')">
                    🏆 Submit Quiz
                </button>
            </div>
        </form>
    </div>
    
    <script>
        function selectAnswer(questionId, choice, btn) {
            // Remove selected class from all buttons in this question
            var parent = btn.parentElement;
            var buttons = parent.querySelectorAll('.choice-btn');
            buttons.forEach(function(b) {
                b.classList.remove('selected');
            });
            
            // Add selected class to clicked button
            btn.classList.add('selected');
            
            // Check the corresponding radio button
            document.getElementById('answer_' + questionId + '_' + choice).checked = true;
        }
    </script>

    <?php include '../../includes/footer.php'; ?>
</body>
</html>