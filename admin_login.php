<?php
session_start();

$host = "localhost";
$user = "root";
$pass = "";
$db = "bamboo";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

if(isset($_POST['login'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM admin WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();

    if($res->num_rows == 1){
        $admin = $res->fetch_assoc();
        if(password_verify($password, $admin['password'])){
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_name'] = $admin['username'];

            // ✅ Alert + redirect to admin.php
            echo "<script>
                    alert('✅ Login successful!');
                    window.location.href = 'admin.php';
                  </script>";
            exit;
        } else {
            $error = "❌ Incorrect password!";
        }
    } else {
        $error = "⚠️ Admin not found!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Login</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background: #f4f4f9;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
    }
    .login-box {
        background: #fff;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        width: 350px;
        text-align: center;
    }
    h2 {
        color: darkolivegreen;
        margin-bottom: 20px;
    }
    input[type="text"], input[type="password"] {
        width: 90%;
        padding: 10px;
        margin: 8px 0;
        border: 1px solid #ccc;
        border-radius: 8px;
        outline: none;
    }
    input[type="submit"] {
        background: olive;
        color: white;
        border: none;
        border-radius: 8px;
        padding: 10px 20px;
        cursor: pointer;
        margin-top: 10px;
        width: 100%;
        font-weight: bold;
    }
    input[type="submit"]:hover {
        background: darkolivegreen;
    }
    .error {
        color: red;
        margin-bottom: 10px;
        font-size: 14px;
    }
    .register-link {
        display: block;
        margin-top: 15px;
        font-size: 14px;
        color: darkolivegreen;
        text-decoration: none;
    }
    .register-link:hover {
        text-decoration: underline;
    }
</style>
</head>
<body>

<div class="login-box">
    <h2>🔑 Admin Login</h2>
    <?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>
    <form method="POST">
        <input type="text" name="username" placeholder="Enter Username" required><br>
        <input type="password" name="password" placeholder="Enter Password" required><br>
        <input type="submit" name="login" value="Login">
    </form>

    <a href="register.php" class="register-link">Not registered? Register here</a>
</div>

</body>
</html>
