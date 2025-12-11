<?php
// ===== FILE: student/take-quiz.php =====
require_once '../../config/config.php';
require_once '../../models/Quiz.php';
include '../../includes/header.php';

$quizId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$quiz = new Quiz();
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
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
        }
        .quiz-container { max-width: 900px; margin: 0 auto; }
        .question-card { background: white; border-radius: 15px; padding: 28px; margin-bottom: 20px; }
        .choice-btn { padding: 14px; margin: 6px 0; text-align: left; font-size: 15px; border-width: 2px; }
        .choice-btn.selected { background-color: #667eea !important; color: white !important; border-color: #4c51bf !important; }
        .progress-small { height: 10px; border-radius: 999px; overflow: hidden; background: rgba(0,0,0,0.08); }
        .progress-small > div { height: 100%; background: linear-gradient(90deg,#7c3aed,#ec4899); transition: width .3s ease; }
        /* Hide JS-only container when JS disabled */
        .js-only { display: none; }
        /* When JS is available, add a class to body to show JS UI */
        body.js-enabled .no-js { display: none !important; }
        body.js-enabled .js-only { display: block !important; }
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
