<?php
session_start(); //  Start session

$errors = ["username" => "", "password" => "", "phone" => "", "address" => "", "general" => ""];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = trim($_POST['username']);
    $phone = trim($_POST['phone']);
    $pass = trim($_POST['password']);
    $address = trim($_POST['address']);


    if (!array_filter($errors)) {
        $conn = new mysqli("localhost", "root", "", "bamboo");
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $check = $conn->prepare("SELECT id FROM users WHERE username = ? OR phone = ?");
        $check->bind_param("ss", $user, $phone);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $errors["general"] = "User with this username or phone already exists!";
        } else {
            $hashedPass = password_hash($pass, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("INSERT INTO users (username, phone, address, password) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $user, $phone, $address, $hashedPass);

            if ($stmt->execute()) {
                $_SESSION['user_id'] = $stmt->insert_id; 
                $_SESSION['username'] = $user;

                header("Location: index.php");
                exit();
            } else {
                $errors["general"] = "Error while registering. Try again!";
            }
            $stmt->close();
        }

        $check->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f5f5dc; 
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .container {
      background: #fff;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0px 4px 12px rgba(0,0,0,0.1);
      width: 350px;
    }
    h2 {
      text-align: center;
      color: #556b2f; 
    }
    input[type="text"], input[type="password"], input[type="tel"], textarea {
      width: 100%;
      padding: 10px;
      margin: 8px 0;
      border: 1px solid #ccc;
      border-radius: 6px;
      resize: none;
    }
    .error {
      color: red;
      font-size: 14px;
    }
    .general-error {
      text-align: center;
      color: red;
      font-weight: bold;
      margin-bottom: 10px;
    }
    button {
      width: 100%;
      background-color: #556b2f; 
      color: white;
      padding: 10px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }
    button:hover {
      background-color: #6b8e23; 
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
      color: #556b2f;
    }
    .login-link {
      text-align: center;
      margin-top: 12px;
    }
    .login-link a {
      color: #556b2f;
      text-decoration: none;
      font-weight: bold;
    }
    .login-link a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Register</h2>
    <?php if (!empty($errors["general"])): ?>
      <div class="general-error"><?= $errors["general"] ?></div>
    <?php endif; ?>
    <form method="post">
      <label>Username</label>
      <input type="text" name="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
      <div class="error"><?= $errors['username'] ?></div>

      <label>Phone</label>
      <input type="tel" name="phone" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
      <div class="error"><?= $errors['phone'] ?></div>

      <label>Address</label>
      <textarea name="address" rows="3"><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
      <div class="error"><?= $errors['address'] ?></div>

      <label>Password</label>
      <div class="password-box">
        <input type="password" name="password" id="password" value="<?= htmlspecialchars($_POST['password'] ?? '') ?>">
        <i class="fa-solid fa-eye toggle-eye" id="eyeIcon" onclick="togglePassword()"></i>
      </div>
      <div class="error"><?= $errors['password'] ?></div>

      <button type="submit">Register</button>

      <div class="login-link">
        Already registered? <a href="login.php">Login</a>
      </div>
    </form>
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