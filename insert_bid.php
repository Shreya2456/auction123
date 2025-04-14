<?php

require_once "./includes/db.php";

session_start();


header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

// Check login
if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to place a bid.']);
    exit;
}

$user_id = $_SESSION['id']; // Always use from session
$item_id = isset($data['item_id']) ? (int)$data['item_id'] : null;  // Cast to integer
$bid_amount = isset($data['bid_amount']) ? (float)$data['bid_amount'] : null;  // Cast to float

$stmt = $db->prepare("INSERT INTO bids (user_id, item_id, bid_amount, bid_time) VALUES (?, ?, ?, NOW())");
if (!$stmt) {
    error_log("Error preparing INSERT statement: " . $db->error);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $db->error]);
    exit();
}
$stmt->bind_param("iid", $user_id, $item_id, $bid_amount);
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Bid placed successfully.']);
} else {
    error_log("Error executing INSERT statement: " . $stmt->error);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
}
$stmt->close();
$db->close();
