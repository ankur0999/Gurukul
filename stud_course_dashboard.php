<?php
// Database connection using PDO
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

// Set the student_id (you would typically get this from session or login)
$student_id = $_SESSION["student_id"]; // Example student_id
$_SESSION["course_id"] = "";
// Join a course if "join" button is clicked
if (isset($_POST['join'])) {
    $course_id = $_POST['course_id'];
    $stmt = $conn->prepare("INSERT INTO lms.enrollment (Student_id, Course_id) VALUES (:student_id, :course_id)");
    $stmt->bindParam(':student_id', $student_id);
    $stmt->bindParam(':course_id', $course_id);
    $stmt->execute();
}
// Explore a course if "explore button" clicked

if (isset($_POST['explore'])) {

    $_SESSION["course_id"] = $_POST['course_id'];
    header("Location: stud_qas.php");

    exit();
}

// Fetch available courses
$available_courses_stmt = $conn->prepare("SELECT * FROM lms.courses WHERE Course_id NOT IN 
    (SELECT Course_id FROM lms.enrollment WHERE Student_id = :student_id)");
$available_courses_stmt->bindParam(':student_id', $student_id);
$available_courses_stmt->execute();
$available_courses = $available_courses_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch joined courses
$joined_courses_stmt = $conn->prepare("SELECT * FROM lms.courses c 
    JOIN lms.enrollment jc ON c.Course_id = jc.Course_id WHERE jc.Student_id = :student_id");
$joined_courses_stmt->bindParam(':student_id', $student_id);
$joined_courses_stmt->execute();
$joined_courses = $joined_courses_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="stud_courses.css">
</head>
<body>

<div class="container">
    <h1>Welcome to the Student Dashboard</h1>
    
    <!-- Available Courses Section -->
    <h2>Available Courses</h2>
    <div class="courses">
        <?php foreach ($available_courses as $course): ?>
            <div class="course">
                <h3><?php echo htmlspecialchars($course['Course_name']); ?></h3>
                <form method="post">
                    <input type="hidden" name="course_id" value="<?php echo $course['Course_id']; ?>">
                    <button type="submit" name="join">Join Course</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
    
    <!-- Joined Courses Section -->
    <h2>Your Joined Courses</h2>
    <div class="courses">
        <?php foreach ($joined_courses as $course): ?>
            <div class="course">
                <h3><?php echo htmlspecialchars($course['Course_name']); ?></h3>
                <form method="post">
                    <input type="hidden" name="course_id" value="<?php echo $course['Course_id']; ?>">
                    <button type="submit" name="explore">Explore</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>

<?php
$conn = null; // Close the PDO connection
?>
