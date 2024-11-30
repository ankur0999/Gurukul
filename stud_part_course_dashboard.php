<?php
// Database connection
$host = 'localhost';
$db = 'lms';
$user = 'ankur';
$pass = 'ankur';
session_start();

try {
    $conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Set course_id and student_id (in real cases, get from session or login)
$course_id = $_SESSION["course_id"]; // Example Course ID
$student_id = $_SESSION["student_id"]; // Example Student ID

// Fetch course and instructor details
$course_stmt = $conn->prepare("SELECT 
    c.Course_name AS CourseName,
    u.Name AS InstructorName
FROM 
    courses c
JOIN 
    instructor i ON c.Instructor_id = i.Instructor_id
JOIN 
    users u ON i.Email_id = u.Email_id
WHERE 
    c.Course_id = :course_id");
$course_stmt->bindParam(':course_id', $course_id);
$course_stmt->execute();
$course = $course_stmt->fetch(PDO::FETCH_ASSOC);

// Fetch quizzes
$quizzes_stmt = $conn->prepare("SELECT q.quiz_id FROM create_quiz q WHERE q.Course_id = :course_id");
$quizzes_stmt->bindParam(':course_id', $course_id);
$quizzes_stmt->execute();
$quizzes = $quizzes_stmt->fetchAll(PDO::FETCH_ASSOC);



// Fetch assignments
$assignments_stmt = $conn->prepare("SELECT * FROM create_assignment WHERE Course_id = :course_id");
$assignments_stmt->bindParam(':course_id', $course_id);
$assignments_stmt->execute();
$assignments = $assignments_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch surveys
$surveys_stmt = $conn->prepare("SELECT * FROM create_survey WHERE Course_id = :course_id");
$surveys_stmt->bindParam(':course_id', $course_id);
$surveys_stmt->execute();
$surveys = $surveys_stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['quiz_answer'])) {
        $quiz_id = $_POST['quiz_id'];
        $quiz_answer = $_POST['quiz_answer'];
        $stmt = $conn->prepare("INSERT INTO quiz_answers (Quiz_id, Student_id, Answers) VALUES (:quiz_id, :student_id, :quiz_answer)");
        $stmt->execute([':quiz_id' => $quiz_id, ':student_id' => $student_id, ':quiz_answer' => $quiz_answer]);
    } elseif (isset($_FILES['assignment_file'])) {
        $assignment_id = $_POST['assignment_id'];
        $file_path = 'uploads/' . $_FILES['assignment_file']['name'];
        move_uploaded_file($_FILES['assignment_file']['tmp_name'], $file_path);
        $stmt = $conn->prepare("INSERT INTO assignment_solution (Assignment_id, Student_id, Solutions) VALUES (:assignment_id, :student_id, :file_path)");
        $stmt->execute([':assignment_id' => $assignment_id, ':student_id' => $student_id, ':file_path' => $file_path]);
    } elseif (isset($_POST['survey_answer'])) {
        $survey_id = $_POST['survey_id'];
        $survey_answer = $_POST['survey_answer'];
        $stmt = $conn->prepare("INSERT INTO survey_response (Survey_id, Student_id, Response) VALUES (:survey_id, :student_id, :survey_answer)");
        $stmt->execute([':survey_id' => $survey_id, ':student_id' => $student_id, ':survey_answer' => $survey_answer]);
    }
}

// Fetch student marks for assignments and quizzes


