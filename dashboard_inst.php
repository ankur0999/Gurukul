<?php
// Database connection
include 'db_connection.php';
session_start();
$instructor_id = $_SESSION["instructor_id"];
$course_id = $_GET['course_id']; // Get the course ID from the URL

$stmt_i = $pdo->prepare("
        SELECT users.Name 
        FROM users 
        JOIN instructor ON instructor.Email_id = users.Email_id
        WHERE instructor.Instructor_id = :Instructor_id
    ");
    $stmt_i->bindParam(':Instructor_id', $instructor_id);
    $stmt_i->execute();
    $instructor = $stmt_i->fetchAll(PDO::FETCH_ASSOC);
    #if(!empty($instructor)) Print_R($instructor) ;
    $name = $instructor[0]['Name'] ?? " User";

// for the assignment




if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['assignment_file'])) {
    // Fetch the details from the form
    $assignment_name = $_POST['assignment_name'];
    $due_date = $_POST['due_date'];

    // Set the upload directory
    $upload_dir = 'resources/assignments/';
    $upload_file = $upload_dir . basename($_FILES['assignment_file']['name']);

    // Check if the upload directory exists, and create it if not
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    // Move the uploaded file to the designated directory
    if (move_uploaded_file($_FILES['assignment_file']['tmp_name'], $upload_file)) {
        echo "Assignment uploaded successfully.";

        // Insert the assignment details into the database
        $stmt = $pdo->prepare("INSERT INTO create_assignment (Instructor_id, Course_id, Due_date, Problems)
                               VALUES (:instructor_id, :course_id, :due_date, :problems)");
        $stmt->bindParam(':instructor_id', $instructor_id);
        $stmt->bindParam(':course_id', $course_id);
        $stmt->bindParam(':due_date', $due_date);
        $stmt->bindParam(':problems', $upload_file);  // Store the path of the uploaded file
        $stmt->execute();

        echo "<script>alert('Assignment has been uploaded');</script>";
    } else {
        echo "Error uploading assignment.";
    }
}




// Insert new quiz
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['quiz_name'])) {
    $quiz_name = $_POST['quiz_name'];
    

    // Insert the new quiz into the `create_quiz` table
    $stmt = $pdo->prepare("INSERT INTO create_quiz(Instructor_id, Course_id, quiz_name) 
                            VALUES(:instructor_id, :course_id, :quiz_name)");
    $stmt->bindParam(':quiz_name', $quiz_name);
    $stmt->bindParam(':instructor_id', $instructor_id);
    $stmt->bindParam(':course_id', $course_id);
    $stmt->execute();

    $quiz_id = $pdo->lastInsertId();
    header("Location: select_questions.php?course_id=$course_id&quiz_id=$quiz_id");
    exit;
}

// Delete existing quiz
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_quiz_id'])) {

    // Check if quiz_id is provided

    $quiz_id = $_POST['delete_quiz_id'];

    // Step 1: Get all Question_ids associated with the given quiz_id
    $query = "SELECT Question_id FROM Quiz_Question_Link WHERE quiz_id = :quiz_id";
    $stmt_q = $pdo->prepare($query);
    $stmt_q->bindParam(':quiz_id', $quiz_id, PDO::PARAM_INT);
    $stmt_q->execute();
    $question_ids = $stmt_q->fetchAll(PDO::FETCH_COLUMN);

    // Step 2: Delete answers in `question_answer` table for the fetched Question_ids
    if (!empty($question_ids)) {
        $placeholders = implode(',', array_fill(0, count($question_ids), '?'));
        $deleteQuery = "DELETE FROM question_answer WHERE Question_id IN ($placeholders)";
        $deleteStmt = $pdo->prepare($deleteQuery);
        $deleteStmt->execute($question_ids);

        
    } 



    $delete_quiz_id = $_POST['delete_quiz_id'];
    $stmt_ = $pdo->prepare("DELETE FROM Quiz_Question_Link WHERE quiz_id= :quiz_id;");
    $stmt = $pdo->prepare("DELETE FROM create_quiz WHERE quiz_id = :quiz_id");
    $stmt->bindParam(':quiz_id', $delete_quiz_id);
    $stmt_->bindParam(':quiz_id', $delete_quiz_id);
    $stmt_->execute();
    $stmt->execute();

    echo "<script>alert('quiz has been deleted');</script>";
    
}

