<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in</title>
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
        <a href="index.php">Sign up</a>
        <a href="#" class="signup-btn">Log in</a>
    </div>
</nav>

<!-- Main Content -->
<section class="signup-section">
<div class="left-content">
        <h1>"Ready to Learn?"

        </h1>
        <ul class="features">
            <li>✔️ Log in to resume where you left off</li>
            <li>✔️ keep advancing in your courses and assessments. </li>
            <li>✔️ Let's start your learning journey!</li>
        </ul>
    </div>
    <div class="right-content">
        <form class="signup-form" action="sign.php" method="post">
            

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
