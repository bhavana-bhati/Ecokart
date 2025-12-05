<?php
session_start();

// Database connection
$servername = "localhost";
$db_username = "root"; // Default XAMPP username
$db_password = "";     // Default XAMPP password is empty
$dbname = "bamboo_store";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle registration
if (isset($_POST['register'])) {
    $username = trim($_POST['reg_username']);
    $password = password_hash($_POST['reg_password'], PASSWORD_DEFAULT);

    // Prepared statement
    $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $password);

    if ($stmt->execute()) {
        echo "<script>alert('Registration successful! You can now log in.');</script>";
    } else {
        if ($conn->errno === 1062) {
            echo "<script>alert('Username already exists. Please choose another.');</script>";
        } else {
            echo "<script>alert('Error: " . $conn->error . "');</script>";
        }
    }
    $stmt->close();
}

// Handle login
if (isset($_POST['login'])) {
    $username = trim($_POST['log_username']);
    $password = $_POST['log_password'];

    $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['username'] = $username;
            header("Location: buy-now.html");
            exit();
        } else {
            echo "<script>alert('Invalid password.');</script>";
        }
    } else {
        echo "<script>alert('No user found with that username.');</script>";
    }
    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentication - Bamboo Bowl</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 300px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #3e5216;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #4a6a1e;
        }
        .toggle-btn {
            background: none;
            border: none;
            color: #3e5216;
            cursor: pointer;
            text-decoration: underline;
            margin-top: 10px;
        }
        .form-container {
            display: none;
        }
        .active {
            display: block;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 id="formTitle">Login</h2>
        
        <!-- Login Form -->
        <div id="loginForm" class="form-container active">
            <form method="POST" action="">
                <input type="text" name="log_username" placeholder="Username" required>
                <input type="password" name="log_password" placeholder="Password" required>
                <button type="submit" name="login">Login</button>
            </form>
            <button class="toggle-btn" onclick="toggleForms()">Don't have an account? Register</button>
        </div>

        <!-- Registration Form -->
        <div id="registerForm" class="form-container">
            <form method="POST" action="">
                <input type="text" name="reg_username" placeholder="Username" required>
                <input type="password" name="reg_password" placeholder="Password" required>
                <button type="submit" name="register">Register</button>
            </form>
            <button class="toggle-btn" onclick="toggleForms()">Already have an account? Login</button>
        </div>
    </div>

    <script>
        function toggleForms() {
            const loginForm = document.getElementById('loginForm');
            const registerForm = document.getElementById('registerForm');
            const formTitle = document.getElementById('formTitle');

            if (loginForm.classList.contains('active')) {
                loginForm.classList.remove('active');
                registerForm.classList.add('active');
                formTitle.textContent = 'Register';
            } else {
                registerForm.classList.remove('active');
                loginForm.classList.add('active');
                formTitle.textContent = 'Login';
            }
        }
    </script>
</body>
</html>
