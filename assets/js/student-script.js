// Student Interface JavaScript - Math Adventure Quiz Game

$(document).ready(function() {
    // Add floating math symbols animation
    createFloatingSymbols();
    
    // Initialize quiz interface
    initQuizInterface();
    
    // Smooth animations
    $('.quiz-card, .action-card').hover(
        function() {
            $(this).addClass('animate-hover');
        },
        function() {
            $(this).removeClass('animate-hover');
        }
    );
});

// Create floating math symbols
function createFloatingSymbols() {
    const symbols = ['+', '−', '×', '÷', '=', '√', 'π', '∞', '±', '°'];
    const container = $('.landing-container, .student-dashboard');
    
    if (container.length && !$('.math-symbols').length) {
        const mathSymbolsDiv = $('<div class="math-symbols"></div>');
        
        for (let i = 0; i < 15; i++) {
            const symbol = symbols[Math.floor(Math.random() * symbols.length)];
            const span = $('<span class="math-symbol"></span>')
                .text(symbol)
                .css({
                    left: Math.random() * 100 + '%',
                    top: Math.random() * 100 + '%',
                    animationDelay: Math.random() * 3 + 's',
                    animationDuration: (3 + Math.random() * 2) + 's'
                });
            mathSymbolsDiv.append(span);
        }
        
        container.prepend(mathSymbolsDiv);
    }
}

// Landing page form toggles
function toggleForm(formType) {
    $('.action-form').slideUp(300);
    
    if (formType === 'login') {
        $('#loginForm').slideDown(300);
    } else if (formType === 'register') {
        $('#registerForm').slideDown(300);
    } else if (formType === 'guest') {
        $('#guestForm').slideDown(300);
    }
}

// Quiz Interface Functions
var quizInterface = {
    currentQuestion: 0,
    answers: {},
    totalQuestions: 0,
    quizId: null,
    
    init: function(quizId, totalQuestions) {
        this.quizId = quizId;
        this.totalQuestions = totalQuestions;
        this.currentQuestion = 0;
        this.answers = {};
        this.updateProgress();
    },
    
    selectAnswer: function(questionId, choice, element) {
        // Remove selected class from all choices in this question
        $(element).closest('.choices-container').find('.choice-btn').removeClass('selected');
        
        // Add selected class to clicked choice
        $(element).addClass('selected');
        
        // Store answer
        this.answers[questionId] = choice;
        
        // Check hidden radio button
        $('input[name="answers[' + questionId + ']"][value="' + choice + '"]').prop('checked', true);
        
        // Enable next button
        $('.btn-next, .btn-submit').prop('disabled', false);
        
        // Update answer status
        this.updateAnswerStatus();
    },
    
    nextQuestion: function() {
        if (this.currentQuestion < this.totalQuestions - 1) {
            this.currentQuestion++;
            this.showQuestion(this.currentQuestion);
            this.updateProgress();
        }
    },
    
    previousQuestion: function() {
        if (this.currentQuestion > 0) {
            this.currentQuestion--;
            this.showQuestion(this.currentQuestion);
            this.updateProgress();
        }
    },
    
    showQuestion: function(index) {
        $('.question-card').hide();
        $('.question-card').eq(index).fadeIn(300);
        
        // Scroll to top
        $('html, body').animate({ scrollTop: 0 }, 300);
        
        // Update navigation buttons
        if (index === 0) {
            $('.btn-previous').prop('disabled', true);
        } else {
            $('.btn-previous').prop('disabled', false);
        }
        
        if (index === this.totalQuestions - 1) {
            $('.btn-next').hide();
            $('.btn-submit').show();
        } else {
            $('.btn-next').show();
            $('.btn-submit').hide();
        }
        
        // Check if current question is answered
        var questionId = $('.question-card').eq(index).data('question-id');
        if (this.answers[questionId]) {
            $('.btn-next, .btn-submit').prop('disabled', false);
        } else {
            $('.btn-next, .btn-submit').prop('disabled', true);
        }
    },
    
    updateProgress: function() {
        var progress = ((this.currentQuestion + 1) / this.totalQuestions) * 100;
        $('.progress-fill').css('width', progress + '%');
        $('.progress-text').text('Question ' + (this.currentQuestion + 1) + ' of ' + this.totalQuestions);
        $('.progress-percentage').text(Math.round(progress) + '%');
    },
    
    updateAnswerStatus: function() {
        var answered = Object.keys(this.answers).length;
        $('.answers-status').html('');
        
        for (var i = 0; i < this.totalQuestions; i++) {
            var questionId = $('.question-card').eq(i).data('question-id');
            var status = this.answers[questionId] ? 'answered' : 'unanswered';
            var statusClass = this.answers[questionId] ? 'bg-success' : 'bg-secondary';
            
            if (i === this.currentQuestion) {
                statusClass += ' current';
            }
            
            var statusItem = $('<div>')
                .addClass('answer-status-item ' + statusClass)
                .text(i + 1)
                .attr('title', 'Question ' + (i + 1))
                .on('click', function() {
                    var idx = $(this).text() - 1;
                    quizInterface.currentQuestion = idx;
                    quizInterface.showQuestion(idx);
                    quizInterface.updateProgress();
                });
            
            $('.answers-status').append(statusItem);
        }
        
        $('.answered-count').text(answered + ' / ' + this.totalQuestions);
    },
    
    submitQuiz: function() {
        var answered = Object.keys(this.answers).length;
        
        if (answered < this.totalQuestions) {
            var unanswered = this.totalQuestions - answered;
            if (!confirm('You have ' + unanswered + ' unanswered question(s). Do you want to submit anyway?')) {
                return false;
            }
        }
        
        if (confirm('Are you sure you want to submit your quiz? You cannot change your answers after submission.')) {
            // Show loading
            showLoading('Submitting your quiz...');
            return true;
        }
        
        return false;
    }
};

