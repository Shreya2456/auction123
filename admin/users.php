<?php
session_start();
// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../login.html");
    exit();
}

// Include database connection
require_once "../includes/db.php";

// Fetch all users from the database
$users = $db->query("SELECT id, username, email FROM users");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin Dashboard</title>
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
                    <li><a href="bids.php" class="block px-4 py-2 rounded hover:bg-gray-600">Bidding History</a></li>
                    <li><a href="users.php" class="block px-4 py-2 rounded bg-gray-700 hover:bg-gray-600">Manage Users</a></li>
                    <li><a href="../php/logout.php" class="block px-4 py-2 rounded hover:bg-gray-600">Logout</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-6">
            <!-- Header -->
            <header class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-800">Manage Users</h1>
            </header>

            <!-- Users Table -->
            <div class="bg-white p-6 rounded-lg shadow">
                <table class="w-full border-collapse border border-gray-200">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border border-gray-200 px-4 py-2 text-left">User ID</th>
                            <th class="border border-gray-200 px-4 py-2 text-left">Username</th>
                            <th class="border border-gray-200 px-4 py-2 text-left">Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($users->num_rows > 0): ?>
                            <?php while ($user = $users->fetch_assoc()): ?>
                                <tr>
                                    <td class="border border-gray-200 px-4 py-2"><?php echo htmlspecialchars($user['id']); ?></td>
                                    <td class="border border-gray-200 px-4 py-2"><?php echo htmlspecialchars($user['username']); ?></td>
                                    <td class="border border-gray-200 px-4 py-2"><?php echo htmlspecialchars($user['email']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="border border-gray-200 px-4 py-2 text-center">No users found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>