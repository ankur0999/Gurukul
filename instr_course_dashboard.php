<?php
// Include the PDO connection
include 'db_connection.php';
session_start();

// Assume instructor_id is passed as a GET parameter
$instructor_id = $_SESSION["instructor_id"];
$_SESSION['course_id'] = "";

try {
    // Query to fetch assigned courses for the instructor
    $stmt = $pdo->prepare("SELECT Course_id, Course_name FROM courses WHERE Instructor_id = :instructor_id");
    $stmt->bindParam(':instructor_id', $instructor_id, PDO::PARAM_INT);
    $stmt->execute();
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instructor's Courses</title>
    <link rel="stylesheet" href="instr_course_dashboard.css">
</head>
<body>
    <h1>Assigned Courses</h1>
    <div class="course-list">
        <?php if (!empty($courses)): ?>
            <ul>
                <?php foreach ($courses as $course): ?>
                    <li>
                        <a href="dashboard_inst.php?course_id=<?php echo htmlspecialchars($course['Course_id']); ?>">
                            <?php echo htmlspecialchars($course['Course_name']); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No courses assigned.</p>
        <?php endif; ?>
    </div>
</body>
</html>