$marks_stmt = $conn->prepare("SELECT mark
FROM marks
WHERE Student_id = :student_id AND Course_id = :course_id;
");
$marks_stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
$marks_stmt->bindParam(':course_id', $course_id, PDO::PARAM_STR);
$marks_stmt->execute();

// Fetch the mark
$mark = $marks_stmt->fetch(PDO::FETCH_ASSOC);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Course Dashboard</title>
    <link rel="stylesheet" href="stud_part_course.css">
</head>
<body>
<div class="container">
    <h1>Course Dashboard</h1>
    <h2><?php echo htmlspecialchars($course['CourseName']); ?></h2>
    <p>Instructor: <?php echo htmlspecialchars($course['InstructorName']); ?></p>

    <!-- Quizzes Section -->
     
    <h3>Quiz</h3>
    <?php foreach ($quizzes as $quiz): ?>
        <?php 
            // Fetch question of quizzes
                $mcq_stmt = $conn->prepare("SELECT 
                q.Problem
                FROM 
                Quiz_Question_Link qql
                JOIN 
                Question q ON qql.Question_id = q.Question_id
                WHERE 
                qql.quiz_id = :quiz_id AND q.Type = 'MCQ';
            ");
            $mcq_stmt->bindParam(':quiz_id', $quiz['quiz_id']);
            $mcq_stmt->execute();
            $mcqs = $mcq_stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>
        <!--  for mcq  -->
        <?php foreach($mcqs as $mcq): ?>
            <p> (MCQ) <?php echo htmlspecialchars($mcq['Problem']); ?>
        <?php endforeach; ?>
        <!-- for t/f -->
        <?php
        $tf_stmt = $conn->prepare("SELECT 
                q.Problem
                FROM 
                Quiz_Question_Link qql
                JOIN 
                Question q ON qql.Question_id = q.Question_id
                WHERE 
                qql.quiz_id = :quiz_id AND q.Type = 'T/F';
            ");
            $tf_stmt->bindParam(':quiz_id', $quiz['quiz_id']);
            $tf_stmt->execute();
            $tfs = $tf_stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>
        <!--  for t/f  -->
        <?php foreach($tfs as $tf): ?>
            <p> (T/F) <?php echo htmlspecialchars($tf['Problem']); ?>
        <?php endforeach; ?>
        
         <?php       
            $sa_stmt = $conn->prepare("SELECT 
                q.Problem
                FROM 
                Quiz_Question_Link qql
                JOIN 
                Question q ON qql.Question_id = q.Question_id
                WHERE 
                qql.quiz_id = :quiz_id AND q.Type = 'Short Answers';
            ");
            $sa_stmt->bindParam(':quiz_id', $quiz['quiz_id']);
            $sa_stmt->execute();
            $sas = $sa_stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>
        <!--  for short answer  -->
        <?php foreach($sas as $sa): ?>
            <p> (Short Answer) <?php echo htmlspecialchars($sa['Problem']); ?>
        <?php endforeach; ?>



        <form method="post">
            <p>Quiz ID: <?php echo htmlspecialchars($quiz['quiz_id']); ?></p>
            <textarea name="quiz_answer" required></textarea>
            <input type="hidden" name="quiz_id" value="<?php echo $quiz['quiz_id']; ?>">
            <button type="submit">Submit Quiz</button>
        </form>
    <?php endforeach; ?>

    

    <!-- Assignments Section -->
    <h3>Assignments</h3>
    <?php foreach ($assignments as $assignment): ?>
        <form method="post" enctype="multipart/form-data">
            <p><?php echo htmlspecialchars($assignment['Problems']); ?></p>
            <p>Due Date: <?php echo htmlspecialchars($assignment['Due_date']); ?></p>
            <input type="file" name="assignment_file" accept="application/pdf" required>
            <input type="hidden" name="assignment_id" value="<?php echo $assignment['Assignment_id']; ?>">
            <button type="submit">Submit Assignment</button>
        </form>
    <?php endforeach; ?>

    <!-- Surveys Section -->
    <h3>Surveys</h3>
    <?php foreach ($surveys as $survey): ?>
        <form method="post">
            <p><?php echo htmlspecialchars($survey['Questions']); ?></p>
            <textarea name="survey_answer" required></textarea>
            <input type="hidden" name="survey_id" value="<?php echo $survey['Survey_id']; ?>">
            <button type="submit">Submit Survey</button>
        </form>
    <?php endforeach; ?>

    <!-- Marks Section -->
     <h3>Your Marks <?php if($mark) htmlspecialchars($mark['mark'])  ?></h3>
     
</div>
</body>
</html>

<?php $conn = null; // Close the PDO connection ?>
