<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../login.php"); // Redirect to login page
    exit();
}

// Include database connection
require_once "../includes/db.php";

// Fetch dashboard stats
$total_products = $db->query("SELECT COUNT(*) AS count FROM products")->fetch_assoc()['count'] ?? 0;
$active_auctions = $db->query("SELECT COUNT(*) AS count FROM auctions WHERE status = 'active'")->fetch_assoc()['count'] ?? 0;
$total_bids = $db->query("SELECT COUNT(*) AS count FROM bids")->fetch_assoc()['count'] ?? 0;
$total_users = $db->query("SELECT COUNT(*) AS count FROM users")->fetch_assoc()['count'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Online Auction System</title>
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
                    <li><a href="dashboard.php" class="block px-4 py-2 rounded bg-gray-700 hover:bg-gray-600">Dashboard</a></li>
                    <li><a href="products.php" class="block px-4 py-2 rounded hover:bg-gray-600">Manage Products</a></li>
                    <li><a href="auctions.php" class="block px-4 py-2 rounded hover:bg-gray-600">Manage Auctions</a></li>
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
                <h1 class="text-3xl font-bold text-gray-800">Admin Dashboard</h1>
                <div class="text-gray-600">
                    Welcome, <span class="font-semibold"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                </div>
            </header>

            <!-- Dashboard Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-gray-600 text-sm font-medium">Total Products</h3>
                    <p class="text-2xl font-bold text-blue-600"><?php echo $total_products; ?></p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-gray-600 text-sm font-medium">Active Auctions</h3>
                    <p class="text-2xl font-bold text-blue-600"><?php echo $active_auctions; ?></p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-gray-600 text-sm font-medium">Total Bids</h3>
                    <p class="text-2xl font-bold text-blue-600"><?php echo $total_bids; ?></p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-gray-600 text-sm font-medium">Registered Users</h3>
                    <p class="text-2xl font-bold text-blue-600"><?php echo $total_users; ?></p>
                </div>
            </div>
        </main>
    </div>
</body>
</html>