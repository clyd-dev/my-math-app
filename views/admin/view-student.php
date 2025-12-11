<?php
// views/admin/view-student.php - UPDATED WITH CONTROLLER
require_once '../../config/config.php';
require_once '../../controllers/StudentController.php';

// Initialize controller
$studentController = new StudentController();

// Get student ID
$studentId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Validate student exists
if(!$studentController->studentExists($studentId)) {
    $_SESSION['error_message'] = 'Student not found';
    header("Location: " . APP_URL . "/views/admin/students.php");
    exit();
}

// Get student data
$student = $studentController->getStudent($studentId);
$quizHistory = $studentController->getQuizHistory($studentId);
$stats = $studentController->getStudentStats($studentId);
$rank = $studentController->getStudentRank($studentId);

$pageTitle = 'View Student - ' . $student['name'];
include '../../includes/admin-layout.php';
?>

<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="text-white"><i class="fas fa-user"></i> Student Details</h2>
            <p class="text-white-50">Complete information and quiz history</p>
        </div>
        <div class="col-md-4 text-right">
            <a href="<?php echo APP_URL; ?>/views/admin/students.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Students
            </a>
            <a href="<?php echo APP_URL; ?>/views/admin/edit-student.php?id=<?php echo $student['id']; ?>" 
               class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Student Profile Card -->
        <div class="col-md-4">
            <div class="card shadow-lg mb-4">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0"><i class="fas fa-id-card"></i> Student Profile</h4>
                </div>
                <div class="card-body text-center">
                    <!-- Avatar -->
                    <div style="width: 120px; height: 120px; margin: 0 auto 20px; background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 60px; box-shadow: 0 5px 15px rgba(0,0,0,0.2);">
                        👨‍🎓
                    </div>
                    
                    <h4 class="mb-3"><?php echo htmlspecialchars($student['name']); ?></h4>
                    
                    <!-- Info Table -->
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th class="text-left">Student ID:</th>
                            <td class="text-right">
                                <span class="badge badge-secondary">#<?php echo str_pad($student['id'], 5, '0', STR_PAD_LEFT); ?></span>
                            </td>
                        </tr>
                        <tr>
                            <th class="text-left">Section:</th>
                            <td class="text-right">
                                <span class="badge badge-info"><?php echo htmlspecialchars($student['section']); ?></span>
                            </td>
                        </tr>
                        <tr>
                            <th class="text-left">Account Type:</th>
                            <td class="text-right">
                                <?php if($student['is_guest']): ?>
                                    <span class="badge badge-warning"><i class="fas fa-user"></i> Guest</span>
                                <?php else: ?>
                                    <span class="badge badge-success"><i class="fas fa-user-check"></i> Registered</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="text-left">Registered:</th>
                            <td class="text-right"><?php echo date('M d, Y', strtotime($student['created_at'])); ?></td>
                        </tr>
                    </table>
                    
                    <hr>
                    
                    <!-- Action Buttons -->
                    <a href="<?php echo APP_URL; ?>/views/admin/edit-student.php?id=<?php echo $student['id']; ?>" 
                       class="btn btn-warning btn-block">
                        <i class="fas fa-edit"></i> Edit Information
                    </a>
                    <button onclick="deleteStudent(<?php echo $student['id']; ?>, '<?php echo addslashes($student['name']); ?>')" 
                            class="btn btn-danger btn-block">
                        <i class="fas fa-trash"></i> Delete Student
                    </button>
                </div>
            </div>

            <!-- Ranking Card -->
            <?php if($rank && $stats['total_quizzes'] > 0): ?>
            <div class="card shadow-lg mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-trophy"></i> Student Ranking</h5>
                </div>
                <div class="card-body text-center">
                    <h1 class="display-4 text-success mb-0">
                        #<?php echo $rank['rank']; ?>
                    </h1>
                    <p class="text-muted mb-3">out of <?php echo $rank['total']; ?> students</p>
                    <div class="progress mb-2" style="height: 25px;">
                        <div class="progress-bar bg-success" 
                             style="width: <?php echo $rank['percentile']; ?>%">
                            <?php echo number_format($rank['percentile'], 1); ?>th Percentile
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Statistics and History -->
        <div class="col-md-8">
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-info text-white shadow">
                        <div class="card-body text-center">
                            <h5>Total Quizzes</h5>
                            <h2 class="mb-0"><?php echo $stats['total_quizzes']; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white shadow">
                        <div class="card-body text-center">
                            <h5>Average Score</h5>
                            <h2 class="mb-0"><?php echo $stats['total_quizzes'] > 0 ? number_format($stats['average_score'], 1) : '0'; ?>%</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white shadow">
                        <div class="card-body text-center">
                            <h5>Highest Score</h5>
                            <h2 class="mb-0"><?php echo $stats['total_quizzes'] > 0 ? number_format($stats['highest_score'], 1) : '0'; ?>%</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white shadow">
                        <div class="card-body text-center">
                            <h5>Lowest Score</h5>
                            <h2 class="mb-0"><?php echo $stats['total_quizzes'] > 0 ? number_format($stats['lowest_score'], 1) : '0'; ?>%</h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quiz History -->
            <div class="card shadow-lg">
                <div class="card-header bg-white">
                    <h4 class="mb-0"><i class="fas fa-history"></i> Quiz History</h4>
                </div>
                <div class="card-body">
                    <?php if(empty($quizHistory)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-clipboard-list fa-5x text-muted mb-3"></i>
                            <h5 class="text-muted">No Quiz History</h5>
                            <p class="text-muted">This student hasn't taken any quizzes yet.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Quiz Title</th>
                                        <th>Date Taken</th>
                                        <th>Score</th>
                                        <th>Percentage</th>
                                        <th>Grade</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($quizHistory as $index => $quiz): ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td><strong><?php echo htmlspecialchars($quiz['title']); ?></strong></td>
                                            <td><?php echo date('M d, Y h:i A', strtotime($quiz['submitted_at'])); ?></td>
                                            <td>
                                                <span class="badge badge-lg badge-primary">
                                                    <?php echo $quiz['score']; ?>/<?php echo $quiz['total_questions']; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php
                                                $percentage = $quiz['percentage'];
                                                $badgeClass = 'secondary';
                                                if($percentage >= 90) $badgeClass = 'success';
                                                elseif($percentage >= 75) $badgeClass = 'primary';
                                                elseif($percentage >= 60) $badgeClass = 'warning';
                                                else $badgeClass = 'danger';
                                                ?>
                                                <span class="badge badge-<?php echo $badgeClass; ?> badge-lg">
                                                    <?php echo number_format($percentage, 1); ?>%
                                                </span>
                                            </td>
                                            <td>
                                                <?php
                                                if($percentage >= 90) echo '<span style="font-size: 20px;">⭐⭐⭐⭐⭐</span>';
                                                elseif($percentage >= 75) echo '<span style="font-size: 20px;">⭐⭐⭐⭐</span>';
                                                elseif($percentage >= 60) echo '<span style="font-size: 20px;">⭐⭐⭐</span>';
                                                elseif($percentage >= 50) echo '<span style="font-size: 20px;">⭐⭐</span>';
                                                else echo '<span style="font-size: 20px;">⭐</span>';
                                                ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Performance Chart -->
                        <div class="mt-4">
                            <h5><i class="fas fa-chart-line"></i> Performance Trend</h5>
                            <canvas id="performanceChart" height="80"></canvas>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

<script>
function deleteStudent(id, name) {
    if(confirm('⚠️ WARNING ⚠️\n\nAre you sure you want to delete ' + name + '?\n\n' +
               'This will permanently delete:\n' +
               '• All quiz responses\n' +
               '• All quiz history\n' +
               '• All student data\n\n' +
               'This action CANNOT be undone!')) {
        window.location.href = '<?php echo APP_URL; ?>/views/admin/delete-student.php?id=' + id;
    }
}

// Performance Chart
<?php if(!empty($quizHistory)): ?>
var ctx = document.getElementById('performanceChart').getContext('2d');
var chart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: [
            <?php 
            foreach($quizHistory as $quiz) {
                echo "'" . date('M d', strtotime($quiz['submitted_at'])) . "',";
            }
            ?>
        ],
        datasets: [{
            label: 'Score Percentage',
            data: [
                <?php 
                foreach($quizHistory as $quiz) {
                    echo $quiz['percentage'] . ",";
                }
                ?>
            ],
            borderColor: '#667eea',
            backgroundColor: 'rgba(102, 126, 234, 0.1)',
            borderWidth: 3,
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: true,
                position: 'top'
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                max: 100,
                ticks: {
                    callback: function(value) {
                        return value + '%';
                    }
                }
            }
        }
    }
});
<?php endif; ?>
</script>

<?php include '../../includes/admin-layout-footer.php'; ?>