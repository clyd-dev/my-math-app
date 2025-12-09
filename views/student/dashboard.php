<?php
require_once '../../config/config.php';
require_once '../../models/Quiz.php';
require_once '../../models/Student.php';


if(!isset($_SESSION['student_id'])) {
    redirect('views/student/landing.php');
}

$studentId = $_SESSION['student_id'];
$studentName = $_SESSION['student_name'];

$quizModel = new Quiz();
$studentModel = new Student();

// Get all available quizzes
$allQuizzes = $quizModel->getAll();

// Get student's quiz history
$completedQuizzes = $studentModel->getQuizHistory($studentId);
$completedQuizIds = array_column($completedQuizzes, 'quiz_id');

$pageTitle = 'Dashboard';
$isAdmin = false;
?>
<?php include '../../includes/header.php'; ?>

<div class="student-dashboard">
    <!-- Header -->
    <nav class="navbar navbar-expand-lg navbar-dark shadow-lg" style="background: linear-gradient(135deg, #667eea, #764ba2);">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-graduation-cap"></i> Math Adventure
            </a>
            <div class="ml-auto d-flex align-items-center">
                <span class="text-white mr-3">
                    <i class="fas fa-user"></i> <?php echo htmlspecialchars($studentName); ?>
                </span>
                <a href="logout.php" class="btn btn-light btn-sm">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Welcome Header -->
        <div class="dashboard-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="welcome-text">Welcome back, <?php echo htmlspecialchars($studentName); ?>! 🎉</h1>
                    <p class="dashboard-subtitle">Ready to take on some math challenges?</p>
                </div>
                <div class="col-md-4 text-right">
                    <div class="d-inline-block text-center">
                        <div class="teacher-avatar" style="width: 80px; height: 80px; font-size: 40px;">
                            👩‍🏫
                        </div>
                        <p class="mb-0 mt-2"><small>Teacher Lilibeth</small></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Available Quizzes -->
        <div class="mb-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-clipboard-list"></i> Available Quizzes</h2>
                <span class="badge badge-primary badge-lg"><?php echo count($allQuizzes) - count($completedQuizIds); ?> New</span>
            </div>

            <?php
            $availableQuizzes = array_filter($allQuizzes, function($quiz) use ($completedQuizIds) {
                return !in_array($quiz['id'], $completedQuizIds);
            });
            ?>

            <?php if(empty($availableQuizzes)): ?>
                <div class="card shadow-lg text-center py-5">
                    <div class="card-body">
                        <i class="fas fa-check-circle fa-5x text-success mb-3"></i>
                        <h3>All Caught Up!</h3>
                        <p class="text-muted">You've completed all available quizzes. Great job! 🎉</p>
                    </div>
                </div>
            <?php else: ?>
                <div class="quiz-grid">
                    <?php foreach($availableQuizzes as $quiz): ?>
                        <div class="quiz-card">
                            <div class="quiz-icon">
                                📝
                            </div>
                            <h3 class="quiz-title"><?php echo htmlspecialchars($quiz['title']); ?></h3>
                            <div class="quiz-meta">
                                <div class="meta-item">
                                    <i class="fas fa-calendar"></i>
                                    <?php echo date('M d, Y', strtotime($quiz['date'])); ?>
                                </div>
                                <?php if($quiz['topic']): ?>
                                    <div class="meta-item">
                                        <i class="fas fa-book"></i>
                                        <?php echo htmlspecialchars($quiz['topic']); ?>
                                    </div>
                                <?php endif; ?>
                                <div class="meta-item">
                                    <i class="fas fa-question-circle"></i>
                                    <?php echo $quiz['question_count']; ?> Questions
                                </div>
                            </div>
                            <p class="quiz-description">
                                <?php echo htmlspecialchars(substr($quiz['instructions'], 0, 100)); ?>...
                            </p>
                            <a href="take-quiz.php?id=<?php echo $quiz['id']; ?>" class="btn btn-start-quiz">
                                <i class="fas fa-play"></i> Start Quiz
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Completed Quizzes -->
        <?php if(!empty($completedQuizzes)): ?>
            <div class="mb-5">
                <h2 class="mb-4"><i class="fas fa-check-circle text-success"></i> Completed Quizzes</h2>
                <div class="quiz-grid">
                    <?php foreach($completedQuizzes as $completed): ?>
                        <div class="quiz-card" style="opacity: 0.9;">
                            <div class="completed-badge">
                                <i class="fas fa-check"></i> Completed
                            </div>
                            <div class="quiz-icon">
                                ✅
                            </div>
                            <h3 class="quiz-title"><?php echo htmlspecialchars($completed['title']); ?></h3>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Your Score:</span>
                                    <strong><?php echo $completed['score']; ?>/<?php echo $completed['total_questions']; ?></strong>
                                </div>
                                <div class="progress" style="height: 25px;">
                                    <?php
                                    $percentage = $completed['percentage'];
                                    $progressColor = 'bg-danger';
                                    if($percentage >= 90) $progressColor = 'bg-success';
                                    elseif($percentage >= 75) $progressColor = 'bg-info';
                                    elseif($percentage >= 60) $progressColor = 'bg-warning';
                                    ?>
                                    <div class="progress-bar <?php echo $progressColor; ?>" style="width: <?php echo $percentage; ?>%">
                                        <?php echo number_format($percentage, 1); ?>%
                                    </div>
                                </div>
                            </div>
                            <small class="text-muted">
                                <i class="fas fa-clock"></i>
                                Completed: <?php echo date('M d, Y', strtotime($completed['submitted_at'])); ?>
                            </small>
                            <a href="quiz-result.php?response=<?php echo $completed['id']; ?>" class="btn btn-start-quiz mt-3">
                                <i class="fas fa-eye"></i> View Results
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>