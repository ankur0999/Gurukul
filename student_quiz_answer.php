<?php
// Database connection
include 'db_connection.php';
session_start();

// Get course_id and student_id from GET parameters or session
$course_id = $_SESSION['course_id'];
$student_id = $_GET['student_id'];

// SQL query to fetch questions, quiz details, and answers for a specific course and student
$query = "
    SELECT cq.quiz_name, q.Question_id, q.Problem, q.Type, qa.answer
    FROM create_quiz cq
    JOIN Quiz_Question_Link qql ON cq.quiz_id = qql.quiz_id
    JOIN Question q ON qql.Question_id = q.Question_id
    LEFT JOIN question_answer qa ON q.Question_id = qa.Question_id AND qa.Student_id = :student_id
    WHERE cq.Course_id = :course_id
    ORDER BY cq.quiz_name, q.Question_id;
";

// Prepare and execute the statement
$stmt = $pdo->prepare($query);
$stmt->bindParam(':course_id', $course_id);
$stmt->bindParam(':student_id', $student_id);
$stmt->execute();

// Fetch results
$quizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Quiz Answers</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* General Styling */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        /* Container */
        .container {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            background-color: #ffffff;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        h1 {
            text-align: center;
            color: #333;
            font-size: 24px;
        }

        /* Quiz and Question Cards */
        .quiz-card {
            margin-top: 20px;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 8px;
            background-color: #fafafa;
        }

        .quiz-title {
            font-size: 18px;
            font-weight: bold;
            color: #444;
        }

        .question-card {
            padding: 15px;
            margin-top: 10px;
            border-radius: 6px;
            background-color: #ffffff;
            border: 1px solid #ddd;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .question-text {
            color: #555;
            font-weight: bold;
        }

        .answer-text {
            color: #333;
            margin-top: 5px;
            font-style: italic;
        }

        /* Badge for question type */
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            color: white;
            margin-left: 10px;
        }

        .badge.short { background-color: #4CAF50; }   /* Green for Short Answer */
        .badge.mcq { background-color: #2196F3; }     /* Blue for MCQ */
        .badge.tf { background-color: #FF9800; }      /* Orange for True/False */
    </style>
</head>
<body>

<div class="container">
    <h1>Answers for Course Quizzes</h1>

    <?php if ($quizzes): ?>
        <?php
        $current_quiz_name = '';
        foreach ($quizzes as $qa):
            // Check if we’re at a new quiz
            if ($qa['quiz_name'] != $current_quiz_name):
                $current_quiz_name = $qa['quiz_name'];
        ?>
                <!-- Display quiz title -->
                <div class="quiz-card">
                    <div class="quiz-title"><?php echo htmlspecialchars($current_quiz_name); ?></div>
        <?php endif; ?>
                    <!-- Display each question and answer -->
                    <div class="question-card">
                        <div class="question-text">
                            <?php echo htmlspecialchars($qa['Problem']); ?>
                            <?php
                                $typeClass = strtolower(str_replace(" ", "", $qa['Type']));
                                $typeLabel = '';
                                switch ($qa['Type']) {
                                    case 'Short Answers': $typeLabel = 'Short'; break;
                                    case 'MCQ': $typeLabel = 'MCQ'; break;
                                    case 'T/F': $typeLabel = 'T/F'; break;
                                }
                            ?>
                            <span class="badge <?php echo $typeClass; ?>"><?php echo $typeLabel; ?></span>
                        </div>
                        <div class="answer-text">
                            Answer: <?php echo htmlspecialchars($qa['answer'] ?? 'No answer provided'); ?>
                        </div>
                    </div>
        <?php
            // Close quiz-card div if we’re at the end of a quiz
            if (!isset($quizzes[array_search($qa, $quizzes) + 1]['quiz_name']) || 
                $quizzes[array_search($qa, $quizzes) + 1]['quiz_name'] != $current_quiz_name):
        ?>
                </div>
        <?php endif; ?>
        <?php endforeach; ?>
    <?php else: ?>
        <p style="text-align:center; color: #888;">No quizzes found for this course.</p>
    <?php endif; ?>
</div>

</body>
</html>
