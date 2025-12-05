<?php
$errors = ["username" => "", "password" => ""];
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = trim($_POST['username']);
    $pass = trim($_POST['password']);

    if (empty($user)) {
        $errors["username"] = "Username cannot be empty.";
    }
    if (empty($pass)) {
        $errors["password"] = "Password cannot be empty.";
    }

    if (!array_filter($errors)) {
        $conn = new mysqli("localhost", "root", "", "bamboo"); // database name = bamboo
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
        $stmt->bind_param("s", $user);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
           $row = $result->fetch_assoc();
           if (password_verify($pass, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];      // store user ID
            $_SESSION['username'] = $row['username']; // store username
            echo "<script>alert('You have been successfully logged in our website!'); window.location='index.php';</script>";
            exit;
           } else {
            $errors["password"] = "Incorrect password.";
           }
        } else {
           $errors["username"] = "No user found with this username.";
        }
        $stmt->close();
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <!-- Font Awesome for eye icon -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
    }
    .page-wrapper {
      position: relative;
      width: 100%;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      background-color: #e6decf; /* beige background */
    }
    /* Left icon */
    .page-wrapper::before {
  content: "";
  position: absolute;
  left: 50px;
  top: 40%;
  width: 300px;
  height: 300px;
  background-image:
    url('images/bowl.png'),
    url('images/brush icon.png'),
    url('images/chair icon.png');
     background-position:
    left center,
right center,
    bottom left,
  background-repeat: no-repeat, no-repeat, no-repeat;
  background-size: contain, contain, contain;
  opacity: 0.9;
  pointer-events: none;
  transform: translateY(-10%) rotate(-18deg);
}

    /* Right icon */
    .page-wrapper::after {
      content: "";
      position: absolute;
      right: 20px;
      top: 50%;
      transform: translateY(-50%);
      width: 100px;
      height: 100px;
      background-image: url('right-icon.png'); /* Replace with your right icon path */
      background-size: contain;
      background-repeat: no-repeat;
      background-position: center;
      opacity: 0.2;
      pointer-events: none;
    }
    .container {
      background: #fffdf7;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0px 4px 12px rgba(0,0,0,0.1);
      width: 350px;
    }
    h2 {
      text-align: center;
      color: #6b8e23; /* olive green */
    }
    input[type="text"], input[type="password"] {
      width: 100%;
      padding: 10px;
      margin: 8px 0;
      border: 1px solid #ccc;
      border-radius: 6px;
    }
    .error {
      color: red;
      font-size: 14px;
    }
    button {
      width: 100%;
      background-color: #6b8e23; /* olive green */
      color: white;
      padding: 10px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }
    button:hover {
      background-color: #556b2f;
    }
    .password-box {
      position: relative;
    }
    .toggle-eye {
      position: absolute;
      right: 10px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      color: #6b8e23; /* olive green */
    }
    .register-link {
      text-align: center;
      margin-top: 12px;
    }
    .register-link a {
      color: #6b8e23;
      text-decoration: none;
      font-weight: bold;
    }
    .register-link a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="page-wrapper">
    <div class="container">
      <h2>Login</h2>
      <form method="post">
        <label>Username</label>
        <input type="text" name="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
        <div class="error"><?= $errors['username'] ?></div>

        <label>Password</label>
        <div class="password-box">
          <input type="password" name="password" id="password">
          <i class="fa-solid fa-eye toggle-eye" id="eyeIcon" onclick="togglePassword()"></i>
        </div>
        <div class="error"><?= $errors['password'] ?></div>

        <button type="submit">Login</button>
      </form>
      <div class="register-link">
        <p>Don’t have an account? <a href="register.php">Register here</a></p>
      </div>
    </div>
  </div>

  <script>
    function togglePassword() {
      const passField = document.getElementById("password");
      const eyeIcon = document.getElementById("eyeIcon");
      if (passField.type === "password") {
        passField.type = "text";
        eyeIcon.classList.remove("fa-eye");
        eyeIcon.classList.add("fa-eye-slash");
      } else {
        passField.type = "password";
        eyeIcon.classList.remove("fa-eye-slash");
        eyeIcon.classList.add("fa-eye");
      }
    }
  </script>
</body>
</html>