// Initialize quiz interface
function initQuizInterface() {
    if ($('.quiz-container').length) {
        var quizId = $('.quiz-container').data('quiz-id');
        var totalQuestions = $('.question-card').length;
        
        if (totalQuestions > 0) {
            quizInterface.init(quizId, totalQuestions);
            
            // Show first question
            $('.question-card').hide();
            $('.question-card').first().show();
            
            // Hide submit button initially
            $('.btn-submit').hide();
        }
    }
}

// Choice selection handler
$(document).on('click', '.choice-btn', function() {
    var questionId = $(this).closest('.question-card').data('question-id');
    var choice = $(this).data('choice');
    quizInterface.selectAnswer(questionId, choice, this);
});

// Navigation handlers
$(document).on('click', '.btn-next', function() {
    quizInterface.nextQuestion();
});

$(document).on('click', '.btn-previous', function() {
    quizInterface.previousQuestion();
});

$(document).on('click', '.btn-submit', function(e) {
    if (!quizInterface.submitQuiz()) {
        e.preventDefault();
    }
});

// Results page animations
function showResultsAnimation() {
    // Trophy animation
    $('.trophy-icon').addClass('animated tada');
    
    // Count up animation for score
    var scoreElement = $('.score-number-large');
    if (scoreElement.length) {
        var score = parseInt(scoreElement.text().split('/')[0]);
        var count = 0;
        var interval = setInterval(function() {
            if (count <= score) {
                scoreElement.text(count + '/' + scoreElement.text().split('/')[1]);
                count++;
            } else {
                clearInterval(interval);
            }
        }, 50);
    }
    
    // Star animation
    $('.star').each(function(index) {
        $(this).css('animation-delay', (index * 0.1) + 's');
    });
    
    // Confetti effect for high scores
    var percentage = parseFloat($('.score-percentage').text());
    if (percentage >= 80) {
        createConfetti();
    }
}

// Create confetti effect
function createConfetti() {
    var colors = ['#667eea', '#764ba2', '#10b981', '#f59e0b', '#ef4444', '#3b82f6'];
    var confettiCount = 50;
    
    for (var i = 0; i < confettiCount; i++) {
        var confetti = $('<div class="confetti"></div>');
        confetti.css({
            left: Math.random() * 100 + '%',
            animationDelay: Math.random() * 3 + 's',
            backgroundColor: colors[Math.floor(Math.random() * colors.length)]
        });
        $('body').append(confetti);
        
        // Remove after animation
        setTimeout(function() {
            confetti.remove();
        }, 3000);
    }
}

// Show loading overlay
function showLoading(message) {
    message = message || 'Loading...';
    
    var loadingHtml = '<div class="loading-overlay">' +
        '<div class="loading-content">' +
        '<div class="loading-spinner"></div>' +
        '<p class="loading-message">' + message + '</p>' +
        '</div>' +
        '</div>';
    
    $('body').append(loadingHtml);
    
    $('.loading-overlay').css({
        'position': 'fixed',
        'top': 0,
        'left': 0,
        'width': '100%',
        'height': '100%',
        'background': 'rgba(0, 0, 0, 0.8)',
        'display': 'flex',
        'align-items': 'center',
        'justify-content': 'center',
        'z-index': 9999
    });
    
    $('.loading-content').css({
        'text-align': 'center',
        'color': 'white'
    });
}

