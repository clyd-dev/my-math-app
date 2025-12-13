<?php
// ===== FILE: student/take-quiz.php =====
require_once '../../config/config.php';
require_once '../../models/Quiz.php';
include '../../includes/header.php';

$quizId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$quiz = new Quiz();

// Add this after $quizData is loaded, before showing the quiz

$studentSection = $_SESSION['student_section'] ?? null;
$quizSection = trim($quizData['section'] ?? '');

if (!empty($quizSection) && $quizSection !== trim($studentSection)) {
    $_SESSION['error_message'] = "Sorry, this quiz is only for section: <strong>" . htmlspecialchars($quizSection) . "</strong>";
    header("Location: dashboard.php");
    exit();
}

$quizData = $quiz->getById($quizId);
$questions = $quiz->getQuestions($quizId);

if(!$quizData) {
    header("Location: " . APP_URL . "/views/student/dashboard.php");
    exit();
}

// Check if already answered
if(isset($_SESSION['student_id'])) {
    if($quiz->hasStudentAnswered($quizId, $_SESSION['student_id'])) {
        redirect('views/student/quiz-result.php?id=' . $quizId);
    }
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // $_POST['answers'] will be provided by JS-injected hidden inputs
    $answers = isset($_POST['answers']) ? $_POST['answers'] : [];
    $studentId = isset($_SESSION['student_id']) ? $_SESSION['student_id'] : null;
    
    $responseId = $quiz->submitResponse($quizId, $studentId, $answers);
    
    if($responseId) {
        redirect('views/student/quiz-result.php?response=' . $responseId);
    } else {
        // fallback: redisplay with an error - for simplicity redirect back
        set_flash_message('danger', 'There was a problem submitting your response. Please try again.');
        redirect('views/student/take-quiz.php?id=' . $quizId);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo htmlspecialchars($quizData['title']); ?></title>

    <!-- Bootstrap CSS (local) -->
    <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">

    <!-- Tailwind CDN for some utility classes (optional, safe to mix) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        /* Keep a few custom styles, avoid conflicting with Bootstrap */
        :root {
        --primary: #4f46e5; /* deeper academic purple */
        --secondary: #6366f1;
        --success: #16a34a;
        --danger: #dc2626;
        --warning: #f59e0b;
        --info: #3b82f6;
        --light: #f9fafb;
        --dark: #111827;

        --card-bg: #ffffff;
        --card-shadow: rgba(0, 0, 0, 0.08);
        --border-soft: #e5e7eb;
        --radius-lg: 20px;
        --transition-fast: 0.25s ease;
    }

    /* Reset */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    /* Background */
    body {
        font-family: "Inter", "Segoe UI", sans-serif;
        background: #f3f4f6;
        color: var(--dark);
        min-height: 100vh;
        line-height: 1.6;
    }

    /* =============================
    REFINED LANDING PAGE
    ============================= */

    .landing-container {
        min-height: 100vh;
        padding: 30px 20px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    /* Remove heavy animations and over-floating shapes */
    .math-symbols {
        display: none;
    }

    /* Teacher Section */
    .teacher-section {
        background: var(--card-bg);
        border-radius: var(--radius-lg);
        padding: 35px;
        box-shadow: 0 8px 25px var(--card-shadow);
        animation: fadeIn 0.6s ease;
    }

    .teacher-avatar {
        width: 140px;
        height: 140px;
        border-radius: 50%;
        background: #eef2ff;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary);
        font-size: 60px;
        margin: 0 auto 20px;
    }

    .teacher-message {
        background: #eef2ff;
        padding: 20px;
        border-radius: var(--radius-lg);
        text-align: center;
    }

    /* =============================
    ACTION CARDS (Login, Register)
    ============================= */

    .action-card {
        background: var(--card-bg);
        border-radius: var(--radius-lg);
        padding: 28px;
        cursor: pointer;
        transition: var(--transition-fast);
        border: 1px solid var(--border-soft);
        box-shadow: 0 4px 12px var(--card-shadow);
    }

    .action-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px var(--card-shadow);
    }

    .action-icon {
        width: 55px;
        height: 55px;
        border-radius: 14px;
        background: var(--primary);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 18px;
        font-size: 26px;
    }

    /* =============================
    FORMS
    ============================= */

    .form-control {
        border-radius: 10px;
        border: 1.8px solid #d1d5db;
        padding: 14px;
        font-size: 1rem;
        transition: var(--transition-fast);
    }

    .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(99,102,241,0.15);
    }

    /* Modern Academic Buttons */
    .btn-gradient-purple,
    .btn-gradient-green,
    .btn-gradient-blue {
        padding: 14px 26px;
        border-radius: 10px;
        font-weight: 600;
        border: none;
        transition: var(--transition-fast);
    }

    .btn-gradient-purple {
        background: var(--primary);
        color: white;
    }

    .btn-gradient-purple:hover {
        background: var(--secondary);
        transform: translateY(-2px);
    }

    /* =============================
    DASHBOARD
    ============================= */

    .student-dashboard {
        padding: 24px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .dashboard-header {
        background: var(--card-bg);
        padding: 35px;
        border-radius: var(--radius-lg);
        box-shadow: 0 5px 20px var(--card-shadow);
        margin-bottom: 30px;
    }

    .welcome-text {
        font-size: 28px;
        font-weight: 700;
    }

    .dashboard-subtitle {
        font-size: 17px;
        color: #6b7280;
    }

    /* =============================
    QUIZ CARDS
    ============================= */

    .quiz-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(290px, 1fr));
        gap: 25px;
    }

    .quiz-card {
        background: white;
        border-radius: var(--radius-lg);
        padding: 25px;
        border: 1px solid var(--border-soft);
        box-shadow: 0 5px 15px var(--card-shadow);
        transition: var(--transition-fast);
    }

    .quiz-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 28px rgba(0,0,0,0.15);
        border-color: var(--primary);
    }

    .quiz-icon {
        width: 60px;
        height: 60px;
        background: var(--primary);
        border-radius: 16px;
        color: white;
        display: flex;
        justify-content: center;
        align-items: center;
        margin-bottom: 18px;
        font-size: 30px;
    }

    .quiz-title {
        font-size: 20px;
        font-weight: 600;
    }

    .quiz-description {
        color: #6b7280;
        font-size: 0.95rem;
    }

    .btn-start-quiz {
        background: var(--primary);
        color: white;
        padding: 12px 18px;
        border-radius: 12px;
        margin-top: 12px;
        font-weight: 600;
        display: block;
        text-align: center;
    }

    .btn-start-quiz:hover {
        background: var(--secondary);
    }

    /* =============================
    QUIZ TAKING INTERFACE
    ============================= */

    .quiz-container {
        max-width: 850px;
        margin: 0 auto;
        padding: 20px;
    }

    .question-card {
        background: var(--card-bg);
        padding: 35px;
        border-radius: var(--radius-lg);
        box-shadow: 0 5px 15px var(--card-shadow);
    }

    .question-text {
        font-size: 22px;
        font-weight: 600;
    }

    /* Choices */
    .choice-btn {
        padding: 18px 22px;
        background: white;
        border: 2px solid var(--border-soft);
        border-radius: 14px;
        font-size: 17px;
        font-weight: 500;
        transition: var(--transition-fast);
    }

    .choice-btn:hover {
        border-color: var(--primary);
        background: #f9fafb;
        transform: translateX(6px);
    }

    .choice-btn.selected {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }

    /* Navigation */
    .quiz-navigation {
        margin-top: 25px;
        display: flex;
        justify-content: space-between;
        gap: 15px;
    }

    /* =============================
    RESULTS PAGE
    ============================= */

    .results-header {
        background: var(--card-bg);
        padding: 50px;
        border-radius: 30px;
        box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        text-align: center;
    }

    .score-number-large {
        font-size: 64px;
        font-weight: 800;
        color: var(--primary);
    }

    /* Review section */
    .review-section {
        background: var(--card-bg);
        padding: 35px;
        border-radius: var(--radius-lg);
        box-shadow: 0 5px 20px var(--card-shadow);
    }

    /* Correct / Incorrect */
    .review-item.correct {
        background: #ecfdf5;
        border-left: 5px solid var(--success);
    }

    .review-item.incorrect {
        background: #fef2f2;
        border-left: 5px solid var(--danger);
    }

    /* =============================
    RESPONSIVENESS
    ============================= */

    @media (max-width: 768px) {
        .quiz-navigation {
            flex-direction: column;
        }
        .score-number-large {
            font-size: 48px;
        }
        .question-text {
            font-size: 19px;
        }
    }
    </style>
