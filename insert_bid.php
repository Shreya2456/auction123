<?php
// Enable error reporting for debugging
header('Content-Type: application/json');   
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start output buffering to prevent unexpected output
ob_start();

require_once "../includes/db.php";
session_start();
$data=json_decode(file_get_contents('php://input'),true);
$user_id = $data['user_id'] ?? null;
$item_id = $data['item_id'] ?? null;
$bid_amount = $data['bid_amount'] ?? null;

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
