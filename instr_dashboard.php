<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <style>
    /* Basic styling for the page */
    body {
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      margin: 0;
      font-family: Arial, sans-serif;
      background-color: #f4f4f9;
      position: relative;
    }

    /* Profile section styling */
    .profile {
      position: absolute;
      top: 20px;
      right: 20px;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .profile img {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      object-fit: cover;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    }

    .profile-name {
      font-size: 1em;
      color: #333;
    }

    /* Container styling */
    .dashboard-container {
      display: flex;
      gap: 20px;
      flex-wrap: wrap;
    }

    /* Button styling */
    .dashboard-button {
      background-color: #4CAF50; /* Green background */
      color: white;
      border: none;
      border-radius: 10px;
      padding: 20px 40px;
      font-size: 1.5em;
      cursor: pointer;
      width: 200px;
      text-align: center;
      transition: background-color 0.3s, transform 0.3s;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .dashboard-button:hover {
      background-color: #45a049; /* Darker green on hover */
      transform: scale(1.05); /* Slightly bigger on hover */
    }

    /* Responsive styling */
    @media (max-width: 600px) {
      .dashboard-container {
        flex-direction: column;
        align-items: center;
      }
      .dashboard-button {
        width: 100%; /* Full width on small screens */
      }
      .profile {
        top: 10px;
        right: 10px;
      }
    }
  </style>
</head>
<body>

  <!-- Profile Section -->
  <div class="profile">
    <img src="admin-photo.jpg" alt="Admin Photo">
    <span class="profile-name">Admin Name</span>
  </div>

  <!-- Dashboard Buttons -->
  <div class="dashboard-container">
    <button class="dashboard-button">Button 1</button>
    <button class="dashboard-button">Button 2</button>
    <button class="dashboard-button">Button 3</button>
  </div>

</body>
</html>
