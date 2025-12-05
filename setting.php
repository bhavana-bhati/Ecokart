<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Settings</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f8f5e9; /* beige */
      margin: 0;
      padding: 0;
    }
    .container {
      width: 400px;
      margin: 60px auto;
      background: #fff;
      border: 2px solid #d6cfc4; 
      border-radius: 12px;
      padding: 20px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    h2 {
      text-align: center;
      color: #556b2f; /* olive green */
    }
    ul {
      list-style: none;
      padding: 0;
    }
    ul li {
      background: #f0ead6;
      margin: 10px 0;
      padding: 12px;
      border-radius: 8px;
      transition: 0.3s;
      display: flex;
      align-items: center;
    }
    ul li:hover {
      background: #e6dfc9;
    }
    ul li a {
      text-decoration: none;
      color: #333;
      font-weight: bold;
      margin-left: 10px;
      flex-grow: 1;
    }
    ul li i {
      color: #556b2f;
    }
  </style>
</head>
<body>

<div class="container">
  <?php if (isset($_SESSION['username'])): ?>
      <h2>Account Settings</h2>
      <ul>
        <li><i class="fa-solid fa-location-dot"></i><a href="update_address.php">Manage Address</a></li>
        <li><i class="fa-solid fa-phone"></i><a href="update_phone.php">Change Phone Number</a></li>
        <li><i class="fa-solid fa-lock"></i><a href="update_password.php">Change Password</a></li>
      </ul>
  <?php else: ?>
      <h2>General Settings</h2>
      <ul>
        <li><i class="fa-solid fa-venus-mars"></i><a href="choose_gender.php">Choose Gender</a></li>
        <li><i class="fa-solid fa-palette"></i><a href="theme.php">Change Theme</a></li>
        <li><i class="fa-solid fa-circle-question"></i><a href="help.php">Help & Support</a></li>
      </ul>
  <?php endif; ?>
</div>

</body>
</html>
