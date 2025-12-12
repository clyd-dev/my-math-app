<?php
// views/student/guest-quiz.php - UPDATED WITH CONTROLLER
require_once '../../config/config.php';
require_once '../../controllers/StudentController.php';
require_once '../../models/Quiz.php';

$studentController = new StudentController();
$quizModel = new Quiz();

$shareCode = isset($_GET['code']) ? strtoupper(sanitize($_GET['code'])) : '';
if (strpos($shareCode, '?') !== false) {
    // extract only last value after last "="
    $parts = explode('=', $shareCode);
    $shareCode = end($parts);
}

$shareCode = strtoupper(trim($shareCode));
$error = '';

if(empty($shareCode)) {
    $_SESSION['error_message'] = 'No quiz code provided';
    header("Location: " . APP_URL . "/views/student/landing.php");
    exit();
}

$quiz = $quizModel->getByShareCode($shareCode);

if(!$quiz) {
    $_SESSION['error_message'] = 'Invalid quiz code: ' . $shareCode;
    header("Location: " . APP_URL . "/views/student/landing.php");
    exit();
}

// Handle guest information submission
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_guest'])) {
    $name = sanitize($_POST['name']);
    $section = sanitize($_POST['section']);
    
    $result = $studentController->createGuest($name, $section);
    
    if($result['success']) {
        // Session is automatically set by controller
        header("Location: " . APP_URL . "/views/student/take-quiz.php?id=" . $quiz['id']);
        exit();
    } else {
        $error = $result['message'];
    }
}

$pageTitle = 'Join Quiz - ' . $quiz['title'];
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
</head>
<body>

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
                            <div class="quiz-icon mx-auto" style="width: 100px; height: 100px; background: linear-gradient(135deg, #dbeafe, #bfdbfe); border-radius: 20px; display: flex; align-items: center; justify-content: center; font-size: 50px;">
                                📝
                            </div>
                            <h4 class="mt-3"><?php echo htmlspecialchars($quiz['title']); ?></h4>
                            <?php if($quiz['topic']): ?>
                                <p class="text-muted mb-2">
                                    <i class="fas fa-book"></i> <?php echo htmlspecialchars($quiz['topic']); ?>
                                </p>
                            <?php endif; ?>
                            <p class="text-muted mb-2">
                                <i class="fas fa-calendar"></i> <?php echo date('F d, Y', strtotime($quiz['date'])); ?>
                            </p>
                            <span class="badge badge-primary badge-lg mt-2">
                                <i class="fas fa-link"></i> Code: <?php echo $quiz['share_code']; ?>
                            </span>
                        </div>

                        <?php if($error): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                                <button type="button" class="close" data-dismiss="alert">
                                    <span>&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Please enter your information to take this quiz as a guest.
                        </div>

                        <form method="POST">
                            <div class="form-group">
                                <label><i class="fas fa-user"></i> Your Name *</label>
                                <input type="text" name="name" class="form-control form-control-lg" 
                                       placeholder="Enter your full name" required minlength="2" autofocus>
                            </div>
                            
                            <div class="form-group">
                                <label><i class="fas fa-users"></i> Section *</label>
                                <select name="section" class="form-control form-control-lg" required>
                                    <option value="">Select Section</option>
                                    <option value="Diamond">Diamond</option>
                                    <option value="Ruby">Ruby</option>
                                    <option value="Jade">Jade</option>
                                    <option value="Garnet">Garnet</option>
                                    <option value="Emerald">Emerald</option>
                                    <option value="Topaz">Topaz</option>
                                    <option value="Sapphire">Sapphire</option>
                                    <option value="Pearl">Pearl</option>
                                </select>
                            </div>
                            
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i> 
                                <strong>Note:</strong> Guest accounts don't save your progress permanently. 
                                <a href="<?php echo APP_URL; ?>/views/student/register.php" class="alert-link">
                                    Create a free account
                                </a> to track your progress!
                            </div>
                            
                            <button type="submit" name="create_guest" class="btn btn-gradient-blue btn-block btn-lg">
                                <i class="fas fa-rocket"></i> Start Quiz
                            </button>
                        </form>

                        <hr>

                        <div class="text-center">
                            <p class="mb-2">Already have an account?</p>
                            <a href="<?php echo APP_URL; ?>/views/student/login.php" class="btn btn-outline-primary">
                                <i class="fas fa-sign-in-alt"></i> Login
                            </a>
                            <a href="<?php echo APP_URL; ?>/views/student/landing.php" class="btn btn-outline-secondary ml-2">
                                <i class="fas fa-home"></i> Home
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Quiz Preview Card -->
                <div class="card shadow-lg mt-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-info-circle"></i> Quiz Information</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Instructions:</strong></p>
                        <p class="text-muted"><?php echo nl2br(htmlspecialchars($quiz['instructions'])); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>
<script src="../../assets/js/student-script.js"></script>
</body>
</html>