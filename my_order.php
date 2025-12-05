<?php
session_start();
include("db_connect.php");
require("fpdf/fpdf.php");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// ---------------- PDF Generation Function ----------------
function generateBill($conn, $order_id, $user_id){
    $sql = "SELECT o.order_id, o.quantity, o.total_price, o.order_status, o.created_at, o.payment_method,
                   o.customer_name, o.customer_phone, o.customer_address, o.customer_city, o.customer_pincode,
                   p.name AS product_name, p.price AS unit_price
            FROM orders o
            JOIN products p ON o.product_id = p.product_id
            WHERE o.order_id=$order_id AND o.user_id=$user_id";
    $result = $conn->query($sql);
    if($result->num_rows==0) exit("Order not found.");
    $order = $result->fetch_assoc();
    if($order['order_status'] != "Delivered") exit("Invoice can only be generated for delivered orders.");

    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont("Arial","B",20);
    $pdf->Cell(0,10,"EcoKart",0,1,"C");
    $pdf->SetFont("Arial","I",12);
    $pdf->Cell(0,10,"Your one-stop bamboo shopping website",0,1,"C");
    $pdf->Ln(10);

    // Order info
    $pdf->SetFont("Arial","B",14);
    $pdf->Cell(0,10,"Order Details:",0,1);

    // Table header
    $pdf->SetFont("Arial","B",12);
    $pdf->Cell(80,10,"Product Name",1);
    $pdf->Cell(30,10,"Qty",1,0,'C');
    $pdf->Cell(40,10,"Price/Unit",1,0,'R');
    $pdf->Cell(40,10,"Total",1,1,'R');

    // Table content
    $pdf->SetFont("Arial","",12);
    $pdf->Cell(80,10,$order['product_name'],1);
    $pdf->Cell(30,10,$order['quantity'],1,0,'C');
    $pdf->Cell(40,10,"₹".$order['unit_price'],1,0,'R');
    $pdf->Cell(40,10,"₹".$order['total_price'],1,1,'R');

    $pdf->Ln(5);
    $pdf->Cell(0,8,"Customer: ".$order['customer_name'],0,1);
    $pdf->Cell(0,8,"Phone: ".$order['customer_phone'],0,1);
    $pdf->Cell(0,8,"Address: ".$order['customer_address'].", ".$order['customer_city']." - ".$order['customer_pincode'],0,1);
    $pdf->Cell(0,8,"Order Date: ".$order['created_at'],0,1);
    $pdf->Cell(0,8,"Payment: ".$order['payment_method'],0,1);

    $pdf->Ln(10);
    $pdf->SetFont("Arial","I",12);
    $pdf->Cell(0,10,"Thank you for shopping with EcoKart!",0,1,"C");

    $pdf->Output("D","Invoice_Order_".$order['order_id'].".pdf");
    exit;
}

// ---------------- Handle Cancel Order -----------------
if(isset($_POST['cancel_order']) && isset($_POST['order_id'])){
    $order_id = intval($_POST['order_id']);
    $conn->query("UPDATE orders SET order_status='Cancelled' WHERE order_id=$order_id AND user_id=$user_id");
}

// ---------------- Handle PDF Download -----------------
if(isset($_POST['download_invoice']) && isset($_POST['order_id'])){
    $order_id = intval($_POST['order_id']);
    generateBill($conn, $order_id, $user_id);
}

