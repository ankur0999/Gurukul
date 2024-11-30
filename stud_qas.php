<?php
// Include the PDO connection
include 'db_connection.php';

session_start();


// Check if the assignment ID is provided
if (isset($_GET['assignment_id'])) {
    $assignment_id = $_GET['assignment_id'];

    // Fetch the file path from the database
    try {
        $stmt = $pdo->prepare("SELECT Problems FROM create_assignment WHERE Assignment_id = :assignment_id");
        $stmt->bindParam(':assignment_id', $assignment_id, PDO::PARAM_INT);
        $stmt->execute();
        $assignment = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($assignment) {
            $filePath = $assignment['Problems'];

            // Check if the file exists
            if (file_exists($filePath)) {
                // Set headers to download the file
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($filePath));

                // Output the file to the user
                readfile($filePath);
                exit;
            } else {
                echo "Error: File not found at path $filePath.";
            }
        } else {
            echo "Error: Assignment not found.";
        }
    } catch (PDOException $e) {
        echo "Error fetching file: " . $e->getMessage();
    }
} else {
    #echo "No assignment ID provided.";
}





// Check if form data and file are received via POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['assignment_answer'])) {
    // Fetch the assignment ID and student ID (you should set the student ID dynamically, for example from session or request)
    $assignment_id = $_POST['assignment_id'];
    $student_id = $_SESSION['student_id'];  // Example: replace this with the actual student ID from session or input

    // Open the uploaded file
    $file = $_FILES['assignment_answer']['tmp_name'];
    $file_name = $_FILES['assignment_answer']['name'];
    $file_data = file_get_contents($file); // Read the file content into a variable

    

    try {
        // Prepare the SQL statement to insert the file as a BLOB
        $stmt = $pdo->prepare("INSERT INTO assignment_solution (Assignment_id, Student_id, Solutions)
                               VALUES (:assignment_id, :student_id, :solutions)");

        // Bind parameters
        $stmt->bindParam(':assignment_id', $assignment_id, PDO::PARAM_INT);
        $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
        $stmt->bindParam(':solutions', $file_data, PDO::PARAM_LOB);

        // Execute the insert query
        if ($stmt->execute()) {
            echo "Assignment solution uploaded successfully.";
        } else {
            echo "Error uploading assignment solution.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}






try {
    // Fetch all quizzes from `create_quiz`
    $quizStmt = $pdo->prepare("SELECT quiz_id, quiz_name FROM create_quiz");
    $quizStmt->execute();
    $quizzes = $quizStmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch all assignments from `create_assignment`
    $assignmentStmt = $pdo->prepare("SELECT Assignment_id, Due_date FROM create_assignment");
    $assignmentStmt->execute();
    $assignments = $assignmentStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quizzes, Assignments, and Survey</title>
    <link rel="stylesheet" href="stud_qas.css">
    <script>
        // Navigate to the quiz page
        document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById("surveyModal");
        modal.style.display = "none";  // Ensure it's hidden on page load
        });


        function goToQuiz(quizId) {
            if (quizId) {
                window.location.href = "stud_quiz.php?quiz_id=" + quizId;
            }
        }

        // Toggle the modal for the survey
        function toggleSurveyModal() {
            const modal = document.getElementById("surveyModal");
            modal.style.display = (modal.style.display === "block") ? "none" : "block";
        }

        // Function to close the modal when clicking outside of it
        window.onclick = function(event) {
            const modal = document.getElementById("surveyModal");
            if (event.target === modal) {
                modal.style.display = "none";
            }
        }

    </script>
</head>
<body>

<!-- Quiz Dropdown -->
<div class="dropdown">
    <button class="quiz-button">Select Quiz</button>
    <div class="dropdown-content">
        <?php foreach ($quizzes as $quiz): ?>
            <a href="#" onclick="goToQuiz(<?php echo htmlspecialchars($quiz['quiz_id']); ?>)">
                <?php echo htmlspecialchars($quiz['quiz_name']); ?>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<!-- Assignment Dropdown -->
<div class="dropdown">
    <button class="assignment-button">Assignments</button>
    <div class="dropdown-content">
        <?php foreach ($assignments as $assignment): ?>
            <a href="stud_qas.php?assignment_id=<?php echo htmlspecialchars($assignment['Assignment_id']); ?>" download>Download Assignment <?php echo htmlspecialchars($assignment['Assignment_id']); ?> </a>
            <p> Due Date : <?php echo htmlspecialchars($assignment['Due_date']) ?> </p>
            <form action="" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="assignment_id" value="<?php echo htmlspecialchars($assignment['Assignment_id']); ?>">
                <input type="file" name="assignment_answer" accept="application/pdf" required>
                <button type="submit">Upload Answer</button>
            </form>
        <?php endforeach; ?>
    </div>
</div>

<!-- Survey Button -->
<button class="survey-button" onclick="toggleSurveyModal()">Take Survey</button>

<!-- Survey Modal -->
<div id="surveyModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="toggleSurveyModal()">&times;</span>
        <h2>Survey Questions</h2>
        <form action="submit_survey.php" method="POST">
            <textarea name="survey_answer" rows="4" placeholder="Enter your answer here"></textarea>
            <button type="submit">Submit Survey</button>
        </form>
    </div>
</div>

</body>
</html>
