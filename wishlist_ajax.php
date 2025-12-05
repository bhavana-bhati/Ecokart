<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Not logged in"]);
    exit();
}

$user_id = $_SESSION['user_id'];

$conn = new mysqli("localhost", "root", "", "bamboo");
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "DB connection failed"]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = intval($_POST['product_id']);

    // ✅ Check if already in wishlist
    $check = $conn->prepare("SELECT wishlist_id FROM wishlist WHERE user_id = ? AND product_id = ?");
    $check->bind_param("ii", $user_id, $product_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        // ✅ Remove from wishlist
        $delete = $conn->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
        $delete->bind_param("ii", $user_id, $product_id);
        $delete->execute();
        echo json_encode(["status" => "removed"]);
    } else {
        // ✅ Add to wishlist
        $insert = $conn->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)");
        $insert->bind_param("ii", $user_id, $product_id);
        $insert->execute();
        echo json_encode(["status" => "added"]);
    }
    exit();
}

echo json_encode(["status" => "error", "message" => "Invalid request"]);