// Fetch existing quizzes
$course_id = $_GET['course_id'];
$_SESSION['course_id'] = $course_id;
$quizzes = $pdo->query("SELECT quiz_id, quiz_name FROM create_quiz WHERE Course_id = '$course_id'")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - LMS</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<div class="sidebar">
    <h2>LMS Dashboard</h2>
    <ul>
        <li><i class="fas fa-home"></i> Dashboard</li>
        <li><i class="fas fa-book"></i> Courses</li>
        <li><a href="students.php?course_id=<?php echo $course_id; ?>"><i class="fas fa-users"></i> Students</a></li>
        <li><i class="fas fa-chart-line"></i> Analytics</li>
        <li><i class="fas fa-envelope"></i> Messages</li>
        <li><i class="fas fa-cog"></i> Settings</li>
    </ul>
</div>

<div class="main-content">
    <header>
        <h1>Welcome, <?php echo htmlspecialchars($name) ?></h1>
        <div class="header-right">
            <i class="fas fa-bell"></i>
            <div class="user-profile">
                <img src="pic1.jpg" alt="User Profile">
                <span>Admin</span>
            </div>
        </div>
    </header>

    <section class="dashboard-overview">
        <div class="card">
            <h3>Total Students</h3>
            <p>NA</p>
        </div>
        <div class="card">
            <h3>Total Courses</h3>
            <p>NA</p>
        </div>
        <div class="card">
            <h3>New Enrollments</h3>
            <p>NA</p>
        </div>
        <div class="card">
            <h3>Pending Assignments</h3>
            <p>NA</p>
        </div>
    </section>

    <!-- "Set Quiz" Button -->
    <button id="setQuizBtn" class="large-btn">Set Quiz</button>

    <!-- Modal for entering quiz name -->
    <div id="quizModal" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h2>Set Quiz</h2>
            <form action="" method="POST">
                <label for="quiz_name">Quiz Name:</label>
                <input type="text" id="quiz_name" name="quiz_name" required>
                <button type="submit">Submit</button>
            </form>
        </div>
    </div>

    <!-- List of Existing Quizzes with Delete Option -->
    <section class="quiz-list">
        <h3>Existing Quizzes</h3>
        <ul>
            <?php foreach ($quizzes as $quiz): ?>
                <li>
                    <?php echo htmlspecialchars($quiz['quiz_name']); ?>
                    <form action="" method="POST" style="display: inline;">
                        <input type="hidden" name="delete_quiz_id" value="<?php echo $quiz['quiz_id']; ?>">
                        <button type="submit" class="delete-btn">Delete</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
</div>



<!-- Modal for entering assignment details -->
<div id="assignmentModal" class="modal">
    <div class="modal-content">
        <span class="close-btn">&times;</span>
        <h2>Set Assignment</h2>
        <form action="" method="POST" enctype="multipart/form-data">
            <label for="assignment_name">Assignment Name:</label>
            <input type="text" id="assignment_name" name="assignment_name" required><br><br>

            <label for="due_date">Due Date:</label>
            <input type="datetime-local" id="due_date" name="due_date" required><br><br>

            <label for="assignment_file">Upload Assignment:</label>
            <input type="file" id="assignment_file" name="assignment_file" accept=".pdf,.doc,.docx,.txt" required><br><br>

            <button type="submit">Upload Assignment</button>
        </form>
    </div>
</div>


<script>
    // JavaScript to handle modal open/close
    var modal = document.getElementById("quizModal");
    var btn = document.getElementById("setQuizBtn");
    var span = document.getElementsByClassName("close-btn")[0];

    btn.onclick = function() {
        modal.style.display = "block";
    }
    span.onclick = function() {
        modal.style.display = "none";
    }
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    // JavaScript to handle modal open/close
var assignmentModal = document.getElementById("assignmentModal");
var assignmentBtn = document.getElementById("setAssignmentBtn");
var closeBtn = document.getElementsByClassName("close-btn")[0];

assignmentBtn.onclick = function() {
    assignmentModal.style.display = "block";
}
closeBtn.onclick = function() {
    assignmentModal.style.display = "none";
}
window.onclick = function(event) {
    if (event.target == assignmentModal) {
        assignmentModal.style.display = "none";
    }
}

</script>

</body>
</html>
