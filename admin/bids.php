<?php
session_start();
// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../login.html");
    exit();
}

// Include database connection
require_once "../includes/db.php";

// Fetch all bids from the database

$sql = "SELECT 
            b.id AS bid_id, 
            b.item_id AS item_id, 
            u.username AS user_name, 
            b.bid_amount AS bid_amount, 
            b.bid_time AS bid_time
        FROM bids b
        LEFT JOIN users u ON b.user_id = u.id
        ORDER BY b.bid_time DESC";

$result = $db->query($sql);
$bids = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bidding History - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
                    <li><a href="auctions.php" class="block px-4 py-2 rounded hover:bg-gray-600">Manage Auctions</a></li>
                    <li><a href="bids.php" class="block px-4 py-2 rounded bg-gray-700 hover:bg-gray-600">Bidding History</a></li>
                    <li><a href="users.php" class="block px-4 py-2 rounded hover:bg-gray-600">Manage Users</a></li>
                    <li><a href="logout.php" class="block px-4 py-2 rounded hover:bg-gray-600">Logout</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-6">
            <!-- Header -->
            <header class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-800">Bidding History</h1>
            </header>

            <!-- Data Table -->
            <div class="bg-white p-6 rounded-lg shadow">
                <table class="w-full border-collapse border border-gray-200">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border border-gray-200 px-4 py-2 text-left">Bid ID</th>
                            <th class="border border-gray-200 px-4 py-2 text-left">Item ID</th>
                            <th class="border border-gray-200 px-4 py-2 text-left">User</th>
                            <th class="border border-gray-200 px-4 py-2 text-left">Bid Amount</th>
                            <th class="border border-gray-200 px-4 py-2 text-left">Bid Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($bids)): ?>
                            <?php foreach ($bids as $bid): ?>
                                <tr>
                                    <td class="border border-gray-200 px-4 py-2"><?php echo htmlspecialchars($bid['bid_id']); ?></td>
                                    <td class="border border-gray-200 px-4 py-2"><?php echo htmlspecialchars($bid['item_id']); ?></td>
                                    <td class="border border-gray-200 px-4 py-2"><?php echo htmlspecialchars($bid['user_name']); ?></td>
                                    <td class="border border-gray-200 px-4 py-2"><?php echo htmlspecialchars($bid['bid_amount']); ?></td>
                                    <td class="border border-gray-200 px-4 py-2"><?php echo htmlspecialchars($bid['bid_time']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="border border-gray-200 px-4 py-2 text-center">No bids found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>


