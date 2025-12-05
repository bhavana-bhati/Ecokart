<?php
session_start();
if(!isset($_SESSION['admin_logged_in'])){
    header("Location: admin_register.php");
    exit;
}

$host = "localhost"; 
$user = "root";      
$pass = "";          
$db   = "bamboo";  

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// --- UPDATE ORDER STATUS ---
if(isset($_POST['update_order'])){
    $order_id = $_POST['order_id'];
    $new_status = $_POST['new_status'];
    $conn->query("UPDATE orders SET order_status='$new_status' WHERE order_id=$order_id");
    if($new_status == "Delivered"){
        generateBill($conn, $order_id); exit;
    }
    echo "<meta http-equiv='refresh' content='0'>";
}

// --- CANCEL ORDER ---
if(isset($_POST['cancel_order'])){
    $order_id = $_POST['order_id'];
    $conn->query("DELETE FROM orders WHERE order_id=$order_id");
    echo "<meta http-equiv='refresh' content='0'>";
}

// --- DOWNLOAD BILL ---
if(isset($_POST['download_bill'])){
    $order_id = $_POST['order_id'];
    generateBill($conn, $order_id); exit;
}

function generateBill($conn, $order_id){
    require("fpdf/fpdf.php");

    $sql = "SELECT o.order_id, u.username, u.address, u.phone,
                   p.name AS product_name, o.quantity, o.total_price, o.created_at
            FROM orders o
            JOIN users u ON o.user_id = u.id
            JOIN products p ON o.product_id = p.product_id
            WHERE o.order_id=$order_id";

    $result = $conn->query($sql);

    if(!$result){
        die("Error fetching order: " . $conn->error);
    }

    $order = $result->fetch_assoc();

    if(!$order){
        die("Order not found!");
    }

    $pdf = new FPDF();
    $pdf->AddPage();

    // Header
    $pdf->SetFont("Arial","B",20);
    $pdf->Cell(0,10,"EcoKart",0,1,"C");
    $pdf->SetFont("Arial","I",12);
    $pdf->Cell(0,10,"Your one-stop bamboo shopping website",0,1,"C");
    $pdf->Ln(10);

    // Customer info
    $pdf->SetFont("Arial","B",14);
    $pdf->Cell(0,10,"Customer Details:",0,1);
    $pdf->SetFont("Arial","",12);
    $pdf->Cell(100,8,"Name: ".$order['username'],0,1);
    $pdf->Cell(100,8,"Phone: ".$order['phone'],0,1);
    $pdf->Cell(100,8,"Address: ".$order['address'],0,1);
    $pdf->Ln(5);

    // Order info
    $pdf->SetFont("Arial","B",14);
    $pdf->Cell(0,10,"Order Details:",0,1);

    $pdf->SetFont("Arial","B",12);
    $pdf->Cell(60,10,"Product",1);
    $pdf->Cell(30,10,"Qty",1);
    $pdf->Cell(40,10,"Price/Unit",1);
    $pdf->Cell(40,10,"Total",1,1);

    $pdf->SetFont("Arial","",12);
    $pdf->Cell(60,10,$order['product_name'],1);
    $pdf->Cell(30,10,$order['quantity'],1);
    $pdf->Cell(40,10,"₹".($order['total_price']/$order['quantity']),1);
    $pdf->Cell(40,10,"₹".$order['total_price'],1,1);

    $pdf->Ln(10);
    $pdf->Cell(0,10,"Order Date: ".$order['created_at'],0,1);
    $pdf->Ln(10);

    // Footer
    $pdf->SetFont("Arial","I",12);
    $pdf->Cell(0,10,"Thank you for shopping with EcoKart!",0,1,"C");

    $pdf->Output("D","Bill_Order_".$order['order_id'].".pdf");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard</title>
<style>
    body { font-family: Arial, sans-serif; margin: 20px; background: beige; }
    h1 { text-align: center; color: darkolivegreen; }
    h2 { color: #333; margin-top: 40px; }
    table { width: 100%; border-collapse: collapse; margin: 20px 0; background: #fff; }
    table, th, td { border: 1px solid #ccc; }
    th, td { padding: 10px; text-align: center; }
    th { background: darkolivegreen; color: #fff; }
    tr:nth-child(even) { background: #f9f9f9; }
    button, select { padding: 5px 10px; margin: 2px; }
</style>
</head>
<body>

<h1>Admin Dashboard</h1>

<!-- USERS -->
<h2>Users</h2>
<table>
<tr>
    <th>ID</th>
    <th>Username</th>
    <th>Phone</th>
    <th>Address</th>
</tr>
<?php
$result = $conn->query("SELECT * FROM users");
while($row = $result->fetch_assoc()){
    echo "<tr>
            <td>{$row['id']}</td>
            <td>{$row['username']}</td>
            <td>{$row['phone']}</td>
            <td>{$row['address']}</td>
          </tr>";
}
?>
</table>

<!-- PRODUCTS -->
<h2>Products</h2>
<table>
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Category</th>
    <th>Price</th>
</tr>
<?php
$result = $conn->query("SELECT * FROM products");
while($row = $result->fetch_assoc()){
    echo "<tr>
            <td>{$row['product_id']}</td>
            <td>{$row['name']}</td>
            <td>{$row['category']}</td>
            <td>₹{$row['price']}</td>
          </tr>";
}
?>
</table>

<!-- ORDERS -->
<h2>Orders</h2>
<table>
<tr>
    <th>Order ID</th>
    <th>User ID</th>
    <th>Customer Name</th>
    <th>Phone</th>
    <th>Address</th>
    <th>City</th>
    <th>Pincode</th>
    <th>Product</th>
    <th>Quantity</th>
    <th>Total Price</th>
    <th>Status</th>
    <th>Payment</th>
    <th>Date</th>
    <th>Actions</th>
</tr>
<?php
$sql = "SELECT o.order_id, o.user_id, o.customer_name, o.customer_phone, 
               o.customer_address, o.customer_city, o.customer_pincode,
               p.name AS product_name, o.quantity, o.total_price, 
               o.order_status, o.payment_method, o.created_at
        FROM orders o
        JOIN products p ON o.product_id = p.product_id";

$result = $conn->query($sql);
while($row = $result->fetch_assoc()){
    echo "<tr>
            <td>{$row['order_id']}</td>
            <td>{$row['user_id']}</td>
            <td>{$row['customer_name']}</td>
            <td>{$row['customer_phone']}</td>
            <td>{$row['customer_address']}</td>
            <td>{$row['customer_city']}</td>
            <td>{$row['customer_pincode']}</td>
            <td>{$row['product_name']}</td>
            <td>{$row['quantity']}</td>
            <td>₹{$row['total_price']}</td>
            <td>{$row['order_status']}</td>
            <td>{$row['payment_method']}</td>
            <td>{$row['created_at']}</td>
            <td>
                <form method='POST' style='display:inline;'>
                    <input type='hidden' name='order_id' value='{$row['order_id']}'>
                    <select name='new_status'>
                        <option value='Pending' ".($row['order_status']=="Pending"?"selected":"").">Pending</option>
                        <option value='Delivered' ".($row['order_status']=="Delivered"?"selected":"").">Delivered</option>
                    </select>
                    <button type='submit' name='update_order'>Update</button>
                </form>
                <form method='POST' style='display:inline;'>
                    <input type='hidden' name='order_id' value='{$row['order_id']}'>
                    <button type='submit' name='cancel_order' onclick=\"return confirm('Are you sure to cancel this order?')\">Cancel</button>
                </form>";
    if($row['order_status'] == "Delivered"){
        echo "<form method='POST' style='display:inline;'>
                  <input type='hidden' name='order_id' value='{$row['order_id']}'>
                  <button type='submit' name='download_bill'>Download Bill</button>
              </form>";
    }
    echo "</td></tr>";
}
?>
</table>

<!-- WISHLIST -->
<h2>Wishlist</h2>
<table>
<tr>
    <th>Wishlist ID</th>
    <th>User</th>
    <th>Product</th>
    <th>Added At</th>
</tr>
<?php
$sql = "SELECT w.wishlist_id, u.username, p.name AS product_name, w.added_at
        FROM wishlist w
        JOIN users u ON w.user_id = u.id
        JOIN products p ON w.product_id = p.product_id";
$result = $conn->query($sql);

while($row = $result->fetch_assoc()){
    echo "<tr>
            <td>{$row['wishlist_id']}</td>
            <td>{$row['username']}</td>
            <td>{$row['product_name']}</td>
            <td>{$row['added_at']}</td>
          </tr>";
}
?>
</table>

<!-- CART -->
<h2>Cart</h2>
<table>
<tr>
    <th>Cart ID</th>
    <th>User</th>
    <th>Product</th>
    <th>Quantity</th>
    <th>Added At</th>
</tr>
<?php
$sql = "SELECT c.cart_id, u.username, p.name AS product_name, c.quantity, c.added_at
        FROM cart c
        JOIN users u ON c.user_id = u.id
        JOIN products p ON c.product_id = p.product_id";
$result = $conn->query($sql);

while($row = $result->fetch_assoc()){
    echo "<tr>
            <td>{$row['cart_id']}</td>
            <td>{$row['username']}</td>
            <td>{$row['product_name']}</td>
            <td>{$row['quantity']}</td>
            <td>{$row['added_at']}</td>
          </tr>";
}
?>
</table>

</body>
</html>
