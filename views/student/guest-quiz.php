
<?php
// ===== FILE: views/student/guest-quiz.php =====
require_once '../../config/config.php';
require_once '../../models/Quiz.php';
require_once '../../models/Student.php';
include '../../includes/header.php';


$shareCode = isset($_GET['code']) ? strtoupper(sanitize($_GET['code'])) : '';
$error = '';

if(empty($shareCode)) {
    $_SESSION['error_message'] = 'No quiz code provided';
    redirect('landing.php');
}

$quizModel = new Quiz();
$quiz = $quizModel->getByShareCode($shareCode);

if(!$quiz) {
    $_SESSION['error_message'] = 'Invalid quiz code';
    redirect('landing.php');
}

// Handle guest information submission
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_guest'])) {
    $name = sanitize($_POST['name']);
    $grade = sanitize($_POST['grade']);
    $section = sanitize($_POST['section']);
    
    if(empty($name) || empty($grade) || empty($section)) {
        $error = 'All fields are required';
    } else {
        $studentModel = new Student();
        $guestId = $studentModel->createGuest($name, $grade, $section);
        
        if($guestId) {
            $_SESSION['student_id'] = $guestId;
            $_SESSION['student_name'] = $name;
            $_SESSION['is_guest'] = true;
            redirect('take-quiz.php?id=' . $quiz['id']);
        } else {
            $error = 'Failed to create guest account';
        }
    }
}

$pageTitle = 'Join Quiz - ' . $quiz['title'];
$isAdmin = false;
include '../../includes/header.php';
?>

<div class="landing-container">
    <div class="container mt-5 pt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-lg">
                    <div class="card-header text-white text-center" style="background: linear-gradient(135deg, #3b82f6, #2563eb);">
                        <h3 class="mb-0"><i class="fas fa-clipboard-list"></i> Join Quiz</h3>
                    </div>
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <div class="quiz-icon mx-auto" style="width: 100px; height: 100px;">
                                📝
                            </div>
                            <h4 class="mt-3"><?php echo htmlspecialchars($quiz['title']); ?></h4>
                            <?php if($quiz['topic']): ?>
                                <p class="text-muted mb-0"><?php echo htmlspecialchars($quiz['topic']); ?></p>
                            <?php endif; ?>
                            <span class="badge badge-primary mt-2">Code: <?php echo $quiz['share_code']; ?></span>
                        </div>

                        <?php if($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Please enter your information to take this quiz
                        </div>

                        <form method="POST">
                            <div class="form-group">
                                <label>Your Name *</label>
                                <input type="text" name="name" class="form-control form-control-lg" required>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label>Grade *</label>
                                        <select name="grade" class="form-control form-control-lg" required>
                                            <option value="">Select</option>
                                            <option>Grade 7</option>
                                            <option>Grade 8</option>
                                            <option>Grade 9</option>
                                            <option>Grade 10</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label>Section *</label>
                                        <input type="text" name="section" class="form-control form-control-lg" required>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" name="create_guest" class="btn btn-gradient-blue btn-block btn-lg">
                                <i class="fas fa-rocket"></i> Start Quiz
                            </button>
                        </form>

                        <hr>

                        <div class="text-center">
                            <p class="mb-2">Already have an account?</p>
                            <a href="login.php" class="btn btn-outline-primary">Login</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>