// ---------------- Fetch all orders -----------------
$stmt = $conn->prepare("
    SELECT o.order_id, o.quantity, o.total_price, o.order_status,
           o.created_at, o.payment_method,
           o.customer_name, o.customer_phone, o.customer_address,
           o.customer_city, o.customer_pincode,
           p.name AS product_name, p.image AS product_image
    FROM orders o
    JOIN products p ON o.product_id = p.product_id
    WHERE o.user_id = ?
    ORDER BY o.created_at DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();

// Organize orders by order_id
$orders = [];
while($row = $res->fetch_assoc()){
    $oid = $row['order_id'];
    if(!isset($orders[$oid])){
        $orders[$oid] = ['info'=>$row, 'products'=>[]];
    }
    $orders[$oid]['products'][] = [
        'product_name'=>$row['product_name'],
        'product_image'=>$row['product_image'],
        'quantity'=>$row['quantity'],
        'total_price'=>$row['total_price']
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Orders</title>
<style>
body { font-family: Arial, sans-serif; background: #f8f8f8; padding: 20px; }
h2 { text-align:center; color:#333; }
.tabs { display:flex; justify-content:center; margin-bottom:20px; }
.tabs button { padding:10px 20px; margin:0 5px; border:none; border-bottom:2px solid transparent; cursor:pointer; background:#ccc; }
.tabs button.active { border-bottom:2px solid darkolivegreen; font-weight:bold; }
.order-box { background: beige; padding: 15px; margin: 10px auto; border-radius: 10px; max-width: 700px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
.order-box h3 { margin: 0 0 5px 0; color: darkolivegreen; }
.order-box ul { list-style: none; padding-left: 0; margin-top:5px; }
.order-box li { margin: 5px 0; display:flex; align-items:center; }
.order-box li img { width:50px; height:50px; margin-right:10px; border:1px solid #ccc; border-radius:5px; cursor:pointer; }
.info { font-size: 14px; color:#555; margin-top:5px; }
.status { font-weight: bold; color: darkblue; margin-top:5px; }
.download-btn, .cancel-btn { margin-top:8px; }
.download-btn button, .cancel-btn button { padding:8px 12px; border:none; background:olive; color:white; border-radius:5px; cursor:pointer; }
.download-btn button:hover, .cancel-btn button:hover { background: darkolivegreen; }
.back-btn { text-align:center; margin:20px; }
.back-btn button { padding: 10px 20px; border: none; background: olive; color: white; border-radius: 8px; cursor: pointer; }
.back-btn button:hover { background: darkolivegreen; }
</style>
<script>
function showTab(tab) {
    let sections = ['orders','history','cancelled'];
    sections.forEach(s => document.getElementById(s).style.display='none');
    document.getElementById(tab).style.display='block';
    document.querySelectorAll('.tabs button').forEach(b=>b.classList.remove('active'));
    document.getElementById(tab+'-btn').classList.add('active');
}
</script>
</head>
<body>

<h2>📦 My Orders</h2>
<div class="tabs">
    <button id="orders-btn" class="active" onclick="showTab('orders')">Orders</button>
    <button id="history-btn" onclick="showTab('history')">History</button>
    <button id="cancelled-btn" onclick="showTab('cancelled')">Cancelled</button>
</div>

<!-- Orders Tab -->
<div id="orders">
<?php 
$hasOrders = false;
foreach($orders as $oid=>$o):
    if(strtolower($o['info']['order_status'])=='delivered' || strtolower($o['info']['order_status'])=='cancelled') continue;
    $hasOrders = true;
?>
<div class="order-box">
    <h3>Order #<?php echo $oid;?> | Date: <?php echo $o['info']['created_at'];?></h3>
    <ul>
    <?php foreach($o['products'] as $p): ?>
        <li>
            <?php if(!empty($p['product_image']) && file_exists($p['product_image'])): ?>
                <a href="product_detail.php?id=<?php echo $p['product_name']; ?>"><img src="<?php echo $p['product_image'];?>"></a>
            <?php endif; ?>
            <?php echo $p['product_name'];?> × <?php echo $p['quantity'];?> → ₹<?php echo $p['total_price'];?>
        </li>
    <?php endforeach; ?>
    </ul>
    <div class="info"><b>Customer:</b> <?php echo $o['info']['customer_name'];?>, <?php echo $o['info']['customer_phone'];?><br>
    <?php echo $o['info']['customer_address'];?>, <?php echo $o['info']['customer_city'];?> - <?php echo $o['info']['customer_pincode'];?></div>
    <div class="status">Status: <?php echo $o['info']['order_status'];?></div>
    <div class="cancel-btn">
        <form method="POST">
            <input type="hidden" name="order_id" value="<?php echo $oid;?>">
            <button type="submit" name="cancel_order">Cancel Order</button>
        </form>
    </div>
</div>
<?php endforeach; 
if(!$hasOrders) echo "<p style='text-align:center; color:#555;'>No pending orders yet.</p>";
?>
</div>

<!-- History Tab -->
<div id="history" style="display:none;">
<?php 
$hasHistory = false;
foreach($orders as $oid=>$o):
    if(strtolower($o['info']['order_status'])!='delivered') continue;
    $hasHistory = true;
?>
<div class="order-box">
    <h3>Order #<?php echo $oid;?> | Date: <?php echo $o['info']['created_at'];?></h3>
    <ul>
    <?php foreach($o['products'] as $p): ?>
        <li>
            <?php if(!empty($p['product_image']) && file_exists($p['product_image'])): ?>
                <a href="product_detail.php?id=<?php echo $p['product_name']; ?>"><img src="<?php echo $p['product_image'];?>"></a>
            <?php endif; ?>
            <?php echo $p['product_name'];?> × <?php echo $p['quantity'];?> → ₹<?php echo $p['total_price'];?>
        </li>
    <?php endforeach; ?>
    </ul>
    <div class="info"><b>Customer:</b> <?php echo $o['info']['customer_name'];?>, <?php echo $o['info']['customer_phone'];?><br>
    <?php echo $o['info']['customer_address'];?>, <?php echo $o['info']['customer_city'];?> - <?php echo $o['info']['customer_pincode'];?></div>
    <div class="status">Status: <?php echo $o['info']['order_status'];?></div>
    <div class="download-btn">
        <form method="POST">
            <input type="hidden" name="order_id" value="<?php echo $oid;?>">
            <button type="submit" name="download_invoice">Download Invoice (PDF)</button>
        </form>
    </div>
</div>
<?php endforeach;
if(!$hasHistory) echo "<p style='text-align:center; color:#555;'>No delivered orders yet.</p>";
?>
</div>

<!-- Cancelled Tab -->
<div id="cancelled" style="display:none;">
<?php 
$hasCancelled = false;
foreach($orders as $oid=>$o):
    if(strtolower($o['info']['order_status'])!='cancelled') continue;
    $hasCancelled = true;
?>
<div class="order-box">
    <h3>Order #<?php echo $oid;?> | Date: <?php echo $o['info']['created_at'];?></h3>
    <ul>
    <?php foreach($o['products'] as $p): ?>
        <li>
            <?php if(!empty($p['product_image']) && file_exists($p['product_image'])): ?>
                <a href="product_detail.php?id=<?php echo $p['product_name']; ?>"><img src="<?php echo $p['product_image'];?>"></a>
            <?php endif; ?>
            <?php echo $p['product_name'];?> × <?php echo $p['quantity'];?> → ₹<?php echo $p['total_price'];?>
        </li>
    <?php endforeach; ?>
    </ul>
    <div class="info"><b>Customer:</b> <?php echo $o['info']['customer_name'];?>, <?php echo $o['info']['customer_phone'];?><br>
    <?php echo $o['info']['customer_address'];?>, <?php echo $o['info']['customer_city'];?> - <?php echo $o['info']['customer_pincode'];?></div>
    <div class="status">Status: <?php echo $o['info']['order_status'];?></div>
</div>
<?php endforeach;
if(!$hasCancelled) echo "<p style='text-align:center; color:#555;'>No cancelled orders yet.</p>";
?>
</div>

<div class="back-btn">
    <button onclick="window.location.href='product.html'">← Continue Shopping</button>
</div>

</body>
</html>
