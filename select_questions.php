<?php
// Database connection
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['course_id']) && isset($_GET['quiz_id'])) {
    $course_id = $_GET['course_id'];
    $quiz_id = $_GET['quiz_id'];

    // Fetch questions for the specified course
    $stmt = $pdo->prepare("SELECT Question_id, Problem, Type FROM Question WHERE Course_id = :course_id");
    $stmt->bindParam(':course_id', $course_id);
    $stmt->execute();
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle the form submission to link selected questions to the quiz
    $quiz_id = $_POST['quiz_id'];
    $selected_questions = $_POST['question_ids'] ?? [];

    // Insert selected questions into Quiz_Question_Link table
    $stmt = $pdo->prepare("INSERT INTO Quiz_Question_Link (quiz_id, Question_id) VALUES (:quiz_id, :question_id)");

    foreach ($selected_questions as $question_id) {
        $stmt->execute([':quiz_id' => $quiz_id, ':question_id' => $question_id]);
    }

    echo "<script>alert('qestions has been uploaded!');</script>";
    exit;
    
    
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Questions for Quiz</title>
    <link rel="stylesheet" href="select_questions.css">
</head>
<body>

<div class="container">
    <h2>Select Questions for Quiz</h2>
    <form action="select_questions.php" method="POST">
        <input type="hidden" name="quiz_id" value="<?php echo htmlspecialchars($quiz_id); ?>">
        
        <?php if ($questions): ?>
            <ul class="question-list">
                <?php foreach ($questions as $question): ?>
                    <li>
                        <input type="checkbox" name="question_ids[]" value="<?php echo $question['Question_id']; ?>">
                        <label>
                            <strong><?php echo htmlspecialchars($question['Problem']); ?></strong>
                            <em>(<?php echo htmlspecialchars($question['Type']); ?>)</em>
                        </label>
                    </li>
                <?php endforeach; ?>
            </ul>
            <button type="submit" class="submit-btn">Add to Quiz</button>
        <?php else: ?>
            <p>No questions available for this course.</p>
        <?php endif; ?>
    </form>
</div>

</body>
</html>
