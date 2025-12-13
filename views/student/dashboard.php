<?php
// =======================================================
//  STUDENT DASHBOARD – FINAL WORKING VERSION (DESIGN APPLIED + FIXED)
// =======================================================

require_once '../../config/config.php';

// Must be logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: " . APP_URL . "/views/student/landing.php");
    exit();
}

$studentId = (int)$_SESSION['student_id'];

// 1. GET STUDENT DATA
$db = Database::getInstance()->getConnection();
$stmt = $db->prepare("SELECT name, section, is_guest FROM students WHERE id = ?");
$stmt->execute([$studentId]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    session_destroy();
    header("Location: " . APP_URL . "/views/student/landing.php");
    exit();
}

$studentName    = $student['name'];
$rawSection     = $student['section'] ?? '';
$studentSection = trim($rawSection);
$isGuest        = !empty($student['is_guest']);

// 2. LOAD ALL ACTIVE QUIZZES
$stmt = $db->query("
    SELECT 
        q.id, q.title, q.date, q.topic, q.instructions, q.section AS quiz_section,
        COUNT(qst.id) AS question_count
    FROM quizzes q
    LEFT JOIN questions qst ON q.id = qst.quiz_id
    WHERE q.status = 'active'
    GROUP BY q.id
    ORDER BY q.date DESC
");
$allQuizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 3. FILTER BY SECTION
$visibleQuizzes = [];

foreach ($allQuizzes as $quiz) {
    $quizCleanSection = trim($quiz['quiz_section'] ?? '');
    $isPublic = empty($quizCleanSection);
    $matchesSection = strcasecmp($quizCleanSection, $studentSection) === 0;

    if ($isPublic || $matchesSection) {
        $visibleQuizzes[] = $quiz;
    }
}

// 4. COMPLETED QUIZZES
// JOIN quiz_responses with quizzes to include quiz title (avoids undefined index)
$stmt = $db->prepare("
    SELECT qr.*, q.title AS quiz_title
    FROM quiz_responses qr
    LEFT JOIN quizzes q ON qr.quiz_id = q.id
    WHERE qr.student_id = ?
    ORDER BY qr.submitted_at DESC
");
$stmt->execute([$studentId]);
$completedQuizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Extract completed quiz IDs (safe)
$completedQuizIds = array_column($completedQuizzes, 'quiz_id');

// 5. STUDENT STATS
$studentStats = [
    'total_taken'     => 0,
    'average_score'   => 0.0,
    'highest_score'   => 0.0,
    'lowest_score'    => 0.0,
    'total_questions' => 0,
    'total_quizzes'   => count($visibleQuizzes),
];

$stmt = $db->prepare("
    SELECT 
        COUNT(*) AS taken,
        AVG(percentage) AS avg_score,
        MAX(percentage) AS max_score,
        MIN(percentage) AS min_score,
        SUM(total_questions) AS total_q
    FROM quiz_responses 
    WHERE student_id = ?
");
$stmt->execute([$studentId]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row && $row['taken'] > 0) {
    $studentStats['total_taken']     = (int)$row['taken'];
    $studentStats['average_score']   = round((float)$row['avg_score'], 1);
    $studentStats['highest_score']   = round((float)$row['max_score'], 1);
    $studentStats['lowest_score']    = round((float)$row['min_score'], 1);
    $studentStats['total_questions'] = (int)$row['total_q'];
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
/* ✔ DESIGN FROM FIRST DASHBOARD APPLIED HERE */
/* ---- CARDS ---- */
.quiz-card {
    background: linear-gradient(145deg, #ffffff, #f3f6ff);
    border-radius: 25px;
    padding: 25px;
    box-shadow: 0 10px 20px rgba(0,0,0,0.08);
    transition: .2s;
    position: relative;
}
.quiz-card:hover { transform: translateY(-5px); box-shadow: 0 18px 30px rgba(0,0,0,0.12); }

/* ---- ICON ---- */
.quiz-icon {
    font-size: 45px;
    background: linear-gradient(135deg, #a855f7, #ec4899);
    width: 70px; height: 70px;
    border-radius: 20px;
    display: flex; align-items: center; justify-content: center;
    color: white;
    margin-bottom: 15px;
}

/* ---- TITLE ---- */
.quiz-title { font-size: 1.4rem; font-weight: 700; color: #4b2aad; margin-bottom:10px; }

/* ---- META BOX ---- */
.quiz-meta {
    background: #f6f0ff;
    padding: 12px 18px;
    border-radius: 15px;
    margin-bottom: 15px;
}
.quiz-meta .meta-item { display: flex; align-items: center; color: #6a4ea0; margin-bottom: 6px; }
.quiz-meta .meta-item i { margin-right: 8px; }

/* ---- BUTTON ---- */
.btn-start-quiz {
    background: linear-gradient(135deg, #ec4899, #a855f7);
    color: white;
    font-weight: 600;
    padding: 12px 20px;
    border-radius: 15px;
    display: inline-block;
    text-align: center;
    text-decoration: none;
}

/* ---- GRID ---- */
.quiz-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    grid-gap: 25px;
}
.completed-badge {
    position:absolute; top:10px; right:10px;
    background:#22c55e;
    color:white; padding:6px 14px;
    border-radius:50px;
    box-shadow: 0 5px 15px rgba(34,197,94,0.4);
}
    </style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark shadow-lg" style="background: linear-gradient(135deg,#667eea,#764ba2);">
    <div class="container">
        <a class="navbar-brand" href="<?=APP_URL?>/views/student/dashboard.php">
            <i class="fas fa-graduation-cap"></i> Math Adventure
        </a>
        <div class="ml-auto d-flex align-items-center">
            <span class="navbar-text text-white mr-3">
                <i class="fas fa-user"></i> <?=htmlspecialchars($studentName)?>
                <?php if ($isGuest): ?>
                    <span class="badge badge-warning ml-1">Guest</span>
                <?php endif; ?>
            </span>
            <a href="<?=APP_URL?>/views/student/logout.php" class="btn btn-light btn-sm">Logout</a>
        </div>
    </div>
</nav>

<div class="container mt-4">

    <div class="row align-items-center mb-3">
        <div class="col-md-8">
            <h1 class="mb-0">Welcome back, <?=htmlspecialchars($studentName)?>! 🎉</h1>
            <p class="text-muted mb-0">Ready to take on some math challenges?</p>
        </div>
        <div class="col-md-4 text-right">
            <div style="display:inline-block;text-align:center">
                <div style="width:80px;height:80px;font-size:40px;border-radius:10px;background:#fff;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 10px rgba(0,0,0,0.06)">👩‍🏫</div>
                <small class="d-block mt-2">Teacher Lilibeth</small>
            </div>
        </div>
    </div>

    <!-- GUEST WARNING -->
    <?php if ($isGuest): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> You're using a guest account. <a href="<?=APP_URL?>/views/student/register.php" class="alert-link">Create a permanent account</a> to save your progress!
        </div>
    <?php endif; ?>

    <!-- STATS -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white text-center shadow">
                <div class="card-body p-4">
                    <h3 class="mb-0"><?= (int)$studentStats['total_taken'] ?></h3>
                    <p class="mb-0">Quizzes Taken</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white text-center shadow">
                <div class="card-body p-4">
                    <h3 class="mb-0"><?= number_format($studentStats['average_score'],1) ?>%</h3>
                    <p class="mb-0">Average Score</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white text-center shadow">
                <div class="card-body p-4">
                    <h3 class="mb-0"><?= number_format($studentStats['highest_score'],1) ?>%</h3>
                    <p class="mb-0">Highest Score</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white text-center shadow">
                <div class="card-body p-4">
                    <h3 class="mb-0"><?= number_format($studentStats['lowest_score'],1) ?>%</h3>
                    <p class="mb-0">Lowest Score</p>
                </div>
            </div>
        </div>
    </div>

    <!-- AVAILABLE QUIZZES -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0"><i class="fas fa-clipboard-list"></i> Available Quizzes</h2>
        <span class="badge badge-primary">
            <?= max(0, count($visibleQuizzes) - count(array_filter($visibleQuizzes, function($q) use ($completedQuizIds){ return in_array($q['id'],$completedQuizIds); }))) ?> New
        </span>
    </div>

    <?php if (empty($visibleQuizzes)): ?>
        <div class="card shadow-lg text-center py-5">
            <div class="card-body">
                <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                <h3>All Caught Up!</h3>
                <p class="text-muted">You've completed all available quizzes. Great job! 🎉</p>
                <?php if ($isGuest): ?>
                    <a href="<?=APP_URL?>/views/student/register.php" class="btn btn-success btn-lg mt-3"><i class="fas fa-user-plus"></i> Create Account</a>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="quiz-grid mb-5">
            <?php foreach ($visibleQuizzes as $quiz): 
                $alreadyTaken = in_array($quiz['id'], $completedQuizIds);
            ?>
                <div class="quiz-card">
                    <?php if ($alreadyTaken): ?>
                        <div class="completed-badge"><i class="fas fa-check"></i> Completed</div>
                    <?php endif; ?>

                    <div class="quiz-icon">📝</div>

                    <h3 class="quiz-title"><?= htmlspecialchars($quiz['title'] ?? 'Untitled') ?></h3>

                    <div class="quiz-meta">
                        <div class="meta-item"><i class="fas fa-calendar"></i><?= date("M d, Y", strtotime($quiz['date'] ?? 'now')) ?></div>
                        <?php if (!empty($quiz['topic'])): ?>
                            <div class="meta-item"><i class="fas fa-book"></i><?= htmlspecialchars($quiz['topic']) ?></div>
                        <?php endif; ?>
                        <div class="meta-item"><i class="fas fa-question-circle"></i><?= (int)($quiz['question_count'] ?? 0) ?> Questions</div>
                    </div>

                    <p class="text-muted"><?= htmlspecialchars(substr($quiz['instructions'] ?? '', 0, 100)) ?>...</p>

                    <?php if ($alreadyTaken): ?>
                        <a href="<?=APP_URL?>/views/student/quiz-result.php?response=<?= (int)$quiz['id'] ?>" class="btn-start-quiz">
                            <i class="fas fa-eye"></i> View Result
                        </a>
                    <?php else: ?>
                        <a href="<?=APP_URL?>/views/student/take-quiz.php?id=<?= (int)$quiz['id'] ?>" class="btn-start-quiz">
                            <i class="fas fa-play"></i> Start Quiz
                        </a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- COMPLETED QUIZZES -->
    <?php if (!empty($completedQuizzes)): ?>
        <h2 class="mb-3"><i class="fas fa-check-circle text-success"></i> Completed Quizzes <span class="badge badge-success"><?= count($completedQuizzes) ?></span></h2>

        <div class="quiz-grid">
            <?php foreach ($completedQuizzes as $completed):
                // safe fallbacks
                $title = $completed['quiz_title'] ?? ($completed['title'] ?? 'Untitled Quiz');
                $score = $completed['score'] ?? 0;
                $totalQ = $completed['total_questions'] ?? 0;
                $percentage = isset($completed['percentage']) ? (float)$completed['percentage'] : ($totalQ ? ($score / $totalQ * 100) : 0);
            ?>
                <div class="quiz-card" style="opacity:0.95;">
                    <div class="completed-badge"><i class="fas fa-check"></i> Completed</div>
                    <div class="quiz-icon">✅</div>

                    <h3 class="quiz-title"><?= htmlspecialchars($title) ?></h3>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Your Score:</span>
                            <strong><?= (int)$score ?>/<?= (int)$totalQ ?></strong>
                        </div>

                        <div class="progress" style="height:20px;">
                            <?php
                                $perc = round($percentage, 1);
                                $progressColor = 'bg-danger';
                                if ($perc >= 90) $progressColor = 'bg-success';
                                elseif ($perc >= 75) $progressColor = 'bg-info';
                                elseif ($perc >= 60) $progressColor = 'bg-warning';
                            ?>
                            <div class="progress-bar <?= $progressColor ?>" role="progressbar" style="width: <?= max(0, min(100,$perc)) ?>%">
                                <?= number_format($perc,1) ?>%
                            </div>
                        </div>
                    </div>

                    <small class="text-muted">
                        <i class="fas fa-clock"></i>
                        Completed: <?= !empty($completed['submitted_at']) ? date("M d, Y", strtotime($completed['submitted_at'])) : '—' ?>
                    </small>

                    <a href="<?=APP_URL?>/views/student/quiz-result.php?response=<?= (int)($completed['id'] ?? 0) ?>" class="btn-start-quiz mt-3 d-block">
                        <i class="fas fa-eye"></i> View Results
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