// Hide loading overlay
function hideLoading() {
    $('.loading-overlay').fadeOut(300, function() {
        $(this).remove();
    });
}

// Toast notification
function showToast(message, type) {
    type = type || 'info';
    
    var icons = {
        'success': '✓',
        'error': '✗',
        'warning': '⚠',
        'info': 'ℹ'
    };
    
    var colors = {
        'success': '#10b981',
        'error': '#ef4444',
        'warning': '#f59e0b',
        'info': '#3b82f6'
    };
    
    var toast = $('<div class="toast-notification">')
        .html('<span class="toast-icon">' + icons[type] + '</span>' +
              '<span class="toast-message">' + message + '</span>')
        .css({
            'position': 'fixed',
            'top': '20px',
            'right': '20px',
            'background': colors[type],
            'color': 'white',
            'padding': '15px 25px',
            'border-radius': '10px',
            'box-shadow': '0 5px 20px rgba(0,0,0,0.3)',
            'z-index': 9999,
            'display': 'flex',
            'align-items': 'center',
            'gap': '10px',
            'animation': 'slideInRight 0.3s ease-out'
        });
    
    $('body').append(toast);
    
    setTimeout(function() {
        toast.fadeOut(300, function() {
            $(this).remove();
        });
    }, 3000);
}

// Form validation
function validateForm(formId) {
    var form = $('#' + formId);
    var isValid = true;
    
    form.find('input[required], select[required], textarea[required]').each(function() {
        if (!$(this).val()) {
            $(this).addClass('is-invalid');
            isValid = false;
        } else {
            $(this).removeClass('is-invalid');
        }
    });
    
    if (!isValid) {
        showToast('Please fill in all required fields', 'warning');
    }
    
    return isValid;
}

// Clear form validation errors on input
$(document).on('input change', '.is-invalid', function() {
    $(this).removeClass('is-invalid');
});

// Keyboard shortcuts
$(document).keydown(function(e) {
    // Only in quiz interface
    if ($('.quiz-container').length) {
        // Arrow right = Next
        if (e.keyCode === 39 && !$('.btn-next').prop('disabled')) {
            quizInterface.nextQuestion();
        }
        
        // Arrow left = Previous
        if (e.keyCode === 37 && !$('.btn-previous').prop('disabled')) {
            quizInterface.previousQuestion();
        }
        
        // Number keys 1-4 = Select answer A-D
        if (e.keyCode >= 49 && e.keyCode <= 52) {
            var choices = ['A', 'B', 'C', 'D'];
            var choiceIndex = e.keyCode - 49;
            var currentCard = $('.question-card').eq(quizInterface.currentQuestion);
            var choiceBtn = currentCard.find('.choice-btn[data-choice="' + choices[choiceIndex] + '"]');
            
            if (choiceBtn.length) {
                choiceBtn.click();
            }
        }
    }
});

// Timer functionality (if needed)
var quizTimer = {
    seconds: 0,
    interval: null,
    
    start: function(duration) {
        this.seconds = duration;
        this.interval = setInterval(function() {
            quizTimer.seconds--;
            quizTimer.updateDisplay();
            
            if (quizTimer.seconds <= 0) {
                quizTimer.stop();
                quizTimer.timeUp();
            }
        }, 1000);
    },
    
    stop: function() {
        if (this.interval) {
            clearInterval(this.interval);
        }
    },
    
    updateDisplay: function() {
        var minutes = Math.floor(this.seconds / 60);
        var seconds = this.seconds % 60;
        var display = minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
        $('.timer-display').text(display);
        
        // Warning color when time is low
        if (this.seconds < 60) {
            $('.timer-display').addClass('text-danger');
        }
    },
    
    timeUp: function() {
        alert('Time is up! The quiz will be submitted automatically.');
        $('#quizForm').submit();
    }
};

// Initialize results page
if ($('.results-container').length) {
    showResultsAnimation();
}

// Auto-resize textareas
$(document).on('input', 'textarea', function() {
    this.style.height = 'auto';
    this.style.height = (this.scrollHeight) + 'px';
});

// Prevent accidental page leave during quiz
if ($('.quiz-container').length) {
    window.addEventListener('beforeunload', function(e) {
        e.preventDefault();
        e.returnValue = 'Are you sure you want to leave? Your progress will be lost.';
    });
    
    // Remove warning after submission
    $('#quizForm').on('submit', function() {
        window.removeEventListener('beforeunload', function() {});
    });
}

// Guest quiz code formatter (uppercase)
$('#quizCode').on('input', function() {
    $(this).val($(this).val().toUpperCase());
});