<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_unset();
    session_destroy();

    session_start();
    $_SESSION['logout_msg'] = "✅ You have been logged out successfully!";

    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Logout | EcoKart</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f5f5dc;
      color: #333;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }
    .logout-box {
      background: #ffffff;
      border: 2px solid #556b2f;
      border-radius: 12px;
      padding: 30px;
      text-align: center;
      box-shadow: 0px 6px 15px rgba(0,0,0,0.2);
    }
    h2 {
      color: #556b2f;
    }
    .btn {
      padding: 10px 20px;
      margin: 10px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-size: 16px;
      transition: background 0.3s ease;
    }
    .btn-yes {
      background: #556b2f;
      color: #fff;
    }
    .btn-yes:hover {
      background: #6b8e23;
    }
    .btn-no {
      background: #ccc;
      color: #333;
    }
    .btn-no:hover {
      background: #aaa;
    }
  </style>
</head>
<body>
  <div class="logout-box">
    <h2>Are you sure you want to logout?</h2>
    <form method="POST">
      <button type="submit" class="btn btn-yes">Yes, Logout</button>
      <button type="button" class="btn btn-no" onclick="window.location.href='index.php'">No, Go Back</button>
    </form>
  </div>
</body>
</html>
