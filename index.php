<?php 

session_start();
session_unset();
session_destroy();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LMS</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- Navigation Bar -->
<nav class="navbar">
    <div class="navbar-left">
        
        <span class="logo">Learning Management System</span>
        
    </div>
    <div class="navbar-right">
        <a href="#">Customer Support</a>
        <a href="#">Contact Sales</a>
        <a href="signin.php">Log in</a>
        <a href="signup.php?type=Instructor" class="cta-button">Get started as Instructor</a>
        <a href="signup.php?type=Student" class="cta-button">Get started as Student</a>
    </div>
</nav>



<!-- Main Section -->
<section class="main-content">
    <div class="content-left">
        <img src="pic1.jpg" alt="Website Builder Screenshot" class="builder-screenshot">
    </div>
    <div class="content-right">
        <h1>Free Education for Everyone <span class="tag">Free</span></h1>
        <p>Stay on top of your learning with a platform that offers real-time evaluations, assignments, and personalized feedback.</p>
        <a href="#" class="cta-button primary">Get started free</a>
        <ul class="benefits">
            <li>Engage, learn, and succeed with our LMS </li>
            <li>From quizzes to assignments, our platform provides everything you need </li>
            <li>Leverage custom modules to deliver a great user experience</li>
        </ul>
    </div>
</section>

<!-- Optional Footer Section -->
<footer class="footer">
    <button class="feedback-btn">Is this page relevant to you?</button>
</footer>

</body>
</html>
