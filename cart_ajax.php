<?php
session_start();
include("db_connect.php");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Not logged in"]);
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    if ($action === "update") {
        $cart_id = intval($_POST['cart_id']);
        $qty = max(1, intval($_POST['quantity']));
        $stmt = $conn->prepare("UPDATE cart SET quantity=? WHERE cart_id=? AND user_id=?");
        $stmt->bind_param("iii", $qty, $cart_id, $user_id);
        $stmt->execute();

        $stmt = $conn->prepare("SELECT p.price, (p.price * c.quantity) as subtotal 
                                FROM cart c JOIN products p ON c.product_id=p.product_id 
                                WHERE c.cart_id=? AND c.user_id=?");
        $stmt->bind_param("ii", $cart_id, $user_id);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();

        echo json_encode(["status" => "updated", "subtotal" => $res['subtotal']]);
        exit();
    }

    if ($action === "remove") {
        $cart_id = intval($_POST['cart_id']);
        $stmt = $conn->prepare("DELETE FROM cart WHERE cart_id=? AND user_id=?");
        $stmt->bind_param("ii", $cart_id, $user_id);
        $stmt->execute();
        echo json_encode(["status" => "removed"]);
        exit();
    }
}
