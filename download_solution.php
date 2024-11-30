<?php
// Database connection
include 'db_connection.php';

// Check if assignment_id and student_id are set in the URL
if (isset($_GET['assignment_id']) && isset($_GET['student_id'])) {
    $assignment_id = $_GET['assignment_id'];
    $student_id = $_GET['student_id'];

    // Fetch the assignment solution from the database
    $stmt = $pdo->prepare("SELECT Solutions FROM assignment_solution WHERE Assignment_id = :assignment_id AND Student_id = :student_id");
    $stmt->bindParam(':assignment_id', $assignment_id);
    $stmt->bindParam(':student_id', $student_id);
    $stmt->execute();
    $solution = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($solution) {
        // Send headers to force download the solution as a file
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="solution_' . $assignment_id . '_' . $student_id . '.pdf"');
        echo $solution['Solutions']; // Output the solution data (binary)
        exit;
    } else {
        echo "No solution found for this assignment.";
    }
} else {
    echo "Invalid parameters.";
}
