<?php
session_start();
// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../login.html");
    exit();
}

// Include database connection
require_once "../includes/db.php";

// Initialize error message
$error_message = "";

// Handle new auction creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_type'], $_POST['start_price'], $_POST['end_date'], $_POST['image_url'])) {
    $product_type = $_POST['product_type'];
    $start_price = floatval($_POST['start_price']);
    $end_date = $_POST['end_date'];
    $image_url = $_POST['image_url'];

    // Validate inputs
    if (empty($product_type) || empty($start_price) || empty($end_date) || empty($image_url)) {
        $error_message = "All fields are required to create an auction.";
    } else {
        // Insert the new auction into the database
        $stmt = $db->prepare("INSERT INTO auctions (product_type, start_price, current_bid, end_date, status, image_url) VALUES (?, ?, ?, ?, 'upcoming', ?)");
        $stmt->bind_param("sdsss", $product_type, $start_price, $start_price, $end_date, $image_url);
        $stmt->execute();
        $stmt->close();

        // Redirect to avoid form resubmission
        header("Location: auctions.php");
        exit();
    }
}

// Update current_bid dynamically based on the highest bid
$update_current_bid_sql = "
    UPDATE auctions a
    LEFT JOIN (
        SELECT auction_id, MAX(bid_amount) AS highest_bid
        FROM bids
        GROUP BY auction_id
    ) b ON a.id = b.auction_id
    SET a.current_bid = COALESCE(b.highest_bid, a.start_price)
";
$db->query($update_current_bid_sql);

// Fetch auctions
$sql = "SELECT 
            id AS auction_id, 
            product_type AS product_name, 
            image_url, 
            start_price, 
            current_bid, 
            end_date, 
            status
        FROM auctions
        ORDER BY end_date ASC";

$result = $db->query($sql);
$auctions = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Auctions - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // JavaScript to handle modal visibility
        function toggleModal() {
            const modal = document.getElementById('create-auction-modal');
            modal.classList.toggle('hidden');
        }
    </script>
</head>
<body class="bg-gray-100 font-sans">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-800 text-white flex flex-col">
            <div class="p-6 text-center border-b border-gray-700">
                <h2 class="text-2xl font-bold">Admin Panel</h2>
            </div>
            <nav class="flex-1">
                <ul class="space-y-2 p-4">
                    <li><a href="dashboard.php" class="block px-4 py-2 rounded hover:bg-gray-600">Dashboard</a></li>
                    <li><a href="products.php" class="block px-4 py-2 rounded hover:bg-gray-600">Manage Products</a></li>
                    <li><a href="auctions.php" class="block px-4 py-2 rounded bg-gray-700 hover:bg-gray-600">Manage Auctions</a></li>
                    <li><a href="bids.php" class="block px-4 py-2 rounded hover:bg-gray-600">Bidding History</a></li>
                    <li><a href="users.php" class="block px-4 py-2 rounded hover:bg-gray-600">Manage Users</a></li>
                    <li><a href="logout.php" class="block px-4 py-2 rounded hover:bg-gray-600">Logout</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-6">
            <!-- Header -->
            <header class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-800">Manage Auctions</h1>
                <button onclick="toggleModal()" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Create Auction</button>
            </header>

            <!-- Error Message -->
            <?php if (!empty($error_message)): ?>
                <div class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <!-- Auctions Table -->
            <div class="bg-white p-6 rounded-lg shadow">
                <table class="w-full border-collapse border border-gray-200">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border border-gray-200 px-4 py-2 text-left">Auction ID</th>
                            <th class="border border-gray-200 px-4 py-2 text-left">Product</th>
                            <th class="border border-gray-200 px-4 py-2 text-left">Image</th>
                            <th class="border border-gray-200 px-4 py-2 text-left">Start Price</th>
                            <th class="border border-gray-200 px-4 py-2 text-left">Current Bid</th>
                            <th class="border border-gray-200 px-4 py-2 text-left">End Date</th>
                            <th class="border border-gray-200 px-4 py-2 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($auctions)): ?>
                            <?php foreach ($auctions as $auction): ?>
                                <tr>
                                    <td class="border border-gray-200 px-4 py-2"><?php echo htmlspecialchars($auction['auction_id']); ?></td>
                                    <td class="border border-gray-200 px-4 py-2"><?php echo htmlspecialchars($auction['product_name']); ?></td>
                                    <td class="border border-gray-200 px-4 py-2"><img src="<?php echo htmlspecialchars($auction['image_url']); ?>" alt="Product Image" class="w-16 h-16 object-cover"></td>
                                    <td class="border border-gray-200 px-4 py-2"><?php echo htmlspecialchars($auction['start_price']); ?></td>
                                    <td class="border border-gray-200 px-4 py-2"><?php echo htmlspecialchars($auction['current_bid'] ?? 'N/A'); ?></td>
                                    <td class="border border-gray-200 px-4 py-2"><?php echo htmlspecialchars($auction['end_date']); ?></td>
                                    <td class="border border-gray-200 px-4 py-2"><?php echo htmlspecialchars($auction['status']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="border border-gray-200 px-4 py-2 text-center">No auctions found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

            </div>
        </main>
    </div>
    <div id="create-auction-modal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg w-1/3">
            <h2 class="text-2xl font-bold mb-4">Create New Auction</h2>
            <form method="POST">
            <div class="mb-4">
                    <label for="product="block text-gray-700">Product
                    <input type="text" name="product" id="product" class="w-full bg-gray-200 px-4 py-2 rounded" required>                                             
                </div>
                <div class="mb-4">
                    <label for="product_type" class="block text-gray-700">Product Type</label>
                    <select name="product_type" id="product_type" class="w-full bg-gray-200 px-4 py-2 rounded" required>
                        <option value="">Select Product Type</option>
                        <option value="Clothes">Clothes</option>
                        <option value="Accessories">Accessories</option>
                        <option value="Watch">Watch</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="start_price" class="block text-gray-700">Start Price</label>
                    <input type="number" name="start_price" id="start_price" class="w-full bg-gray-200 px-4 py-2 rounded" required>
                </div>
                <div class="mb-4">
                    <label for="end_date" class="block text-gray-700">End Date</label>
                    <input type="datetime-local" name="end_date" id="end_date" class="w-full bg-gray-200 px-4 py-2 rounded" required>
                </div>
                <div class="mb-4">
                    <label for="image_url" class="block text-gray-700">Image URL</label>
                    <input type="text" name="image_url" id="image_url" class="w-full bg-gray-200 px-4 py-2 rounded" required>
                </div>
                
                
                <div class="mb-4">
                    <label for="status" class="block text-gray-700">Status</label>
                    <select name="status" id="status" class="w-full bg-gray-200 px-4 py-2 rounded" required>
                       
                        <option value="Ongoing">Ongoing</option>
                        <option value="Upcoming">Upcoming</option>
                        <option value="Expired">Expired</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-4">
                    <button type="button" onclick="toggleModal()" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Cancel</button>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Create</button>
                </div>
                
            </form>
        </div>
    </div>
</body>
</html>
</body>
</html>