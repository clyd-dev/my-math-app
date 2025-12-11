<?php
// views/student/dashboard.php - UPDATED WITH CONTROLLER
require_once '../../config/config.php';
require_once '../../controllers/StudentController.php';
require_once '../../models/Quiz.php';

$studentController = new StudentController();
$quizModel = new Quiz();

// Check if student is logged in
if(!$studentController->isLoggedIn()) {
    header("Location: " . APP_URL . "/views/student/landing.php");
    exit();
}

// Get current student info
$currentStudent = $studentController->getCurrentStudent();
$studentId = $currentStudent['id'];
$studentName = $currentStudent['name'];
$isGuest = $currentStudent['is_guest'];

// Get all available quizzes
$allQuizzes = $quizModel->getAll();

// Get student's quiz history
$completedQuizzes = $studentController->getQuizHistory($studentId);
$completedQuizIds = array_column($completedQuizzes, 'quiz_id');

// Get student statistics
$studentStats = $studentController->getStudentStats($studentId);

$pageTitle = 'Dashboard';
$isAdmin = false;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/student-style.css">
    <style>
        .quiz-card {
    background: linear-gradient(145deg, #ffffff, #f3f6ff);
    border-radius: 25px;
    padding: 25px;
    box-shadow: 0 10px 20px rgba(0,0,0,0.08);
    transition: transform .2s ease, box-shadow .2s ease;
    position: relative;
    border: none;
}

.quiz-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 18px 30px rgba(0,0,0,0.12);
}

.quiz-icon {
    font-size: 45px;
    background: linear-gradient(135deg, #a855f7, #ec4899);
    width: 70px;
    height: 70px;
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    margin-bottom: 15px;
}

.quiz-title {
    font-size: 1.4rem;
    font-weight: 700;
    margin-bottom: 10px;
    color: #4b2aad;
}

.quiz-meta {
    background: #f6f0ff;
    padding: 12px 18px;
    border-radius: 15px;
    margin-bottom: 15px;
    font-size: 0.9rem;
}

.quiz-meta .meta-item {
    display: flex;
    align-items: center;
    margin-bottom: 6px;
    color: #6a4ea0;
}

.quiz-meta .meta-item i {
    margin-right: 8px;
}

.quiz-description {
    color: #555;
    font-size: 0.95rem;
    margin-bottom: 20px;
}

.btn-start-quiz {
    background: linear-gradient(135deg, #ec4899, #a855f7);
    color: white;
    font-weight: 600;
    padding: 12px 20px;
    border-radius: 15px;
    display: block;
    text-align: center;
    transition: opacity .2s;
}

.btn-start-quiz:hover {
    color: white;
    opacity: 0.85;
}

.completed-badge {
    position: absolute;
    top: 10px;
    right: 2px;
    background: #22c55e;
    color: white;
    padding: 8px 15px;
    font-size: 0.85rem;
    border-radius: 50px;
    box-shadow: 0 5px 15px rgba(34,197,94,0.4);
}

/* QUIZ GRID IMPROVED */
.quiz-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    grid-gap: 25px;
}

    </style>
</head>
<body>

<div class="student-dashboard">
    <!-- Header -->
    <nav class="navbar navbar-expand-lg navbar-dark shadow-lg" style="background: linear-gradient(135deg, #667eea, #764ba2);">
        <div class="container">
            <a class="navbar-brand" href="<?php echo APP_URL; ?>/views/student/dashboard.php">
                <i class="fas fa-graduation-cap"></i> Math Adventure
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <span class="navbar-text text-white mr-3">
                            <i class="fas fa-user"></i> <?php echo htmlspecialchars($studentName); ?>
                            <?php if($isGuest): ?>
                                <span class="badge badge-warning ml-1">Guest</span>
                            <?php endif; ?>
                        </span>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo APP_URL; ?>/views/student/logout.php" class="btn btn-light btn-sm">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Success/Error Messages -->
        <?php if(isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle"></i> <?php echo $_SESSION['success_message']; ?>
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <!-- Welcome Header -->
        <div class="dashboard-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="welcome-text">
                        Welcome back, <?php echo htmlspecialchars($studentName); ?>! 🎉
                    </h1>
                    <p class="dashboard-subtitle">Ready to take on some math challenges?</p>
                    
                    <?php if($isGuest): ?>
                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle"></i> You're using a guest account. 
                            <a href="<?php echo APP_URL; ?>/views/student/register.php" class="alert-link">
                                Create a permanent account
                            </a> to save your progress!
                        </div>
                    <?php endif; ?>
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

        <!-- Statistics Cards -->
        <?php if($studentStats['total_quizzes'] > 0): ?>
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white shadow">
                    <div class="card-body text-center">
                        <h3><?php echo $studentStats['total_quizzes']; ?></h3>
                        <p class="mb-0">Quizzes Taken</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white shadow">
                    <div class="card-body text-center">
                        <h3><?php echo number_format($studentStats['average_score'], 1); ?>%</h3>
                        <p class="mb-0">Average Score</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white shadow">
                    <div class="card-body text-center">
                        <h3><?php echo number_format($studentStats['highest_score'], 1); ?>%</h3>
                        <p class="mb-0">Highest Score</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white shadow">
                    <div class="card-body text-center">
                        <h3><?php echo number_format($studentStats['lowest_score'], 1); ?>%</h3>
                        <p class="mb-0">Lowest Score</p>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Available Quizzes -->
        <div class="mb-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-clipboard-list"></i> Available Quizzes</h2>
                <span class="badge badge-primary badge-lg">
                    <?php echo count($allQuizzes) - count($completedQuizIds); ?> New
                </span>
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
                        <?php if($isGuest): ?>
                            <a href="<?php echo APP_URL; ?>/views/student/register.php" class="btn btn-success btn-lg mt-3">
                                <i class="fas fa-user-plus"></i> Create Account to Save Progress
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="quiz-grid">
                    <?php foreach($availableQuizzes as $quiz): ?>
                        <div class="quiz-card">
                            
                            <div class="quiz-icon">📝</div>

                            <h3 class="quiz-title">
                                <?php echo htmlspecialchars($quiz['title']); ?>
                            </h3>

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

                            <a href="<?php echo APP_URL; ?>/views/student/take-quiz.php?id=<?php echo $quiz['id']; ?>" 
                            class="btn btn-start-quiz">
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
                <h2 class="mb-4">
                    <i class="fas fa-check-circle text-success"></i> Completed Quizzes 
                    <span class="badge badge-success"><?php echo count($completedQuizzes); ?></span>
                </h2>
                <div class="quiz-grid">
                    <?php foreach($completedQuizzes as $completed): ?>
                        <div class="quiz-card" style="opacity: 0.9;">
                            <div class="completed-badge">
                                <i class="fas fa-check"></i> Completed
                            </div>
                            <div class="quiz-icon">✅</div>
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
                                    <div class="progress-bar <?php echo $progressColor; ?>" 
                                         style="width: <?php echo $percentage; ?>%">
                                        <?php echo number_format($percentage, 1); ?>%
                                    </div>
                                </div>
                            </div>
                            <small class="text-muted">
                                <i class="fas fa-clock"></i>
                                Completed: <?php echo date('M d, Y', strtotime($completed['submitted_at'])); ?>
                            </small>
                            <a href="<?php echo APP_URL; ?>/views/student/quiz-result.php?response=<?php echo $completed['id']; ?>" 
                               class="btn btn-start-quiz mt-3">
                                <i class="fas fa-eye"></i> View Results
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>
<script src="../../assets/js/student-script.js"></script>
</body>
</html>