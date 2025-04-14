<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start output buffering to prevent unexpected output
ob_start();

require_once "./includes/db.php";
session_start();

header('Content-Type: application/json');

// Check if the user is logged in
if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to place a bid.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Decode the JSON payload
    $data = json_decode(file_get_contents('php://input'), true);

    $user_id = $_SESSION['id'];
    $item_id = isset($data['id']) ? (int)$data['id'] : null;
    $bid_amount = isset($data['bid_amount']) ? (float)$data['bid_amount'] : null;

    // Validate input
    if (!$item_id || !$bid_amount) {
        echo json_encode(['success' => false, 'message' => 'Invalid input.']);
        exit();
    }

    // Fetch the current highest bid for the item
    $stmt = $db->prepare("SELECT price FROM products WHERE id = ?");
    if (!$stmt) {
        error_log("Error preparing SELECT statement: " . $db->error);
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $db->error]);
        exit();
    }
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();

    if (!$product) {
        echo json_encode(['success' => false, 'message' => 'Item not found.']);
        exit();
    }

    if ($bid_amount <= $product['price']) {
        echo json_encode(['success' => false, 'message' => 'Bid amount must be higher than the current price.']);
        exit();
    }

    // Insert the bid into the bids table
// Prepare the INSERT statement
$stmt = $db->prepare("INSERT INTO bids (user_id, item_id, bid_amount, bid_time) VALUES (?, ?, ?, NOW())");

if (!$stmt) {
    error_log("Error preparing INSERT statement: " . $db->error);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $db->error]);
    exit();
}

// Bind parameters for user_id, item_id, and bid_amount (types: i = integer, d = double)
$stmt->bind_param("iid", $user_id, $item_id, $bid_amount);

if (!$stmt->execute()) {
    error_log("Error executing INSERT statement: " . $stmt->error);
    echo json_encode(['success' => false, 'message' => 'Failed to place bid.']);
    $stmt->close();
    exit();
}

$stmt->close();


    // Update the product's current price
    $stmt = $db->prepare("UPDATE products SET price = ? WHERE id = ?");
    if (!$stmt) {
        error_log("Error preparing UPDATE statement: " . $db->error);
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $db->error]);
        exit();
    }
    $stmt->bind_param("di", $bid_amount, $item_id);
    if (!$stmt->execute()) {
        error_log("Error executing UPDATE statement: " . $stmt->error);
        echo json_encode(['success' => false, 'message' => 'Failed to update product price.']);
        $stmt->close();
        exit();
    }
    $stmt->close();

    echo json_encode(['success' => true, 'message' => 'Bid placed successfully.']);
    exit();
}

echo json_encode(['success' => false, 'message' => 'Invalid request.']);