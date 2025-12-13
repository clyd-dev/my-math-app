<?php
// views/admin/dashboard.php - UPDATED VERSION
require_once '../../config/config.php';
require_once '../../models/Admin.php';
require_once '../../models/Quiz.php';

$pageTitle = 'Dashboard';
$adminModel = new Admin();
$stats = $adminModel->getDashboardStats();
$quizModel = new Quiz();
$recentQuizzes = $quizModel->getAll();

include '../../includes/admin-layout.php';
?>

<div class="container-fluid">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="text-white">Welcome back, <?php echo $_SESSION['admin_name']; ?>! 👋</h2>
            <p class="text-white-50">Here's what's happening with your quizzes today.</p>
        </div>
        <div class="col-md-4 text-right">
            <a href="<?php echo APP_URL; ?>/views/admin/create-quiz.php" class="btn btn-success btn-lg">
                <i class="fas fa-plus"></i> Create New Quiz
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white shadow">
                <div class="card-body">
                    <h4 class="mb-2">Total Students</h4>
                    <h2 class="mb-0"><?php echo $stats['total_students']; ?></h2>
                    <p class="mb-0 mt-2"><i class="fas fa-users"></i> Registered</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white shadow">
                <div class="card-body">
                    <h4 class="mb-2">Active Quizzes</h4>
                    <h2 class="mb-0"><?php echo $stats['total_quizzes']; ?></h2>
                    <p class="mb-0 mt-2"><i class="fas fa-check-circle"></i> Available</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white shadow">
                <div class="card-body">
                    <h4 class="mb-2">Today's Responses</h4>
                    <h2 class="mb-0"><?php echo $stats['today_responses']; ?></h2>
                    <p class="mb-0 mt-2"><i class="fas fa-calendar-day"></i> Submitted</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white shadow">
                <div class="card-body">
                    <h4 class="mb-2">Total Responses</h4>
                    <h2 class="mb-0"><?php echo count($stats['latest_responses']); ?></h2>
                    <p class="mb-0 mt-2"><i class="fas fa-chart-line"></i> All Time</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Quizzes -->
        <div class="col-md-8 mb-4">
            <div class="card shadow">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="fas fa-clipboard-list"></i> Recent Quizzes</h4>
                        <a href="<?php echo APP_URL; ?>/views/admin/quizzes.php" class="btn btn-sm btn-primary">View All</a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if(empty($recentQuizzes)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No quizzes yet. Create your first quiz!</p>
                            <a href="<?php echo APP_URL; ?>/views/admin/create-quiz.php" class="btn btn-success">
                                <i class="fas fa-plus"></i> Create Quiz
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Date</th>
                                        <th>Questions</th>
                                        <th>Share Code</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach(array_slice($recentQuizzes, 0, 5) as $quiz): ?>
                                        <tr>
                                            <td><strong><?php echo htmlspecialchars($quiz['title']); ?></strong></td>
                                            <td><?php echo date('M d, Y', strtotime($quiz['date'])); ?></td>
                                            <td><?php echo $quiz['question_count']; ?></td>
                                            <td><span class="badge badge-primary"><?php echo $quiz['share_code']; ?></span></td>
                                            <td>
                                                <a href="<?php echo APP_URL; ?>/views/admin/view-quiz.php?id=<?php echo $quiz['id']; ?>" 
                                                   class="btn btn-sm btn-info" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="<?php echo APP_URL; ?>/views/admin/view-responses.php?id=<?php echo $quiz['id']; ?>" 
                                                   class="btn btn-sm btn-success" title="Responses">
                                                    <i class="fas fa-chart-bar"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Latest Responses -->
        <div class="col-md-4 mb-4">
            <div class="card shadow">
                <div class="card-header bg-white">
                    <h4 class="mb-0"><i class="fas fa-bell"></i> Latest Responses</h4>
                </div>
                <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                    <?php if(empty($stats['latest_responses'])): ?>
                        <p class="text-muted text-center">No responses yet.</p>
                    <?php else: ?>
                        <?php foreach($stats['latest_responses'] as $response): ?>
                            <div class="border-left border-primary pl-3 mb-3 pb-3 border-bottom">
                                <h6 class="mb-1"><?php echo htmlspecialchars($response['name']); ?></h6>
                                <p class="text-muted small mb-1"><?php echo htmlspecialchars($response['title']); ?></p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge badge-success">
                                        <?php echo $response['score']; ?>/<?php echo $response['total_questions']; ?> 
                                        (<?php echo number_format($response['percentage'], 1); ?>%)
                                    </span>
                                    <small class="text-muted"><?php echo date('M d', strtotime($response['submitted_at'])); ?></small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-white">
                    <h4 class="mb-0"><i class="fas fa-bolt"></i> Quick Actions</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="<?php echo APP_URL; ?>/views/admin/create-quiz.php" class="btn btn-primary btn-lg btn-block">
                                <i class="fas fa-plus-circle"></i><br>Create Quiz
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="<?php echo APP_URL; ?>/views/admin/students.php" class="btn btn-success btn-lg btn-block">
                                <i class="fas fa-users"></i><br>Manage Students
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="<?php echo APP_URL; ?>/views/admin/quizzes.php" class="btn btn-info btn-lg btn-block">
                                <i class="fas fa-list"></i><br>View All Quizzes
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="<?php echo APP_URL; ?>/views/student/landing.php" class="btn btn-warning btn-lg btn-block">
                                <i class="fas fa-external-link-alt"></i><br>Student Portal
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/admin-layout-footer.php'; ?>