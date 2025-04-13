<?php
// Set page title
$page_title = "My Bids - BidPulse";

// Include database connection and functions
require_once "includes/db.php";
require_once "includes/functions.php";

// Check if the user is logged in
if (!is_logged_in()) {
    $_SESSION["error_message"] = "You must be logged in to view your bids.";
    redirect("login.php");
}

// Get user info
$user_id = $_SESSION["id"];
$username = $_SESSION["username"];

// Fetch user bids
$sql = "SELECT 
            b.id AS bid_id, 
            b.bid_amount AS bid_amount, 
            b.bid_time AS bid_time, 
            p.id AS item_id, 
            p.name AS item_title, 
            p.price AS current_price, 
            p.image_url, 
            DATE_ADD(p.created_at, INTERVAL p.duration DAY) AS end_time
        FROM bids b
        JOIN products p ON b.item_id = p.id
        WHERE b.user_id = ?
        ORDER BY b.bid_time DESC";

$stmt = $db->prepare($sql);
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    $result = $stmt->get_result();
    $user_bids = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'data' => $user_bids]);
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Failed to fetch bids.']);
}
exit();

// Include header
include "includes/header.php";
?>

<div class="py-8">
    <div class="container mx-auto px-4">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-gradient-to-r from-primary to-secondary text-white p-6">
                <div class="flex flex-col md:flex-row items-center">
                    <div class="w-24 h-24 bg-white text-primary rounded-full flex items-center justify-center text-4xl font-bold mb-4 md:mb-0 md:mr-6">
                        <?php echo strtoupper(substr($username, 0, 2)); ?>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold"><?php echo htmlspecialchars($username); ?></h1>
                        <p class="text-white/80">Member since <?php echo date('F Y'); ?></p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="flex flex-wrap mb-6">
                    <a href="profile.php" class="px-4 py-2 text-gray-600 hover:text-primary">Profile</a>
                    <a href="my_bids.php" class="px-4 py-2 font-semibold text-primary border-b-2 border-primary">My Bids</a>
                    <a href="my_items.php" class="px-4 py-2 text-gray-600 hover:text-primary">My Items</a>
                </div>

                <div>
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">My Bidding History</h2>

                    <?php if (empty($user_bids)): ?>
                        <div class="bg-gray-50 rounded-lg p-6 text-center">
                            <i class="fas fa-gavel text-5xl text-gray-300 mb-4"></i>
                            <h3 class="text-xl font-bold text-gray-800 mb-2">No Bidding Activity Yet</h3>
                            <p class="text-gray-600 mb-4">You haven't placed any bids yet.</p>
                            <a href="auctions.php" class="btn-primary">Browse Auctions</a>
                        </div>
                    <?php else: ?>
                        <!-- Bid List -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bid Amount</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Price</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($user_bids as $bid):
                                        $is_highest = $bid['bid_amount'] >= $bid['current_price'];
                                        $auction_ended = strtotime($bid['end_time']) < time();
                                    ?>
                                        <tr>
                                            <td class="px-6 py-4">
                                                <a href="item.php?id=<?php echo $bid['item_id']; ?>" class="text-primary hover:underline font-medium"><?php echo htmlspecialchars($bid['item_title']); ?></a>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900">
                                                <?php echo format_currency($bid['bid_amount']); ?>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900">
                                                <?php echo format_currency($bid['current_price']); ?>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500">
                                                <?php echo date('M d, Y g:i A', strtotime($bid['bid_time'])); ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <?php if ($auction_ended): ?>
                                                    <?php if ($is_highest): ?>
                                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                            Won
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                            Lost
                                                        </span>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <?php if ($is_highest): ?>
                                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                            Highest Bid
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                            Outbid
                                                        </span>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="item.php?id=<?php echo $bid['item_id']; ?>" class="text-primary hover:text-secondary transition-colors">
                                                    View Item
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>