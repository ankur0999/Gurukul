<?php
    session_start();
    session_unset();
    session_destroy();
    $username = isset($_GET['type']) ? $_GET['type']:'';
    if(isset($_GET['error'])){
        $message = "Weak Password";
        
        echo "<script>alert('$message');</script>";
    }

    
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="signup.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar">
    <div class="navbar-left">
        <span class="logo">Learning Management System</span>
    </div>
    <div class="navbar-right">
        <a href="#">Contact Sales</a>
        <a href="signin.php">Log in</a>
        <a href="#" class="signup-btn">Sign up</a>
    </div>
</nav>

<!-- Main Content -->
<section class="signup-section">
    <div class="left-content">
        <h1>"Join our Learning Community"
        </h1>
        <ul class="features">
            <li>✔️ Sign up to access courses, assignments, and interactive quizzes</li>
            <li>✔️ designed to help you succeed. </li>
            <li>✔️ Let's start your learning journey!</li>
        </ul>
    </div>
    <div class="right-content">
        <form class="signup-form" action="sign.php" method="post">
            <input type="hidden" id="type" name="type" value="<?php echo htmlspecialchars($username);?>">
            <label for="name">* Name (required)</label>
            <input type="text" id="name" name="name" placeholder="Your name" required>

            <label for="email">* Email (required)</label>
            <input type="email" id="email" name="email" placeholder="Your Email" required>

            <label for="password">* Password (required)</label>
            <input type="password" id="password" name="password" placeholder="Password" required>

            <button type="submit" class="submit-btn">Get started free</button>
            <p class="terms">By signing up, you agree to our <a href="#">terms of service</a> and <a href="#">privacy policy</a>.</p>
        </form>
    </div>
</section>

</body>
</html>
