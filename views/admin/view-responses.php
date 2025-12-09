<?php
require_once '../../config/config.php';
require_once '../../models/Quiz.php';

if(!isset($_SESSION['admin_id'])) {
    redirect('views/admin/login.php');
}

$quizId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$quizModel = new Quiz();
$quiz = $quizModel->getById($quizId);
$responses = $quizModel->getResponses($quizId);

if(!$quiz) {
    $_SESSION['error_message'] = 'Quiz not found';
    redirect('quizzes.php');
}

// Calculate statistics
$totalResponses = count($responses);
$averageScore = 0;
$highestScore = 0;
$lowestScore = 100;

if($totalResponses > 0) {
    $totalPercentage = 0;
    foreach($responses as $response) {
        $totalPercentage += $response['percentage'];
        if($response['percentage'] > $highestScore) $highestScore = $response['percentage'];
        if($response['percentage'] < $lowestScore) $lowestScore = $response['percentage'];
    }
    $averageScore = $totalPercentage / $totalResponses;
}

$pageTitle = 'Quiz Responses - ' . $quiz['title'];
$isAdmin = true;
?>
<?php include '../../includes/admin-layout.php'; ?>

<div class="container mt-5 pt-5">
    <!-- Quiz Info & Statistics -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-lg">
                <div class="card-header text-white" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                    <h3 class="mb-0"><i class="fas fa-chart-bar"></i> Responses for: <?php echo htmlspecialchars($quiz['title']); ?></h3>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="stat-box bg-primary text-white p-4 rounded">
                                <h2 class="mb-0"><?php echo $totalResponses; ?></h2>
                                <p class="mb-0">Total Responses</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-box bg-success text-white p-4 rounded">
                                <h2 class="mb-0"><?php echo number_format($averageScore, 1); ?>%</h2>
                                <p class="mb-0">Average Score</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-box bg-warning text-white p-4 rounded">
                                <h2 class="mb-0"><?php echo number_format($highestScore, 1); ?>%</h2>
                                <p class="mb-0">Highest Score</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-box bg-danger text-white p-4 rounded">
                                <h2 class="mb-0"><?php echo $totalResponses > 0 ? number_format($lowestScore, 1) : 0; ?>%</h2>
                                <p class="mb-0">Lowest Score</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Responses Table -->
    <div class="card shadow-lg">
        <div class="card-header bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="fas fa-users"></i> Student Responses</h4>
                <?php if($totalResponses > 0): ?>
                    <button class="btn btn-success btn-sm" onclick="exportToCSV()">
                        <i class="fas fa-file-excel"></i> Export to CSV
                    </button>
                <?php endif; ?>
            </div>
        </div>
        <div class="card-body">
            <?php if(empty($responses)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                    <p class="text-muted">No responses yet. Share the quiz link with your students!</p>
                    <button class="btn btn-primary" onclick="copyShareLink()">
                        <i class="fas fa-share"></i> Copy Share Link
                    </button>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover" id="responsesTable">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Student Name</th>
                                <th>Section</th>
                                <th>Score</th>
                                <th>Percentage</th>
                                <th>Submitted</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($responses as $index => $response): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td><strong><?php echo htmlspecialchars($response['name']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($response['section']); ?></td>
                                    <td>
                                        <span class="badge badge-lg badge-primary">
                                            <?php echo $response['score']; ?>/<?php echo $response['total_questions']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php
                                        $percentage = $response['percentage'];
                                        $badgeClass = 'secondary';
                                        if($percentage >= 90) $badgeClass = 'success';
                                        elseif($percentage >= 75) $badgeClass = 'primary';
                                        elseif($percentage >= 60) $badgeClass = 'warning';
                                        else $badgeClass = 'danger';
                                        ?>
                                        <span class="badge badge-<?php echo $badgeClass; ?>">
                                            <?php echo number_format($percentage, 1); ?>%
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y h:i A', strtotime($response['submitted_at'])); ?></td>
                                    <td>
                                        <a href="view-student-answers.php?response_id=<?php echo $response['id']; ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> View Answers
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

<style>
.stat-box {
    transition: all 0.3s;
}
.stat-box:hover {
    transform: scale(1.05);
    box-shadow: 0 5px 20px rgba(0,0,0,0.2);
}
</style>

<script>
function copyShareLink() {
    var baseUrl = window.location.origin + window.location.pathname.replace('admin/view-responses.php', 'student/guest-quiz.php');
    var shareLink = baseUrl + '?code=<?php echo $quiz['share_code']; ?>';
    
    navigator.clipboard.writeText(shareLink).then(function() {
        alert('Share link copied!\n\n' + shareLink + '\n\nShare this link with your students.');
    });
}

function exportToCSV() {
    var csv = [];
    var rows = document.querySelectorAll('#responsesTable tr');
    
    for (var i = 0; i < rows.length; i++) {
        var row = [], cols = rows[i].querySelectorAll('td, th');
        
        for (var j = 0; j < cols.length - 1; j++) { // Exclude Actions column
            var text = cols[j].innerText.replace(/,/g, ';');
            row.push(text);
        }
        
        csv.push(row.join(','));
    }
    
    downloadCSV(csv.join('\n'), '<?php echo preg_replace('/[^a-zA-Z0-9]/', '_', $quiz['title']); ?>_responses.csv');
}

function downloadCSV(csv, filename) {
    var csvFile = new Blob([csv], {type: 'text/csv'});
    var downloadLink = document.createElement('a');
    downloadLink.download = filename;
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = 'none';
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
}
</script>

<?php include '../../includes/admin-layout-footer.php'; ?>