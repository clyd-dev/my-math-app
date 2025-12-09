<?php
// controllers/QuizController.php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Quiz.php';

class QuizController {
    private $quizModel;
    
    public function __construct() {
        $this->quizModel = new Quiz();
    }
    
    /**
     * Create new quiz
     */
    public function createQuiz($title, $date, $topic, $instructions, $adminId) {
        if (empty($title) || empty($date) || empty($instructions)) {
            return ['success' => false, 'message' => 'Title, date, and instructions are required'];
        }
        
        $quizId = $this->quizModel->create($title, $date, $topic, $instructions, $adminId);
        
        if ($quizId) {
            return ['success' => true, 'quiz_id' => $quizId, 'message' => 'Quiz created successfully'];
        }
        
        return ['success' => false, 'message' => 'Failed to create quiz'];
    }
    
    /**
     * Get all quizzes
     */
    public function getAllQuizzes() {
        return $this->quizModel->getAll();
    }
    
    /**
     * Get quiz by ID
     */
    public function getQuiz($id) {
        return $this->quizModel->getById($id);
    }
    
    /**
     * Get quiz by share code
     */
    public function getQuizByShareCode($code) {
        return $this->quizModel->getByShareCode($code);
    }
    
    /**
     * Update quiz
     */
    public function updateQuiz($id, $title, $date, $topic, $instructions) {
        if (empty($title) || empty($date) || empty($instructions)) {
            return ['success' => false, 'message' => 'Title, date, and instructions are required'];
        }
        
        if ($this->quizModel->update($id, $title, $date, $topic, $instructions)) {
            return ['success' => true, 'message' => 'Quiz updated successfully'];
        }
        
        return ['success' => false, 'message' => 'Failed to update quiz'];
    }
    
    /**
     * Delete quiz
     */
    public function deleteQuiz($id) {
        if ($this->quizModel->delete($id)) {
            return ['success' => true, 'message' => 'Quiz deleted successfully'];
        }
        
        return ['success' => false, 'message' => 'Failed to delete quiz'];
    }
    
    /**
     * Add question to quiz
     */
    public function addQuestion($quizId, $questionText, $choiceA, $choiceB, $choiceC, $choiceD, $correctAnswer) {
        if (empty($questionText) || empty($choiceA) || empty($choiceB) || 
            empty($choiceC) || empty($choiceD) || empty($correctAnswer)) {
            return ['success' => false, 'message' => 'All question fields are required'];
        }
        
        if (!in_array($correctAnswer, ['A', 'B', 'C', 'D'])) {
            return ['success' => false, 'message' => 'Invalid correct answer'];
        }
        
        if ($this->quizModel->addQuestion($quizId, $questionText, $choiceA, $choiceB, $choiceC, $choiceD, $correctAnswer)) {
            return ['success' => true, 'message' => 'Question added successfully'];
        }
        
        return ['success' => false, 'message' => 'Failed to add question'];
    }
    
    /**
     * Get quiz questions
     */
    public function getQuestions($quizId) {
        return $this->quizModel->getQuestions($quizId);
    }
    
    /**
     * Delete question
     */
    public function deleteQuestion($questionId) {
        if ($this->quizModel->deleteQuestion($questionId)) {
            return ['success' => true, 'message' => 'Question deleted successfully'];
        }
        
        return ['success' => false, 'message' => 'Failed to delete question'];
    }
    
    /**
     * Submit quiz response
     */
    public function submitResponse($quizId, $studentId, $answers) {
        if (empty($answers)) {
            return ['success' => false, 'message' => 'No answers provided'];
        }
        
        // Check if already submitted
        if ($this->quizModel->hasStudentAnswered($quizId, $studentId)) {
            return ['success' => false, 'message' => 'You have already submitted this quiz'];
        }
        
        $responseId = $this->quizModel->submitResponse($quizId, $studentId, $answers);
        
        if ($responseId) {
            return ['success' => true, 'response_id' => $responseId, 'message' => 'Quiz submitted successfully'];
        }
        
        return ['success' => false, 'message' => 'Failed to submit quiz'];
    }
    
    /**
     * Get quiz responses
     */
    public function getResponses($quizId) {
        return $this->quizModel->getResponses($quizId);
    }
    
    /**
     * Get response details
     */
    public function getResponseDetails($responseId) {
        return $this->quizModel->getResponseDetails($responseId);
    }
    
    /**
     * Check if student has answered quiz
     */
    public function hasStudentAnswered($quizId, $studentId) {
        return $this->quizModel->hasStudentAnswered($quizId, $studentId);
    }
    
    /**
     * Get quiz with questions count
     */
    public function getQuizWithStats($quizId) {
        $quiz = $this->quizModel->getById($quizId);
        if (!$quiz) {
            return null;
        }
        
        $questions = $this->quizModel->getQuestions($quizId);
        $responses = $this->quizModel->getResponses($quizId);
        
        $quiz['question_count'] = count($questions);
        $quiz['response_count'] = count($responses);
        
        return $quiz;
    }
}
?>