</head>
<body>
    <div class="quiz-container">

        <div class="card mb-4 shadow-lg">
            <div class="card-body text-center">
                <h2 class="mb-1"><?php echo htmlspecialchars($quizData['title']); ?></h2>
                <p class="lead mb-1"><?php echo htmlspecialchars($quizData['instructions']); ?></p>
                <p class="mb-0"><strong>Total Questions:</strong> <?php echo count($questions); ?></p>
            </div>
        </div>

        <!-- ===== Hybrid Form =====
             - This <form> will be posted to this same file.
             - JS will inject hidden inputs named answers[QUESTION_ID] before submit.
             - If JS is disabled, fallback radios (provided below inside .no-js) will submit naturally.
         -->
        <form method="POST" id="quizForm" onsubmit="return handleFinalSubmit(event);">
            <!-- JS-driven single-question UI -->
            <div id="jsQuizRoot" class="js-only">

                <!-- Progress + card container -->
                <div id="questionView"></div>

                <!-- Pagination & Submit area -->
                <div class="d-flex justify-content-between align-items-center mt-3 mb-4">
                    <div class="text-muted" id="questionCounter"></div>

                    <div class="btn-group" role="group" aria-label="Navigation">
                        <button type="button" id="prevBtn" class="btn btn-outline-light" onclick="previousQuestion()" disabled>← Previous</button>
                        <button type="button" id="nextBtn" class="btn btn-primary" onclick="nextQuestion()">Next →</button>
                    </div>

                    <div>
                        <button type="button" id="submitBtn" class="btn btn-success" onclick="submitFromJS()" style="display:none;">
                            🏆 Submit Quiz
                        </button>
                    </div>
                </div>
            </div>

            <!-- ===== Non-JS fallback: show all questions with native radios ===== -->
            <noscript>
                <div class="no-js">
                    <?php foreach($questions as $index => $q): ?>
                        <div class="question-card">
                            <h4 class="mb-4">Question <?php echo $index + 1; ?>: <?php echo htmlspecialchars($q['question_text']); ?></h4>
                            <div class="choices">
                                <?php foreach(['A' => $q['choice_a'], 'B' => $q['choice_b'], 'C' => $q['choice_c'], 'D' => $q['choice_d']] as $key => $value): ?>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" 
                                               name="answers[<?php echo $q['id']; ?>]" 
                                               id="noscript_<?php echo $q['id'].'_'.$key; ?>" 
                                               value="<?php echo $key; ?>">
                                        <label class="form-check-label" for="noscript_<?php echo $q['id'].'_'.$key; ?>">
                                            <strong><?php echo $key; ?>.</strong> <?php echo htmlspecialchars($value); ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <div class="text-center">
                        <button type="submit" class="btn btn-success btn-lg" onclick="return confirm('Are you sure you want to submit?')">
                            🏆 Submit Quiz
                        </button>
                    </div>
                </div>
            </noscript>

            <!-- Hidden inputs area: JS will populate this before submission -->
            <div id="hiddenAnswersContainer" style="display:none;"></div>
        </form>
    </div>

    <!-- Pass PHP questions to JS safely -->
    <script>
        // Add a class to body to indicate JS is enabled (shows js-only UI)
        document.documentElement.classList.add('js-enabled');
        document.body.classList.add('js-enabled');

        // Build a JS-friendly quiz object from PHP $questions
        const quiz = {
            id: <?php echo json_encode($quizId); ?>,
            title: <?php echo json_encode($quizData['title']); ?>,
            instructions: <?php echo json_encode($quizData['instructions']); ?>,
            questions: <?php
                // Convert PHP $questions to a lightweight structure
                $jsQuestions = [];
                foreach($questions as $q) {
                    $jsQuestions[] = [
                        'id' => (int)$q['id'],
                        'text' => $q['question_text'],
                        'choices' => [
                            'A' => $q['choice_a'],
                            'B' => $q['choice_b'],
                            'C' => $q['choice_c'],
                            'D' => $q['choice_d'],
                        ],
                        // don't include correct answer in client-side for security
                    ];
                }
                echo json_encode($jsQuestions);
            ?>,
        };
    </script>

    <script>
    // ===== Client-side state =====
    let currentIndex = 0;
    const total = quiz.questions.length;
    const answers = {}; // keyed by questionId => choice (e.g. { 3: 'B' })

    // Elements
    const questionView = document.getElementById('questionView');
    const questionCounter = document.getElementById('questionCounter');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');
    const hiddenAnswersContainer = document.getElementById('hiddenAnswersContainer');

    // Render the current question card
    function renderQuestion() {
        const q = quiz.questions[currentIndex];
        const selected = answers[q.id] || null;
        const progressPct = Math.round(((currentIndex + 1) / total) * 100);

        // Build choices markup
        const choicesHtml = Object.entries(q.choices).map(([key, val]) => {
            const selectedClass = (selected === key) ? 'selected' : '';
            return `
                <button type="button" class="choice-btn btn w-100 text-left ${selectedClass} border border-solid border-indigo-300"
                        onclick="selectAnswer(${q.id}, '${key}', this)">
                    <span class="inline-block w-8 h-8 rounded-full text-center mr-3 ${selected ? (selected === key ? 'bg-white text-purple-600' : 'bg-purple-500 text-white') : 'bg-purple-500 text-white'}">
                        ${key}
                    </span>
                    <strong>${key}.</strong> ${escapeHtml(val)}
                </button>
            `;
        }).join('');

        questionView.innerHTML = `
            <div class="question-card shadow-lg">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <small class="text-muted">Question ${currentIndex + 1} / ${total}</small>
                        <h4 class="mb-1">${escapeHtml(q.text)}</h4>
                    </div>
                    <div style="width:160px;">
                        <div class="progress-small mb-1"><div style="width:${progressPct}%"></div></div>
                        <div class="text-right small text-muted">${progressPct}%</div>
                    </div>
                </div>

                <div class="choices">
                    ${choicesHtml}
                </div>
            </div>
        `;

        // Update counter & nav
        questionCounter.textContent = `Question ${currentIndex + 1} of ${total}`;
        prevBtn.disabled = (currentIndex === 0);
        // Show next button or submit button depending on index
        if (currentIndex === total - 1) {
            nextBtn.style.display = 'none';
            submitBtn.style.display = 'inline-block';
        } else {
            nextBtn.style.display = 'inline-block';
            submitBtn.style.display = 'none';
        }

        // If current question has no answer, disable Next
        const nextDisabled = !answers[q.id] && (currentIndex !== total - 1);
        nextBtn.disabled = nextDisabled;
    }

    // Handle selecting an answer
    function selectAnswer(questionId, choice, btn) {
        // Update state
        answers[questionId] = choice;

        // Update UI: remove .selected from sibling choice-btns, add to clicked
        const parent = btn.closest('.choices');
        if (parent) {
            parent.querySelectorAll('.choice-btn').forEach(el => el.classList.remove('selected'));
        }
        btn.classList.add('selected');

        // Update Next button state
        nextBtn.disabled = false;
    }

    function nextQuestion() {
        if (currentIndex < total - 1) {
            currentIndex++;
            renderQuestion();
            // scroll to top of quiz container for better UX
            window.scrollTo({ top: document.querySelector('.quiz-container').offsetTop - 10, behavior: 'smooth' });
        }
    }

    function previousQuestion() {
        if (currentIndex > 0) {
            currentIndex--;
            renderQuestion();
            window.scrollTo({ top: document.querySelector('.quiz-container').offsetTop - 10, behavior: 'smooth' });
        }
    }

    // Prepare hidden inputs and submit the real form
    function submitFromJS() {
        // Confirm
        if (!confirm('Are you sure you want to submit? Once submitted your answers cannot be changed.')) {
            return;
        }

        // Ensure all questions answered
        const unanswered = quiz.questions.filter(q => !answers[q.id]);
        if (unanswered.length > 0) {
            if (!confirm('You have unanswered questions. Are you sure you want to submit anyway?')) {
                return;
            }
        }

        // Remove any previous hidden inputs
        hiddenAnswersContainer.innerHTML = '';

        // Create a hidden input for each answered question (and for unanswered create empty value)
        quiz.questions.forEach(q => {
            const input = document.createElement('input');
            input.type = 'hidden';
            // name format expected by backend: answers[<question_id>]
            input.name = `answers[${q.id}]`;
            input.value = answers[q.id] ? answers[q.id] : '';
            hiddenAnswersContainer.appendChild(input);
        });

        // Finally submit the form
        document.getElementById('quizForm').submit();
    }

    // For graceful fallback if user clicks browser submit or JS calls form submit
    function handleFinalSubmit(e) {
        // If JS is enabled, we should prevent default submit because we want to inject hidden inputs first.
        // However, if hiddenAnswersContainer already has inputs (maybe user clicked native submit after JS prepared), allow.
        if (hiddenAnswersContainer.children.length === 0) {
            e.preventDefault();
            submitFromJS();
            return false;
        }
        // else let form submit normally
        return true;
    }

    // Helper to escape text for HTML injection
    function escapeHtml(unsafe) {
        if (typeof unsafe !== 'string') return unsafe;
        return unsafe.replace(/[&<"'>]/g, function(m) {
            switch(m) {
                case '&': return '&amp;';
                case '<': return '&lt;';
                case '>': return '&gt;';
                case '"': return '&quot;';
                case "'": return '&#039;';
                default: return m;
            }
        });
    }



    // Initialize UI on load
    renderQuestion();

    // Optional: keyboard navigation (ArrowLeft, ArrowRight)
    document.addEventListener('keydown', function(e) {
        if (e.key === 'ArrowRight') {
            if (!nextBtn.disabled && nextBtn.style.display !== 'none') nextQuestion();
        } else if (e.key === 'ArrowLeft') {
            if (!prevBtn.disabled) previousQuestion();
        }
    });

    </script>

    <!-- Footer -->
    <?php include '../../includes/footer.php'; ?>
</body>
</html>
