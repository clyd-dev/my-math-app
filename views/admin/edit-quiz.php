<?php
// views/admin/edit-quiz.php
require_once '../../config/config.php';
require_once '../../controllers/QuizController.php';

// Check admin authentication
if(!isset($_SESSION['admin_id'])) {
    header("Location: " . APP_URL . "/views/admin/login.php");
    exit();
}

$quizController = new QuizController();

// Get quiz ID from URL
$quizId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if(!$quizId) {
    $_SESSION['error_message'] = 'Invalid quiz ID';
    header("Location: " . APP_URL . "/views/admin/quizzes.php");
    exit();
}

// Get quiz data
$quiz = $quizController->getQuiz($quizId);

if(!$quiz) {
    $_SESSION['error_message'] = 'Quiz not found';
    header("Location: " . APP_URL . "/views/admin/quizzes.php");
    exit();
}

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = sanitize($_POST['title']);
    $date = sanitize($_POST['date']);
    $topic = sanitize($_POST['topic']);
    $instructions = sanitize($_POST['instructions']);
    
    $result = $quizController->updateQuiz($quizId, $title, $date, $topic, $instructions);
    
    if($result['success']) {
        $_SESSION['success_message'] = $result['message'];
        header("Location: " . APP_URL . "/views/admin/view-quiz.php?id=" . $quizId);
        exit();
    } else {
        $error = $result['message'];
    }
}

$pageTitle = 'Edit Quiz - ' . $quiz['title'];
include '../../includes/admin-layout.php';
?>

