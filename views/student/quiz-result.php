<?php
// ===== FILE: views/student/quiz-result.php =====
require_once '../../config/config.php';
require_once '../../models/Quiz.php';

if(!isset($_SESSION['student_id'])) {
    redirect('landing.php');
}

$responseId = isset($_GET['response']) ? intval($_GET['response']) : 0;

if(!$responseId) {
    $_SESSION['error_message'] = 'Invalid response ID';
    redirect('dashboard.php');
}

$quizModel = new Quiz();

// Get response details with all answers
$db = Database::getInstance()->getConnection();
$stmt = $db->prepare("SELECT qr.*, q.title, q.topic, q.instructions, s.name
                      FROM quiz_responses qr
                      JOIN quizzes q ON qr.quiz_id = q.id
                      JOIN students s ON qr.student_id = s.id
                      WHERE qr.id = ? AND qr.student_id = ?");
$stmt->execute([$responseId, $_SESSION['student_id']]);
$response = $stmt->fetch();

if(!$response) {
    $_SESSION['error_message'] = 'Response not found';
    redirect('dashboard.php');
}

$answers = $quizModel->getResponseDetails($responseId);

$pageTitle = 'Quiz Results';
$isAdmin = false;
include '../../includes/header.php';
?>

<div class="results-container">
    <!-- Results Header -->
    <div class="results-header">
        <div class="trophy-icon">
            <?php
            $percentage = $response['percentage'];
            if($percentage >= 90) echo '🏆';
            elseif($percentage >= 75) echo '🥇';
            elseif($percentage >= 60) echo '🥈';
            else echo '💪';
            ?>
        </div>
        
        <h1 class="mb-3">
            <?php
            if($percentage >= 90) echo 'Outstanding! 🎉';
            elseif($percentage >= 75) echo 'Great Job! 👏';
            elseif($percentage >= 60) echo 'Good Work! 👍';
            else echo 'Keep Practicing! 💪';
            ?>
        </h1>

        <div class="score-display">
            <div class="score-number-large">
                <?php echo $response['score']; ?>/<?php echo $response['total_questions']; ?>
            </div>
            <h3><?php echo number_format($percentage, 1); ?>%</h3>
            
            <div class="star-rating">
                <?php
                $stars = round($percentage / 20);
                for($i = 0; $i < 5; $i++) {
                    if($i < $stars) echo '<span class="star">⭐</span>';
                    else echo '<span class="star" style="opacity: 0.3;">⭐</span>';
                }
                ?>
            </div>
        </div>

        <div class="mt-4">
            <a href="dashboard.php" class="btn btn-primary btn-lg mr-2">
                <i class="fas fa-home"></i> Back to Dashboard
            </a>
            <?php if(isset($_SESSION['is_guest']) && $_SESSION['is_guest']): ?>
                <a href="landing.php" class="btn btn-success btn-lg">
                    <i class="fas fa-user-plus"></i> Create Account
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Answer Review -->
    <div class="review-section">
        <h2 class="text-center mb-4">
            <i class="fas fa-clipboard-check"></i> Review Your Answers
        </h2>
        
        <?php foreach($answers as $index => $answer): ?>
            <div class="review-item <?php echo $answer['is_correct'] ? 'correct' : 'incorrect'; ?>">
                <div class="d-flex align-items-start">
                    <div class="review-icon mr-3">
                        <?php if($answer['is_correct']): ?>
                            <i class="fas fa-check-circle text-success"></i>
                        <?php else: ?>
                            <i class="fas fa-times-circle text-danger"></i>
                        <?php endif; ?>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="mb-3">
                            <strong>Question <?php echo $index + 1; ?>:</strong>
                            <?php echo htmlspecialchars($answer['question_text']); ?>
                        </h5>
                        
                        <div class="row">
                            <?php
                            $choices = [
                                'A' => $answer['choice_a'],
                                'B' => $answer['choice_b'],
                                'C' => $answer['choice_c'],
                                'D' => $answer['choice_d']
                            ];
                            
                            foreach($choices as $letter => $text):
                                $isStudentAnswer = ($answer['student_answer'] == $letter);
                                $isCorrectAnswer = ($answer['correct_answer'] == $letter);
                                
                                $class = '';
                                if($isCorrectAnswer) $class = 'border-success bg-light-success';
                                elseif($isStudentAnswer) $class = 'border-danger bg-light-danger';
                            ?>
                                <div class="col-md-6 mb-2">
                                    <div class="p-3 border rounded <?php echo $class; ?>">
                                        <strong><?php echo $letter; ?>.</strong>
                                        <?php echo htmlspecialchars($text); ?>
                                        <?php if($isCorrectAnswer): ?>
                                            <span class="badge badge-success float-right">✓ Correct</span>
                                        <?php elseif($isStudentAnswer): ?>
                                            <span class="badge badge-danger float-right">✗ Your Answer</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
.bg-light-success { background-color: #d1fae5; }
.bg-light-danger { background-color: #fee2e2; }
</style>

<?php include '../../includes/footer.php'; ?>