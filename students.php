<?php
// Database connection
include 'db_connection.php';
session_start();

// Get the course_id from the URL (assuming you pass it in the query string)
$course_id = $_GET['course_id'];



// Fetch all students enrolled in the course
$stmt = $pdo->prepare("SELECT s.Roll_Number, u.Name, s.Email_id 
                       FROM enrollment e
                       INNER JOIN Student s ON e.Student_id = s.Roll_Number
                       INNER JOIN users u ON s.Email_id = u.Email_id
                       WHERE e.Course_id = :course_id");
$stmt->bindParam(':course_id', $course_id);
$stmt->execute();
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch the assignments for the course
$assignmentStmt = $pdo->prepare("SELECT Assignment_id, Problems FROM create_assignment WHERE Course_id = :course_id");
$assignmentStmt->bindParam(':course_id', $course_id);
$assignmentStmt->execute();
$assignments = $assignmentStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch the solutions submitted by students for the assignments
$solutionsStmt = $pdo->prepare("
    SELECT asn.Assignment_id, asn.Student_id, asn.Solutions, a.Assignment_id AS a_id
    FROM assignment_solution asn
    JOIN create_assignment a ON asn.Assignment_id = a.Assignment_id
    WHERE a.Course_id = :course_id
");
$solutionsStmt->bindParam(':course_id', $course_id);
$solutionsStmt->execute();
$solutions = $solutionsStmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students Enrolled - LMS</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<div class="sidebar">
    <h2>LMS Dashboard</h2>
    <ul>
        <li><i class="fas fa-home"></i> Dashboard</li>
        <li><i class="fas fa-book"></i> Courses</li>
        <li><a href="students.php?course_id=<?php echo htmlspecialchars($course_id); ?>"><i class="fas fa-users"></i> Students</a></li>
        <li><i class="fas fa-chart-line"></i> Analytics</li>
        <li><i class="fas fa-envelope"></i> Messages</li>
        <li><i class="fas fa-cog"></i> Settings</li>
    </ul>
</div>

<div class="main-content">
    <header>
        <h1>Students Enrolled in Course</h1>
    </header>

    <section class="students-list">
        <h3>Students Enrolled in "<?php echo htmlspecialchars($course_id); ?>"</h3>
        <div class="student-cards">
            <?php foreach ($students as $student): ?>
                <div class="student-card">
                    <div class="student-info">
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($student['Name']); ?></p>
                        <p><strong>Roll Number:</strong> <?php echo htmlspecialchars($student['Roll_Number']); ?></p>
                    </div>
                    <form action="student_quiz_answer.php?" method="GET">
                        <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($student['Roll_Number']); ?>">
                        <button type="submit" class="view-answers-btn">View Quiz Answers</button>
                    </form>

                    <!-- Download Assignment Solutions -->
                    <div class="assignment-solutions">
                        <h4>Assignment Solutions:</h4>
                        <ul>
                            <?php
                            foreach ($assignments as $assignment) {
                                foreach ($solutions as $solution) {
                                    if ($assignment['Assignment_id'] == $solution['Assignment_id'] && $solution['Student_id'] == $student['Roll_Number']) {
                                        // Create a download link for each solution
                                        $fileName = "assignment_solution_" . $student['Roll_Number'] . "_" . $assignment['Assignment_id'] . ".pdf";
                                        $fileContent = base64_encode($solution['Solutions']); // Convert binary data to base64 for display

                                        echo "<li><a href='download_solution.php?assignment_id=" . $assignment['Assignment_id'] . "&student_id=" . $student['Roll_Number'] . "' download='$fileName'>Download Solution for Assignment " . htmlspecialchars($assignment['Assignment_id']) . "</a></li>";
                                    }
                                }
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</div>

</body>
</html>
