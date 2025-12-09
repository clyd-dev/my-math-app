<?php
require_once '../../config/config.php';
require_once '../../models/Quiz.php';

if(!isset($_SESSION['admin_id'])) {
    redirect('views/admin/login.php');
}

$quizModel = new Quiz();
$quizzes = $quizModel->getAll();

$pageTitle = 'Manage Quizzes';
$isAdmin = true;
?>
<?php include '../../includes/header.php'; ?>

<div class="container-scroller">
    <!-- Navbar -->
    <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row" style="background: linear-gradient(135deg, #667eea, #764ba2);">
        <div class="navbar-brand-wrapper d-flex justify-content-center">
            <a class="navbar-brand text-white" href="dashboard.php">
                <i class="fas fa-graduation-cap"></i> Math Quiz Admin
            </a>
        </div>
        <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
            <ul class="navbar-nav navbar-nav-right">
                <li class="nav-item">
                    <span class="text-white mr-3">Welcome, <?php echo $_SESSION['admin_name']; ?></span>
                    <a href="logout.php" class="btn btn-sm btn-light">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container-fluid page-body-wrapper" style="margin-top: 70px;">
        <div class="container mt-4">
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="text-white"><i class="fas fa-clipboard-list"></i> All Quizzes</h2>
                            <p class="text-white-50">Manage your math quizzes</p>
                        </div>
                        <div>
                            <a href="dashboard.php" class="btn btn-light mr-2">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                            <a href="create-quiz.php" class="btn btn-success">
                                <i class="fas fa-plus"></i> Create New Quiz
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quiz Cards -->
            <div class="row">
                <?php if(empty($quizzes)): ?>
                    <div class="col-12">
                        <div class="card shadow-lg text-center py-5">
                            <div class="card-body">
                                <i class="fas fa-clipboard-list fa-5x text-muted mb-4"></i>
                                <h3 class="text-muted">No Quizzes Yet</h3>
                                <p class="text-muted mb-4">Create your first quiz to get started!</p>
                                <a href="create-quiz.php" class="btn btn-success btn-lg">
                                    <i class="fas fa-plus"></i> Create Quiz
                                </a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach($quizzes as $quiz): ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card shadow-lg h-100 quiz-card-admin">
                                <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                                    <h5 class="mb-0">
                                        <i class="fas fa-file-alt"></i>
                                        <?php echo htmlspecialchars($quiz['title']); ?>
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <small class="text-muted">
                                            <i class="fas fa-calendar"></i>
                                            <?php echo date('F d, Y', strtotime($quiz['date'])); ?>
                                        </small>
                                    </div>
                                    
                                    <?php if($quiz['topic']): ?>
                                        <p class="mb-2">
                                            <strong>Topic:</strong> <?php echo htmlspecialchars($quiz['topic']); ?>
                                        </p>
                                    <?php endif; ?>
                                    
                                    <p class="mb-3">
                                        <strong>Questions:</strong> <?php echo $quiz['question_count']; ?>
                                    </p>
                                    
                                    <div class="mb-3">
                                        <span class="badge badge-primary">Share Code: <?php echo $quiz['share_code']; ?></span>
                                        <button class="btn btn-sm btn-outline-primary ml-2" onclick="copyShareLink('<?php echo $quiz['share_code']; ?>')">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                    
                                    <hr>
                                    
                                    <div class="d-flex justify-content-between">
                                        <a href="view-quiz.php?id=<?php echo $quiz['id']; ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <a href="quiz-builder.php?id=<?php echo $quiz['id']; ?>" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="view-responses.php?id=<?php echo $quiz['id']; ?>" class="btn btn-sm btn-success">
                                            <i class="fas fa-chart-bar"></i> Responses
                                        </a>
                                        <button class="btn btn-sm btn-danger" onclick="deleteQuiz(<?php echo $quiz['id']; ?>, '<?php echo addslashes($quiz['title']); ?>')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.quiz-card-admin {
    transition: all 0.3s;
}
.quiz-card-admin:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.2);
}
</style>

<script>
function copyShareLink(shareCode) {
    var baseUrl = window.location.origin + window.location.pathname.replace('admin/quizzes.php', 'student/guest-quiz.php');
    var shareLink = baseUrl + '?code=' + shareCode;
    
    navigator.clipboard.writeText(shareLink).then(function() {
        alert('Share link copied to clipboard!\n\n' + shareLink);
    });
}

function deleteQuiz(id, title) {
    if(confirm('Are you sure you want to delete "' + title + '"?\n\nThis will delete all questions and responses permanently.')) {
        window.location.href = 'delete-quiz.php?id=' + id;
    }
}
</script>

<?php include '../../includes/footer.php'; ?>