<?php
require_once '../../config/config.php';
require_once '../../models/Admin.php';
require_once '../../models/Student.php';
require_once '../../models/Quiz.php';
include '../../includes/header.php';

// Check if admin is logged in
if(!isset($_SESSION['admin_id'])) {
    redirect('views/admin/login.php');
}

$adminModel = new Admin();
$stats = $adminModel->getDashboardStats();
$quizModel = new Quiz();
$recentQuizzes = $quizModel->getAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Admin Dashboard - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/css/admin-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    
    <div class="container-scroller">
        <!-- Navbar -->
        <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
            <div class="navbar-brand-wrapper d-flex justify-content-center">
                <a class="navbar-brand brand-logo" href="dashboard.php">
                    <h3 class="text-white m-0">Math Quiz</h3>
                </a>
            </div>
            <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
                <ul class="navbar-nav navbar-nav-right">
                    <li class="nav-item nav-profile dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">
                            <span class="text-white"><?php echo $_SESSION['admin_name']; ?></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="logout.php">
                                <i class="fas fa-power-off text-primary"></i> Logout
                            </a>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>

        <div class="container-fluid page-body-wrapper">
            <!-- Sidebar -->
            <nav class="sidebar sidebar-offcanvas" id="sidebar">
                <ul class="nav">
                    <li class="nav-item active">
                        <a class="nav-link" href="dashboard.php">
                            <i class="fas fa-tachometer-alt menu-icon"></i>
                            <span class="menu-title">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="students.php">
                            <i class="fas fa-users menu-icon"></i>
                            <span class="menu-title">Students</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="quizzes.php">
                            <i class="fas fa-clipboard-list menu-icon"></i>
                            <span class="menu-title">Quizzes</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="create-quiz.php">
                            <i class="fas fa-plus-circle menu-icon"></i>
                            <span class="menu-title">Create Quiz</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Main Panel -->
            <div class="main-panel">
                <div class="content-wrapper">
                    <!-- Welcome Section -->
                    <div class="row">
                        <div class="col-md-12 grid-margin">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="font-weight-bold">Welcome back, <?php echo $_SESSION['admin_name']; ?>!</h3>
                                    <h6 class="font-weight-normal mb-0">Here's what's happening with your quizzes today.</h6>
                                </div>
                                <div>
                                    <button class="btn btn-primary btn-icon-text" onclick="location.href='create-quiz.php'">
                                        <i class="fas fa-plus mr-2"></i>Create New Quiz
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Stats Cards -->
                    <div class="row">
                        <div class="col-md-3 grid-margin stretch-card">
                            <div class="card bg-gradient-primary text-white">
                                <div class="card-body">
                                    <h4 class="card-title mb-4">Total Students</h4>
                                    <h2 class="mb-0"><?php echo $stats['total_students']; ?></h2>
                                    <p class="mb-0 mt-2">
                                        <i class="fas fa-users mr-1"></i>Registered
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 grid-margin stretch-card">
                            <div class="card bg-gradient-success text-white">
                                <div class="card-body">
                                    <h4 class="card-title mb-4">Active Quizzes</h4>
                                    <h2 class="mb-0"><?php echo $stats['total_quizzes']; ?></h2>
                                    <p class="mb-0 mt-2">
                                        <i class="fas fa-check-circle mr-1"></i>Available
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 grid-margin stretch-card">
                            <div class="card bg-gradient-warning text-white">
                                <div class="card-body">
                                    <h4 class="card-title mb-4">Today's Responses</h4>
                                    <h2 class="mb-0"><?php echo $stats['today_responses']; ?></h2>
                                    <p class="mb-0 mt-2">
                                        <i class="fas fa-calendar-day mr-1"></i>Submitted
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 grid-margin stretch-card">
                            <div class="card bg-gradient-info text-white">
                                <div class="card-body">
                                    <h4 class="card-title mb-4">Total Responses</h4>
                                    <h2 class="mb-0"><?php echo count($stats['latest_responses']); ?></h2>
                                    <p class="mb-0 mt-2">
                                        <i class="fas fa-chart-line mr-1"></i>All Time
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Recent Quizzes -->
                        <div class="col-md-8 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h4 class="card-title mb-0">Recent Quizzes</h4>
                                        <a href="quizzes.php" class="text-primary">View All</a>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Title</th>
                                                    <th>Date</th>
                                                    <th>Topic</th>
                                                    <th>Share Code</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if(empty($recentQuizzes)): ?>
                                                    <tr>
                                                        <td colspan="5" class="text-center">No quizzes yet. Create your first quiz!</td>
                                                    </tr>
                                                <?php else: ?>
                                                    <?php foreach(array_slice($recentQuizzes, 0, 5) as $quiz): ?>
                                                        <tr>
                                                            <td><?php echo htmlspecialchars($quiz['title']); ?></td>
                                                            <td><?php echo date('M d, Y', strtotime($quiz['date'])); ?></td>
                                                            <td><?php echo htmlspecialchars($quiz['topic'] ?? 'N/A'); ?></td>
                                                            <td>
                                                                <span class="badge badge-primary"><?php echo $quiz['share_code']; ?></span>
                                                            </td>
                                                            <td>
                                                                <a href="view-quiz.php?id=<?php echo $quiz['id']; ?>" class="btn btn-sm btn-info">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                                <a href="view-responses.php?id=<?php echo $quiz['id']; ?>" class="btn btn-sm btn-success">
                                                                    <i class="fas fa-chart-bar"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Latest Responses -->
                        <div class="col-md-4 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title mb-3">Latest Responses</h4>
                                    <div class="list-wrapper">
                                        <?php if(empty($stats['latest_responses'])): ?>
                                            <p class="text-muted">No responses yet.</p>
                                        <?php else: ?>
                                            <?php foreach($stats['latest_responses'] as $response): ?>
                                                <div class="response-item mb-3 p-3 border rounded">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <div>
                                                            <h6 class="mb-1"><?php echo htmlspecialchars($response['name']); ?></h6>
                                                            <p class="text-muted small mb-1"><?php echo htmlspecialchars($response['title']); ?></p>
                                                            <p class="mb-0">
                                                                <span class="badge badge-success">
                                                                    <?php echo $response['score']; ?>/<?php echo $response['total_questions']; ?>
                                                                </span>
                                                                <span class="text-muted small ml-2">
                                                                    <?php echo number_format($response['percentage'], 1); ?>%
                                                                </span>
                                                            </p>
                                                        </div>
                                                        <small class="text-muted">
                                                            <?php echo date('M d', strtotime($response['submitted_at'])); ?>
                                                        </small>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title mb-4">Quick Actions</h4>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <a href="create-quiz.php" class="btn btn-lg btn-block btn-primary">
                                                <i class="fas fa-plus-circle"></i> Create Quiz
                                            </a>
                                        </div>
                                        <div class="col-md-3">
                                            <a href="students.php" class="btn btn-lg btn-block btn-success">
                                                <i class="fas fa-user-plus"></i> Add Student
                                            </a>
                                        </div>
                                        <div class="col-md-3">
                                            <a href="quizzes.php" class="btn btn-lg btn-block btn-info">
                                                <i class="fas fa-list"></i> View All Quizzes
                                            </a>
                                        </div>
                                        <div class="col-md-3">
                                            <a href="students.php" class="btn btn-lg btn-block btn-warning">
                                                <i class="fas fa-users"></i> Manage Students
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <footer class="footer">
                    <div class="d-sm-flex justify-content-center justify-content-sm-between">
                        <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">
                            Copyright © <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. All rights reserved.
                        </span>
                    </div>
                </footer>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.0/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/admin-script.js"></script>
    
    <?php include '../../includes/footer.php'; ?>
</body>
</html>