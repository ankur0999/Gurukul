<?php
// Include database connection
include 'db_connection.php';
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle form submission to save answers
    foreach ($_POST['answers'] as $question_id => $answer) {
        $student_id = $_SESSION["student_id"]; // Set the student_id based on the session or login
        $stmt = $pdo->prepare("INSERT INTO lms.question_answer (Question_id, Student_id, answer) VALUES (?, ?, ?)
                               ON DUPLICATE KEY UPDATE answer = ?");
        $stmt->execute([$question_id, $student_id, $answer, $answer]);
    }

    echo "<script>alert('Answer has been uploaded!');</script>";
    header("Location: stud_qas.php");
    exit();
}

// Fetch all questions
$quiz_id = $_GET['quiz_id']; // Example quiz_id, change as needed
$stmt = $pdo->prepare("SELECT q.Question_id, q.Problem, q.Type
                       FROM question q
                       INNER JOIN Quiz_Question_Link qql ON q.Question_id = qql.Question_id
                       WHERE qql.quiz_id = :quiz_id");
$stmt->bindParam(':quiz_id', $quiz_id);
$stmt->execute();
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Questions</title>
    <link rel="stylesheet" href="stud_quiz.css">
</head>
<body>

<div class="quiz-container">
    <h1>Quiz Questions</h1>
    <form method="POST" action="">
        <?php foreach ($questions as $question): ?>
            <div class="question">
                <h3><?php echo htmlspecialchars($question['Problem']); ?></h3>
                <input type="hidden" name="quiz_id" value="<?php echo htmlspecialchars($quiz_id); ?>">
                
                <!-- Text area for answering the question -->
                <?php if ($question['Type'] === 'Short Answers'): ?>
                    <textarea name="answers[<?php echo $question['Question_id']; ?>]" rows="4" placeholder="Your answer here..."></textarea>
                <?php elseif ($question['Type'] === 'MCQ'): ?>
                    <!-- Example MCQ options -->
                    <label>
                        <input type="radio" name="answers[<?php echo $question['Question_id']; ?>]" value="A"> Option A
                    </label>
                    <label>
                        <input type="radio" name="answers[<?php echo $question['Question_id']; ?>]" value="B"> Option B
                    </label>
                    <label>
                        <input type="radio" name="answers[<?php echo $question['Question_id']; ?>]" value="C"> Option C
                    </label>
                    <label>
                        <input type="radio" name="answers[<?php echo $question['Question_id']; ?>]" value="D"> Option D
                    </label>
                <?php elseif ($question['Type'] === 'T/F'): ?>
                    <!-- True/False options -->
                    <label>
                        <input type="radio" name="answers[<?php echo $question['Question_id']; ?>]" value="True"> True
                    </label>
                    <label>
                        <input type="radio" name="answers[<?php echo $question['Question_id']; ?>]" value="False"> False
                    </label>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
        <button type="submit">Submit Answers</button>
    </form>
</div>

</body>
</html>
