<?php
require_once '../../config/config.php';
require_once '../../models/Quiz.php';

if(!isset($_SESSION['admin_id'])) {
    redirect('views/admin/login.php');
}

$quizId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$quizModel = new Quiz();
$quiz = $quizModel->getById($quizId);
$questions = $quizModel->getQuestions($quizId);

if(!$quiz) {
    $_SESSION['error_message'] = 'Quiz not found';
    redirect('quizzes.php');
}

$pageTitle = 'View Quiz - ' . $quiz['title'];
$isAdmin = true;
?>
<?php include '../../includes/admin-layout.php'; ?>

<div class="container mt-5 pt-5">
    <!-- Quiz Header -->
    <div class="card shadow-lg mb-4">
        <div class="card-header text-white" style="background: linear-gradient(135deg, #667eea, #764ba2);">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="mb-0"><i class="fas fa-clipboard-list"></i> <?php echo htmlspecialchars($quiz['title']); ?></h3>
                <div>
                    <a href="<?php echo APP_URL; ?>/views/admin/quiz-builder.php?id=<?php echo $quiz['id']; ?>" class="btn btn-light btn-sm">
                        <i class="fas fa-edit"></i> Edit Questions
                    </a>
                    <a href="<?php echo APP_URL; ?>/views/admin/view-responses.php?id=<?php echo $quiz['id']; ?>" class="btn btn-light btn-sm">
                        <i class="fas fa-chart-bar"></i> View Responses
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong><i class="fas fa-calendar"></i> Date:</strong> <?php echo date('F d, Y', strtotime($quiz['date'])); ?></p>
                    <?php if($quiz['topic']): ?>
                        <p><strong><i class="fas fa-book"></i> Topic:</strong> <?php echo htmlspecialchars($quiz['topic']); ?></p>
                    <?php endif; ?>
                    <p><strong><i class="fas fa-question-circle"></i> Total Questions:</strong> <?php echo count($questions); ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong><i class="fas fa-link"></i> Share Code:</strong> 
                        <span class="badge badge-primary"><?php echo $quiz['share_code']; ?></span>
                        <button class="btn btn-sm btn-outline-primary" onclick="copyShareLink()">
                            <i class="fas fa-copy"></i> Copy Link
                        </button>
                    </p>
                    <p><strong><i class="fas fa-info-circle"></i> Status:</strong> 
                        <span class="badge badge-success"><?php echo ucfirst($quiz['status']); ?></span>
                    </p>
                </div>
            </div>
            <hr>
            <div>
                <strong><i class="fas fa-scroll"></i> Instructions:</strong>
                <p class="mt-2"><?php echo nl2br(htmlspecialchars($quiz['instructions'])); ?></p>
            </div>
        </div>
    </div>

    <!-- Questions List -->
    <div class="card shadow-lg">
        <div class="card-header bg-light">
            <h4 class="mb-0"><i class="fas fa-list"></i> Questions (<?php echo count($questions); ?>)</h4>
        </div>
        <div class="card-body">
            <?php if(empty($questions)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-question-circle fa-4x text-muted mb-3"></i>
                    <p class="text-muted">No questions added yet</p>
                    <a href="quiz-builder.php?id=<?php echo $quiz['id']; ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Questions
                    </a>
                </div>
            <?php else: ?>
                <?php foreach($questions as $index => $question): ?>
                    <div class="question-item mb-4 p-4 border rounded">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h5 class="mb-0">
                                <span class="badge badge-primary">Q<?php echo $index + 1; ?></span>
                                <?php echo htmlspecialchars($question['question_text']); ?>
                            </h5>
                            <button class="btn btn-sm btn-danger" onclick="deleteQuestion(<?php echo $question['id']; ?>)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <div class="choice-box <?php echo $question['correct_answer'] == 'A' ? 'correct-answer' : ''; ?>">
                                    <strong>A.</strong> <?php echo htmlspecialchars($question['choice_a']); ?>
                                    <?php if($question['correct_answer'] == 'A'): ?>
                                        <span class="badge badge-success float-right"><i class="fas fa-check"></i> Correct</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="choice-box <?php echo $question['correct_answer'] == 'B' ? 'correct-answer' : ''; ?>">
                                    <strong>B.</strong> <?php echo htmlspecialchars($question['choice_b']); ?>
                                    <?php if($question['correct_answer'] == 'B'): ?>
                                        <span class="badge badge-success float-right"><i class="fas fa-check"></i> Correct</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="choice-box <?php echo $question['correct_answer'] == 'C' ? 'correct-answer' : ''; ?>">
                                    <strong>C.</strong> <?php echo htmlspecialchars($question['choice_c']); ?>
                                    <?php if($question['correct_answer'] == 'C'): ?>
                                        <span class="badge badge-success float-right"><i class="fas fa-check"></i> Correct</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="choice-box <?php echo $question['correct_answer'] == 'D' ? 'correct-answer' : ''; ?>">
                                    <strong>D.</strong> <?php echo htmlspecialchars($question['choice_d']); ?>
                                    <?php if($question['correct_answer'] == 'D'): ?>
                                        <span class="badge badge-success float-right"><i class="fas fa-check"></i> Correct</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.choice-box {
    padding: 15px;
    border: 2px solid #e9ecef;
    border-radius: 10px;
    background: #f8f9fa;
}
.correct-answer {
    background: #d1fae5;
    border-color: #10b981;
}
.question-item {
    background: #ffffff;
}
.question-item:hover {
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}
</style>

<script>
function copyShareLink() {
    var baseUrl = window.location.origin + window.location.pathname.replace('admin/view-quiz.php', 'student/guest-quiz.php');
    var shareLink = baseUrl + '?code=<?php echo $quiz['share_code']; ?>';
    
    navigator.clipboard.writeText(shareLink).then(function() {
        alert('Share link copied!\n\n' + shareLink);
    });
}

function deleteQuestion(questionId) {
    if(confirm('Are you sure you want to delete this question?')) {
        window.location.href = 'delete-question.php?id=' + questionId + '&quiz_id=<?php echo $quiz['id']; ?>';
    }
}
</script>

<?php include '../../includes/admin-layout-footer.php'; ?>