<?php
// models/Quiz.php - FINAL CORRECTED VERSION (NO DUPLICATE update())

class Quiz {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    // CREATE QUIZ WITH OPTIONAL SECTION
    public function create($title, $date, $topic, $instructions, $adminId, $section = null) {
        $shareCode = generateShareCode();
        $stmt = $this->db->prepare("INSERT INTO quizzes 
            (title, date, topic, instructions, share_code, created_by, section) 
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $date, $topic, $instructions, $shareCode, $adminId, $section]);
        return $this->db->lastInsertId();
    }
    
    // UPDATE QUIZ (NOW SUPPORTS SECTION) - ONLY ONE update() METHOD!
    public function update($id, $title, $date, $topic, $instructions, $section = null) {
        $stmt = $this->db->prepare("UPDATE quizzes 
            SET title=?, date=?, topic=?, instructions=?, section=? WHERE id=?");
        return $stmt->execute([$title, $date, $topic, $instructions, $section, $id]);
    }

    // GET ALL QUIZZES (with question count)
    public function getAll() {
        $stmt = $this->db->query("SELECT q.*, COUNT(qst.id) as question_count 
                                   FROM quizzes q 
                                   LEFT JOIN questions qst ON q.id = qst.quiz_id 
                                   WHERE q.status='active' 
                                   GROUP BY q.id 
                                   ORDER BY q.created_at DESC");
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM quizzes WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function getByShareCode($code) {
        $stmt = $this->db->prepare("SELECT * FROM quizzes WHERE share_code = ? AND status='active'");
        $stmt->execute([$code]);
        return $stmt->fetch();
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM quizzes WHERE id=?");
        return $stmt->execute([$id]);
    }
    
    public function addQuestion($quizId, $questionText, $choiceA, $choiceB, $choiceC, $choiceD, $correctAnswer) {
        $stmt = $this->db->prepare("INSERT INTO questions 
            (quiz_id, question_text, choice_a, choice_b, choice_c, choice_d, correct_answer) 
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$quizId, $questionText, $choiceA, $choiceB, $choiceC, $choiceD, $correctAnswer]);
    }
    
    public function getQuestions($quizId) {
        $stmt = $this->db->prepare("SELECT * FROM questions WHERE quiz_id = ? ORDER BY question_order, id");
        $stmt->execute([$quizId]);
        return $stmt->fetchAll();
    }
    
    public function deleteQuestion($questionId) {
        $stmt = $this->db->prepare("DELETE FROM questions WHERE id=?");
        return $stmt->execute([$questionId]);
    }
    
    public function submitResponse($quizId, $studentId, $answers) {
        $questions = $this->getQuestions($quizId);
        $totalQuestions = count($questions);
        $correctCount = 0;
        
        $stmt = $this->db->prepare("SELECT id FROM quiz_responses WHERE quiz_id=? AND student_id=?");
        $stmt->execute([$quizId, $studentId]);
        if($stmt->fetch()) return false;

        $answerDetails = [];
        foreach($questions as $question) {
            $studentAnswer = $answers[$question['id']] ?? null;
            $isCorrect = ($studentAnswer === $question['correct_answer']);
            if($isCorrect) $correctCount++;
            
            $answerDetails[] = [
                'question_id' => $question['id'],
                'student_answer' => $studentAnswer,
                'is_correct' => $isCorrect
            ];
        }
        
        $percentage = $totalQuestions > 0 ? ($correctCount / $totalQuestions) * 100 : 0;
        
        $stmt = $this->db->prepare("INSERT INTO quiz_responses 
            (quiz_id, student_id, score, total_questions, percentage) 
            VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$quizId, $studentId, $correctCount, $totalQuestions, $percentage]);
        $responseId = $this->db->lastInsertId();
        
        foreach($answerDetails as $detail) {
            $stmt = $this->db->prepare("INSERT INTO student_answers 
            (response_id, question_id, student_answer, is_correct) VALUES (?, ?, ?, ?)");
            $stmt->execute([$responseId, $detail['question_id'], $detail['student_answer'], $detail['is_correct']]);
        }
        
        return $responseId;
    }
    
    public function getResponses($quizId) {
        $stmt = $this->db->prepare("SELECT qr.*, s.name, s.section 
                                     FROM quiz_responses qr 
                                     JOIN students s ON qr.student_id = s.id 
                                     WHERE qr.quiz_id = ? 
                                     ORDER BY qr.submitted_at DESC");
        $stmt->execute([$quizId]);
        return $stmt->fetchAll();
    }
    
    public function getResponseDetails($responseId) {
        $stmt = $this->db->prepare("SELECT sa.*, q.question_text, q.choice_a, q.choice_b, q.choice_c, q.choice_d, q.correct_answer 
                                     FROM student_answers sa 
                                     JOIN questions q ON sa.question_id = q.id 
                                     WHERE sa.response_id = ? 
                                     ORDER BY q.id");
        $stmt->execute([$responseId]);
        return $stmt->fetchAll();
    }
    
    public function hasStudentAnswered($quizId, $studentId) {
        $stmt = $this->db->prepare("SELECT id FROM quiz_responses WHERE quiz_id=? AND student_id=?");
        $stmt->execute([$quizId, $studentId]);
        return $stmt->fetch() !== false;
    }
}
?>