<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="text-white"><i class="fas fa-edit"></i> Edit Quiz</h2>
            <a href="<?php echo APP_URL; ?>/views/admin/view-quiz.php?id=<?php echo $quiz['id']; ?>" 
               class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Quiz
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Edit Form -->
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="card-header bg-warning text-white">
                    <h4 class="mb-0"><i class="fas fa-clipboard-list"></i> Edit Quiz Information</h4>
                </div>
                <div class="card-body">
                    <?php if(isset($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                            <button type="button" class="close" data-dismiss="alert">
                                <span>&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="form-group">
                            <label><i class="fa fa-heading"></i> Quiz Title *</label>
                            <input type="text" 
                                   name="title" 
                                   class="form-control form-control-lg" 
                                   value="<?php echo htmlspecialchars($quiz['title']); ?>" 
                                   required>
                            <small class="form-text text-muted">A clear, descriptive title for the quiz</small>
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-calendar"></i> Date *</label>
                            <input type="date" 
                                   name="date" 
                                   class="form-control form-control-lg" 
                                   value="<?php echo $quiz['date']; ?>" 
                                   required>
                            <small class="form-text text-muted">The date when this quiz is scheduled</small>
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-book"></i> Topic</label>
                            <input type="text" 
                                   name="topic" 
                                   class="form-control form-control-lg" 
                                   value="<?php echo htmlspecialchars($quiz['topic'] ?? ''); ?>" 
                                   placeholder="e.g., Algebra, Polynomials, Geometry">
                            <small class="form-text text-muted">Optional: What topic does this quiz cover?</small>
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-info-circle"></i> Instructions *</label>
                            <textarea name="instructions" 
                                      class="form-control" 
                                      rows="5" 
                                      required><?php echo htmlspecialchars($quiz['instructions']); ?></textarea>
                            <small class="form-text text-muted">Instructions that students will see before taking the quiz</small>
                        </div>
                        
                        <hr>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Share Code</label>
                                    <div class="input-group">
                                        <input type="text" 
                                               class="form-control" 
                                               value="<?php echo $quiz['share_code']; ?>" 
                                               disabled>
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" 
                                                    type="button" 
                                                    onclick="copyShareCode('<?php echo $quiz['share_code']; ?>')">
                                                <i class="fas fa-copy"></i> Copy
                                            </button>
                                        </div>
                                    </div>
                                    <small class="form-text text-muted">Share code cannot be changed</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Status</label>
                                    <input type="text" 
                                           class="form-control" 
                                           value="<?php echo ucfirst($quiz['status']); ?>" 
                                           disabled>
                                    <small class="form-text text-muted">Quiz status</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Created On</label>
                            <input type="text" 
                                   class="form-control" 
                                   value="<?php echo date('F d, Y h:i A', strtotime($quiz['created_at'])); ?>" 
                                   disabled>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-warning btn-lg">
                                <i class="fas fa-save"></i> Update Quiz
                            </button>
                            <a href="<?php echo APP_URL; ?>/views/admin/view-quiz.php?id=<?php echo $quiz['id']; ?>" 
                               class="btn btn-secondary btn-lg">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Quick Info Card -->
        <div class="col-md-4">
            <div class="card shadow-lg mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Quiz Information</h5>
                </div>
                <div class="card-body">
                    <?php
                    $questions = $quizController->getQuestions($quizId);
                    $responses = $quizController->getResponses($quizId);
                    ?>
                    
                    <div class="text-center mb-3">
                        <div style="width: 80px; height: 80px; margin: 0 auto; background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 36px;">
                            📝
                        </div>
                    </div>
                    
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th>Total Questions:</th>
                            <td class="text-right">
                                <span class="badge badge-primary badge-lg">
                                    <?php echo count($questions); ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Total Responses:</th>
                            <td class="text-right">
                                <span class="badge badge-success badge-lg">
                                    <?php echo count($responses); ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Share Code:</th>
                            <td class="text-right">
                                <span class="badge badge-secondary">
                                    <?php echo $quiz['share_code']; ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td class="text-right">
                                <span class="badge badge-<?php echo $quiz['status'] == 'active' ? 'success' : 'warning'; ?>">
                                    <?php echo ucfirst($quiz['status']); ?>
                                </span>
                            </td>
                        </tr>
                    </table>
                    
                    <hr>
                    
                    <div class="alert alert-warning mb-0">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Note:</strong> Editing quiz information will not affect existing responses.
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-bolt"></i> Quick Actions</h5>
                </div>
                <div class="card-body">
                    <a href="<?php echo APP_URL; ?>/views/admin/view-quiz.php?id=<?php echo $quiz['id']; ?>" 
                       class="btn btn-info btn-block mb-2">
                        <i class="fas fa-eye"></i> View Quiz
                    </a>
                    <a href="<?php echo APP_URL; ?>/views/admin/quiz-builder.php?id=<?php echo $quiz['id']; ?>" 
                       class="btn btn-primary btn-block mb-2">
                        <i class="fas fa-plus"></i> Add Questions
                    </a>
                    <a href="<?php echo APP_URL; ?>/views/admin/view-responses.php?id=<?php echo $quiz['id']; ?>" 
                       class="btn btn-success btn-block mb-2">
                        <i class="fas fa-chart-bar"></i> View Responses
                    </a>
                    <button onclick="copyShareLink()" class="btn btn-secondary btn-block mb-2">
                        <i class="fas fa-share"></i> Copy Share Link
                    </button>
                    <button onclick="deleteQuiz(<?php echo $quiz['id']; ?>, '<?php echo addslashes($quiz['title']); ?>')" 
                            class="btn btn-danger btn-block">
                        <i class="fas fa-trash"></i> Delete Quiz
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyShareCode(code) {
    navigator.clipboard.writeText(code).then(function() {
        alert('Share code copied: ' + code);
    });
}

function copyShareLink() {
    var baseUrl = window.location.origin + window.location.pathname.replace('admin/edit-quiz.php', 'student/guest-quiz.php');
    var shareLink = baseUrl + '?code=<?php echo $quiz['share_code']; ?>';
    
    navigator.clipboard.writeText(shareLink).then(function() {
        alert('Share link copied to clipboard!\n\n' + shareLink);
    });
}

function deleteQuiz(id, title) {
    if(confirm('⚠️ WARNING ⚠️\n\nAre you sure you want to delete "' + title + '"?\n\n' +
               'This will permanently delete:\n' +
               '• All questions\n' +
               '• All student responses\n' +
               '• All quiz data\n\n' +
               'This action CANNOT be undone!')) {
        window.location.href = '<?php echo APP_URL; ?>/views/admin/delete-quiz.php?id=' + id;
    }
}
</script>

<?php include '../../includes/admin-layout-footer.php'; ?>