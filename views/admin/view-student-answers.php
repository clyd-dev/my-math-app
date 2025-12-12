<?php
// views/admin/view-student-answers.php
require_once '../../config/config.php';
require_once '../../models/Quiz.php';

// Check admin authentication
if(!isset($_SESSION['admin_id'])) {
    header("Location: " . APP_URL . "/views/admin/login.php");
    exit();
}

// Get response ID from URL
$responseId = isset($_GET['response_id']) ? intval($_GET['response_id']) : 0;

if(!$responseId) {
    $_SESSION['error_message'] = 'Invalid response ID';
    header("Location: " . APP_URL . "/views/admin/quizzes.php");
    exit();
}

$quizModel = new Quiz();

// Get response details
$db = Database::getInstance()->getConnection();
$stmt = $db->prepare("
    SELECT qr.*, q.title as quiz_title, q.topic, s.name as student_name, s.section
    FROM quiz_responses qr
    JOIN quizzes q ON qr.quiz_id = q.id
    JOIN students s ON qr.student_id = s.id
    WHERE qr.id = ?
");
$stmt->execute([$responseId]);
$response = $stmt->fetch();

if(!$response) {
    $_SESSION['error_message'] = 'Response not found';
    header("Location: " . APP_URL . "/views/admin/quizzes.php");
    exit();
}

// Get detailed answers
$answers = $quizModel->getResponseDetails($responseId);

$pageTitle = 'Student Answers - ' . $response['student_name'];
include '../../includes/admin-layout.php';
?>

<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="text-white">
                <i class="fas fa-file-alt"></i> Student Answer Review
            </h2>
            <a href="<?php echo APP_URL; ?>/views/admin/view-responses.php?id=<?php echo $response['quiz_id']; ?>" 
               class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Responses
            </a>
        </div>
    </div>

    <!-- Student Info Card -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-lg">
                <div class="card-header text-white" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h4 class="mb-0">
                                <i class="fas fa-user"></i> <?php echo htmlspecialchars($response['student_name']); ?>
                            </h4>
                            <p class="mb-0 mt-1">
                                <i class="fas fa-door-open"></i> Section: <?php echo htmlspecialchars($response['section']); ?>
                            </p>
                        </div>
                        <div class="col-md-6 text-right">
                            <h4 class="mb-0">
                                <i class="fas fa-clipboard-list"></i> <?php echo htmlspecialchars($response['quiz_title']); ?>
                            </h4>
                            <?php if($response['topic']): ?>
                                <p class="mb-0 mt-1">
                                    <i class="fas fa-book"></i> Topic: <?php echo htmlspecialchars($response['topic']); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="border rounded p-3 bg-light">
                                <h2 class="mb-0 text-primary">
                                    <?php echo $response['score']; ?>/<?php echo $response['total_questions']; ?>
                                </h2>
                                <p class="mb-0 text-muted">Score</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3 bg-light">
                                <?php
                                $percentage = $response['percentage'];
                                $badgeClass = 'secondary';
                                if($percentage >= 90) $badgeClass = 'success';
                                elseif($percentage >= 75) $badgeClass = 'primary';
                                elseif($percentage >= 60) $badgeClass = 'warning';
                                else $badgeClass = 'danger';
                                ?>
                                <h2 class="mb-0 text-<?php echo $badgeClass; ?>">
                                    <?php echo number_format($percentage, 1); ?>%
                                </h2>
                                <p class="mb-0 text-muted">Percentage</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3 bg-light">
                                <h2 class="mb-0 text-success">
                                    <?php echo $response['score']; ?>
                                </h2>
                                <p class="mb-0 text-muted">Correct</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3 bg-light">
                                <h2 class="mb-0 text-danger">
                                    <?php echo $response['total_questions'] - $response['score']; ?>
                                </h2>
                                <p class="mb-0 text-muted">Incorrect</p>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="text-center">
                        <p class="mb-0">
                            <i class="fas fa-calendar"></i> Submitted: 
                            <strong><?php echo date('F d, Y h:i A', strtotime($response['submitted_at'])); ?></strong>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Answers Review -->
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-lg">
                <div class="card-header bg-white">
                    <h4 class="mb-0">
                        <i class="fas fa-list-ol"></i> Detailed Answer Review
                    </h4>
                </div>
                <div class="card-body">
                    <?php foreach($answers as $index => $answer): ?>
                        <div class="question-review-item mb-4 p-4 border rounded <?php echo $answer['is_correct'] ? 'border-success bg-light-success' : 'border-danger bg-light-danger'; ?>">
                            <div class="d-flex align-items-start">
                                <div class="question-number mr-3">
                                    <div class="badge badge-lg <?php echo $answer['is_correct'] ? 'badge-success' : 'badge-danger'; ?>" 
                                         style="font-size: 24px; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                        <?php if($answer['is_correct']): ?>
                                            <i class="fas fa-check"></i>
                                        <?php else: ?>
                                            <i class="fas fa-times"></i>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="mb-3">
                                        <span class="badge badge-secondary">Q<?php echo $index + 1; ?></span>
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
                                            
                                            $class = 'border-secondary';
                                            $bgClass = '';
                                            $icon = '';
                                            
                                            if($isCorrectAnswer) {
                                                $class = 'border-success';
                                                $bgClass = 'bg-success-light';
                                                $icon = '<i class="fas fa-check-circle text-success"></i>';
                                            }
                                            if($isStudentAnswer && !$isCorrectAnswer) {
                                                $class = 'border-danger';
                                                $bgClass = 'bg-danger-light';
                                                $icon = '<i class="fas fa-times-circle text-danger"></i>';
                                            }
                                        ?>
                                            <div class="col-md-6 mb-3">
                                                <div class="p-3 border rounded <?php echo $class; ?> <?php echo $bgClass; ?>" 
                                                     style="<?php echo ($isStudentAnswer || $isCorrectAnswer) ? 'border-width: 3px !important;' : ''; ?>">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <div>
                                                            <strong class="badge badge-light"><?php echo $letter; ?></strong>
                                                            <span class="ml-2"><?php echo htmlspecialchars($text); ?></span>
                                                        </div>
                                                        <div class="ml-2">
                                                            <?php if($isCorrectAnswer): ?>
                                                                <span class="badge badge-success">
                                                                    <i class="fas fa-check"></i> Correct Answer
                                                                </span>
                                                            <?php endif; ?>
                                                            <?php if($isStudentAnswer && !$isCorrectAnswer): ?>
                                                                <span class="badge badge-danger">
                                                                    <i class="fas fa-times"></i> Student's Answer
                                                                </span>
                                                            <?php endif; ?>
                                                            <?php if($isStudentAnswer && $isCorrectAnswer): ?>
                                                                <span class="badge badge-success">
                                                                    <i class="fas fa-check-double"></i> Correct!
                                                                </span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>

                                    <?php if(!$answer['is_correct']): ?>
                                        <div class="alert alert-info mt-2 mb-0">
                                            <i class="fas fa-info-circle"></i> 
                                            <strong>Student selected:</strong> <?php echo $answer['student_answer']; ?> - 
                                            <?php echo htmlspecialchars($choices[$answer['student_answer']]); ?>
                                            <br>
                                            <i class="fas fa-lightbulb"></i> 
                                            <strong>Correct answer:</strong> <?php echo $answer['correct_answer']; ?> - 
                                            <?php echo htmlspecialchars($choices[$answer['correct_answer']]); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row mt-4 mb-5">
        <div class="col-md-12 text-center">
            <a href="<?php echo APP_URL; ?>/views/admin/view-responses.php?id=<?php echo $response['quiz_id']; ?>" 
               class="btn btn-secondary btn-lg">
                <i class="fas fa-arrow-left"></i> Back to All Responses
            </a>
            <a href="<?php echo APP_URL; ?>/views/admin/view-student.php?id=<?php echo $response['student_id']; ?>" 
               class="btn btn-info btn-lg">
                <i class="fas fa-user"></i> View Student Profile
            </a>
            <button onclick="window.print()" class="btn btn-primary btn-lg">
                <i class="fas fa-print"></i> Print Answers
            </button>
        </div>
    </div>
</div>

<style>
.bg-light-success {
    background-color: #d1fae5 !important;
}
.bg-light-danger {
    background-color: #fee2e2 !important;
}
.bg-success-light {
    background-color: #ecfdf5 !important;
}
.bg-danger-light {
    background-color: #fef2f2 !important;
}
.question-review-item {
    transition: all 0.3s;
}
.question-review-item:hover {
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    transform: translateX(5px);
}
.question-number {
    flex-shrink: 0;
}
@media print {
    .btn, .navbar, .sidebar, .card-header {
        display: none !important;
    }
    .main-panel {
        margin-left: 0 !important;
    }
    .question-review-item {
        page-break-inside: avoid;
    }
}
</style>

<?php include '../../includes/admin-layout-footer.php'; ?>