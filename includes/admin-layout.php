<?php
// includes/admin-layout.php - Consistent Admin Layout
if(!isset($_SESSION['admin_id'])) {
    header("Location: " . APP_URL . "/views/admin/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .navbar {
            background: linear-gradient(135deg, #667eea, #764ba2);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .sidebar {
            background: #2c3e50;
            min-height: calc(100vh - 70px);
            position: fixed;
            top: 70px;
            left: 0;
            width: 260px;
            padding-top: 20px;
            z-index: 100;
        }
        .sidebar .nav-link {
            color: #ecf0f1;
            padding: 15px 25px;
            border-radius: 8px;
            margin: 5px 15px;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover {
            background: rgba(255,255,255,0.1);
            padding-left: 30px;
        }
        .sidebar .nav-link.active {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }
        .main-content {
            margin-left: 260px;
            margin-top: 70px;
            padding: 30px;
            min-height: calc(100vh - 70px);
        }
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?php echo APP_URL; ?>/views/admin/dashboard.php">
                <i class="fas fa-graduation-cap"></i> Math Quiz Admin
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdown" role="button" data-toggle="dropdown">
                            <i class="fas fa-user-circle"></i> <?php echo $_SESSION['admin_name']; ?>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="<?php echo APP_URL; ?>/views/admin/logout.php">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <nav class="sidebar">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" 
                   href="<?php echo APP_URL; ?>/views/admin/dashboard.php">
                    <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'sections.php' ? 'active' : ''; ?>" 
                href="<?php echo APP_URL; ?>/views/admin/sections.php">
                    <i class="fas fa-layer-group mr-2"></i> Sections
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'students.php' ? 'active' : ''; ?>" 
                   href="<?php echo APP_URL; ?>/views/admin/students.php">
                    <i class="fas fa-users mr-2"></i> Students
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'quizzes.php' ? 'active' : ''; ?>" 
                   href="<?php echo APP_URL; ?>/views/admin/quizzes.php">
                    <i class="fas fa-clipboard-list mr-2"></i> Quizzes
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'create-quiz.php' ? 'active' : ''; ?>" 
                   href="<?php echo APP_URL; ?>/views/admin/create-quiz.php">
                    <i class="fas fa-plus-circle mr-2"></i> Create Quiz
                </a>
            </li>
        </ul>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <?php
        // Display flash messages
        if (isset($_SESSION['success_message'])) {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
            echo $_SESSION['success_message'];
            echo '<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>';
            echo '</div>';
            unset($_SESSION['success_message']);
        }
        
        if (isset($_SESSION['error_message'])) {
            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
            echo $_SESSION['error_message'];
            echo '<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>';
            echo '</div>';
            unset($_SESSION['error_message']);
        }
        ?>
        
        <!-- Page Content Goes Here -->