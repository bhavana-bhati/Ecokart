<?php
session_start(); // start session

$host = "localhost";
$user = "root";
$pass = "";
$db = "bamboo";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if(isset($_POST['submit'])){
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if username already exists
    $check = $conn->prepare("SELECT * FROM admin WHERE username=?");
    $check->bind_param("s", $username);
    $check->execute();
    $res_check = $check->get_result();
    
    if($res_check->num_rows > 0){
        $error = "Admin with this username already exists!";
    } else {
        $stmt = $conn->prepare("INSERT INTO admin (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $password);

        if($stmt->execute()){
            // Auto-login after registration
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_name'] = $username;

            // Redirect to admin dashboard
            header("Location: admin.php");
            exit;
        } else {
            $error = "Error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Registration</title>
<style>
  body {
    font-family: 'Segoe UI', sans-serif;
    background: #f5f5dc; /* beige background */
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
  }

  .register-box {
    background: #fff;
    padding: 35px 30px;
    border-radius: 12px;
    box-shadow: 0 6px 15px rgba(0,0,0,0.2);
    width: 360px;
    text-align: center;
    border-top: 6px solid darkolivegreen;
  }

  h2 {
    color: darkolivegreen;
    margin-bottom: 25px;
    font-size: 24px;
  }

  input[type="text"], input[type="password"] {
    width: 90%;
    padding: 12px;
    margin: 12px 0;
    border-radius: 8px;
    border: 1px solid #ccc;
    outline: none;
    transition: 0.3s;
  }

  input[type="text"]:focus,
  input[type="password"]:focus {
    border-color: darkolivegreen;
  }

  input[type="submit"] {
    width: 95%;
    padding: 14px;
    border: none;
    border-radius: 8px;
    background: darkolivegreen;
    color: white;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    margin-top: 10px;
    transition: 0.3s;
  }

  input[type="submit"]:hover {
    background: olive;
  }

  .message {
    margin-bottom: 15px;
    font-size: 14px;
  }
  .error { color: red; }
  .success { color: green; }

  .login-link {
    display: block;
    margin-top: 18px;
    font-size: 14px;
    color: darkolivegreen;
    text-decoration: none;
  }
  .login-link:hover {
    text-decoration: underline;
  }
</style>
</head>
<body>
  <div class="register-box">
    <h2>🔐 Admin Registration</h2>
    
    <?php 
      if(isset($error)) echo "<p class='message error'>$error</p>";
      if(isset($success)) echo "<p class='message success'>$success</p>";
    ?>

    <form method="POST">
      <input type="text" name="username" placeholder="Enter Username" required><br>
      <input type="password" name="password" placeholder="Enter Password" required><br>
      <input type="submit" name="submit" value="Register">
    </form>

    <a href="admin_login.php" class="login-link">Already Registered? Login Here</a>
  </div>
</body>
</html>
