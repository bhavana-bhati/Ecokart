<?php
session_start();
include("db_connect.php");

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle updates from forms
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['field'])) {
    $field = $_POST['field'];
    $value = trim($_POST['value']);
    if ($value !== "") {
        if (in_array($field, ['username', 'phone', 'password'])) {
            if ($field == 'password') {
                $value = password_hash($value, PASSWORD_DEFAULT);
            }
            $stmt = $conn->prepare("UPDATE users SET $field = ? WHERE id = ?");
            $stmt->bind_param("si", $value, $user_id);
        } else {
            $db_field = "customer_" . $field;
            $stmt = $conn->prepare("UPDATE orders SET $db_field = ? WHERE user_id = ?");
            $stmt->bind_param("si", $value, $user_id);
        }
        $stmt->execute();
    }
}

// Fetch user data
$user_sql = $conn->prepare("SELECT username, phone, password FROM users WHERE id = ?");
$user_sql->bind_param("i", $user_id);
$user_sql->execute();
$user_res = $user_sql->get_result()->fetch_assoc();

// Latest address info
$order_sql = $conn->prepare("
    SELECT customer_address, customer_city, customer_pincode 
    FROM orders 
    WHERE user_id = ? 
    ORDER BY order_id DESC LIMIT 1
");
$order_sql->bind_param("i", $user_id);
$order_sql->execute();
$order_res = $order_sql->get_result()->fetch_assoc();

$address = $order_res['customer_address'] ?? "Not Provided";
$city = $order_res['customer_city'] ?? "Not Provided";
$pincode = $order_res['customer_pincode'] ?? "Not Provided";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Account</title>
<style>
body { 
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
    background: #eef2f7; 
    margin:0; 
    padding:40px; 
}
.container {
    max-width: 850px;
    margin:auto;
    background: beige;
    padding: 40px 50px;
    border-radius: 20px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}
h2 { 
    text-align:center; 
    color: darkolivegreen; 
    margin-bottom: 40px;
    font-size: 32px;
}
.section {
    margin: 25px 0;
    padding: 20px 25px;
    border-radius: 12px;
    background: #fdf5e6;
    box-shadow: 0 3px 10px rgba(0,0,0,0.08);
}

.section label {
    font-weight: bold;
    font-size: 22px;             
    font-family: 'Arial Black', Gadget, sans-serif;
    color: black;       
}

.section span { 
    font-size: 17px; 
    color:#333; 
    display:block; 
    margin-top:8px; 
}
.manage-btn {
    background: darkolivegreen;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 8px;
    cursor: pointer;
    margin-top:10px;
    font-size: 14px;
}
.manage-btn:hover { background: #556B2F; }
.edit-form { display: none; margin-top: 10px; }
input[type="text"], input[type="password"] {
    padding: 8px;
    border: 1px solid #aaa;
    border-radius: 8px;
    width: 100%;
    max-width: 400px;
    margin-bottom:8px;
    font-size:16px;
}
.update-btn {
    background: darkolivegreen;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 15px;
}
.update-btn:hover { background: #556B2F; }
</style>
<script>
function toggleEdit(field) {
    const form = document.getElementById(field + '-form');
    form.style.display = form.style.display === 'none' || form.style.display === '' ? 'block' : 'none';
}
</script>
</head>
<body>
<div class="container">
    <h2>👤 My Account</h2>

    <?php
    $fields = [
        'username'=>'Name',
        'phone'=>'Phone',
        'password'=>'Password',
        'address'=>'Address',
        'city'=>'City',
        'pincode'=>'Pincode'
    ];
    $values = [
        'username'=>$user_res['username'],
        'phone'=>$user_res['phone'],
        'password'=>'********',
        'address'=>$address,
        'city'=>$city,
        'pincode'=>$pincode
    ];
    foreach($fields as $key => $label):
    ?>
    <div class="section">
        <label><?php echo $label; ?></label>
        <span id="<?php echo $key; ?>-display"><?php echo htmlspecialchars($values[$key]); ?></span>
        <button class="manage-btn" onclick="toggleEdit('<?php echo $key; ?>')">Manage</button>
        <form method="post" id="<?php echo $key; ?>-form" class="edit-form">
            <input type="<?php echo $key=='password'?'password':'text'; ?>" name="value" placeholder="Enter new <?php echo strtolower($label); ?>">
            <input type="hidden" name="field" value="<?php echo $key; ?>">
            <button type="submit" class="update-btn">Update</button>
        </form>
    </div>
    <?php endforeach; ?>

</div>
</body>
</html>
