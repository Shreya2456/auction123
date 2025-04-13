<?php
// Set page title
$page_title = "My Items - BidPulse";

// Include functions file
require_once "includes/functions.php";

// Check if the user is logged in
if (!is_logged_in()) {
    $_SESSION["error_message"] = "You must be logged in to view your items.";
    redirect("login.php");
}

// Get user info
$user_id = $_SESSION["id"];
$username = $_SESSION["username"];

// Get filter status from query parameter
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';

// Get user's auctions from the database
$auctions = [];
$sql = "SELECT a.*, c.name AS category_name
        FROM auctions a
        JOIN categories c ON a.category_id = c.id
        WHERE a.seller_id = ?";

// Add status filter to the query if applicable
if ($status_filter !== 'all') {
    $sql .= " AND a.status = ?";
}

$sql .= " ORDER BY a.end_date DESC";

if ($stmt = mysqli_prepare($conn, $sql)) {
    if ($status_filter !== 'all') {
        mysqli_stmt_bind_param($stmt, "is", $user_id, $status_filter);
    } else {
        mysqli_stmt_bind_param($stmt, "i", $user_id);
    }

    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);

        while ($row = mysqli_fetch_assoc($result)) {
            $auctions[] = $row;
        }
    }

    mysqli_stmt_close($stmt);
}

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
                    <a href="my_bids.php" class="px-4 py-2 text-gray-600 hover:text-primary">My Bids</a>
                    <a href="my_items.php" class="px-4 py-2 font-semibold text-primary border-b-2 border-primary">My Items</a>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-2xl font-bold text-gray-800">My Listed Auctions</h2>
                        <a href="add_item.php" class="btn-accent">
                            <i class="fas fa-plus mr-2"></i> List New Auction
                        </a>
                    </div>

                    <?php if (empty($auctions)): ?>
                        <div class="bg-gray-50 rounded-lg p-6 text-center">
                            <i class="fas fa-box-open text-5xl text-gray-300 mb-4"></i>
                            <h3 class="text-xl font-bold text-gray-800 mb-2">No Auctions Listed Yet</h3>
                            <p class="text-gray-600 mb-4">You haven't listed any auctions yet.</p>
                            <a href="add_item.php" class="btn-primary">List an Auction</a>
                        </div>
                    <?php else: ?>
                        <!-- Filter Options -->
                        <div class="bg-gray-50 p-4 rounded-lg mb-6">
                            <div class="flex flex-wrap items-center">
                                <span class="font-semibold text-gray-700 mr-4 mb-2 md:mb-0">Filter By:</span>
                                <div class="space-x-2">
                                    <a href="my_items.php?status=all" class="px-3 py-1 <?php echo $status_filter === 'all' ? 'bg-primary text-white' : 'bg-gray-200 hover:bg-gray-300 text-gray-700'; ?> rounded-full text-sm">All Auctions</a>
                                    <a href="my_items.php?status=ongoing" class="px-3 py-1 <?php echo $status_filter === 'ongoing' ? 'bg-primary text-white' : 'bg-gray-200 hover:bg-gray-300 text-gray-700'; ?> rounded-full text-sm">Ongoing</a>
                                    <a href="my_items.php?status=upcoming" class="px-3 py-1 <?php echo $status_filter === 'upcoming' ? 'bg-primary text-white' : 'bg-gray-200 hover:bg-gray-300 text-gray-700'; ?> rounded-full text-sm">Upcoming</a>
                                    <a href="my_items.php?status=expired" class="px-3 py-1 <?php echo $status_filter === 'expired' ? 'bg-primary text-white' : 'bg-gray-200 hover:bg-gray-300 text-gray-700'; ?> rounded-full text-sm">Expired</a>
                                </div>
                            </div>
                        </div>

                        <!-- Auctions Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            <?php foreach ($auctions as $auction): ?>
                                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                                    <div class="relative h-48 overflow-hidden">
                                        <img src="<?php echo htmlspecialchars($auction['image_url']); ?>" alt="<?php echo htmlspecialchars($auction['title']); ?>" class="w-full h-full object-cover">
                                        <?php if ($auction['status'] === 'expired'): ?>
                                            <div class="absolute top-0 bottom-0 left-0 right-0 bg-black/50 flex items-center justify-center">
                                                <span class="px-3 py-1 bg-red-500 text-white rounded-full text-sm font-semibold">Auction Ended</span>
                                            </div>
                                        <?php endif; ?>
                                        <div class="absolute top-0 right-0 bg-accent text-white px-3 py-1 rounded-bl-lg">
                                            <div class="flex items-center">
                                                <i class="fas fa-gavel mr-1"></i>
                                                <span><?php echo format_currency($auction['current_bid']); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="p-4">
                                        <div class="flex justify-between items-start mb-2">
                                            <h3 class="text-xl font-bold text-gray-800 hover:text-primary transition-colors">
                                                <a href="item.php?id=<?php echo $auction['id']; ?>"><?php echo htmlspecialchars($auction['title']); ?></a>
                                            </h3>
                                            <span class="bg-primary/10 text-primary text-sm px-2 py-1 rounded-full"><?php echo htmlspecialchars($auction['category_name']); ?></span>
                                        </div>
                                        <div class="mb-3">
                                            <div class="flex justify-between text-sm">
                                                <span class="text-gray-600">Starting Price:</span>
                                                <span class="font-semibold"><?php echo format_currency($auction['start_price']); ?></span>
                                            </div>
                                            <div class="flex justify-between text-sm">
                                                <span class="text-gray-600">Current Price:</span>
                                                <span class="font-semibold"><?php echo format_currency($auction['current_bid']); ?></span>
                                            </div>
                                            <div class="flex justify-between text-sm">
                                                <span class="text-gray-600">Ends:</span>
                                                <span class="font-semibold"><?php echo $auction['status'] === 'expired' ? 'Ended' : time_remaining($auction['end_date']); ?></span>
                                            </div>
                                        </div>
                                        <div class="mt-4 pt-4 border-t border-gray-200">
                                            <a href="item.php?id=<?php echo $auction['id']; ?>" class="w-full block text-center btn-secondary text-sm py-1">
                                                View Auction
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>