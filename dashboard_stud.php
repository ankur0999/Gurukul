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
        <li><i class="fas fa-users"></i> Students</li>
        <li><i class="fas fa-chart-line"></i> Analytics</li>
        <li><i class="fas fa-envelope"></i> Messages</li>
        <li><i class="fas fa-cog"></i> Settings</li>
    </ul>
</div>

<div class="main-content">
    <header>
        <h1>Welcome, [User]</h1>
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
            <p>1200</p>
        </div>
        <div class="card">
            <h3>Total Courses</h3>
            <p>35</p>
        </div>
        <div class="card">
            <h3>New Enrollments</h3>
            <p>150</p>
        </div>
        <div class="card">
            <h3>Pending Assignments</h3>
            <p>45</p>
        </div>
    </section>

    <section class="charts">
        <div class="chart">
            <h3>Student Progress</h3>
            <!-- Insert chart.js canvas or an image placeholder here -->
            <canvas id="progressChart"></canvas>
        </div>
        <div class="chart">
            <h3>Course Completion Rate</h3>
            <canvas id="completionChart"></canvas>
        </div>
    </section>

    <section class="course-list">
        <h3>Recent Courses</h3>
        <table>
            <tr>
                <th>Course Name</th>
                <th>Enrolled</th>
                <th>Progress</th>
                <th>Actions</th>
            </tr>
            <tr>
                <td>JavaScript Basics</td>
                <td>200</td>
                <td>80%</td>
                <td><button>Manage</button></td>
            </tr>
            <tr>
                <td>Advanced Python</td>
                <td>150</td>
                <td>60%</td>
                <td><button>Manage</button></td>
            </tr>
            <!-- Add more rows as needed -->
        </table>
    </section>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="dashboard.js"></script>
</body>
</html>
