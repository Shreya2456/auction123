<?php
session_start();
// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../login.html");
    exit();
}

// Include database connection
require_once "../includes/db.php";

// Handle product addition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $duration = $_POST['duration'];
    $image_url = $_POST['image_url'];

    // Insert product into the database
    $stmt = $db->prepare("INSERT INTO products (name, description, category, price, duration, image_url) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssdis", $name, $description, $category, $price, $duration, $image_url);
    $stmt->execute();
    $stmt->close();

    // Redirect to avoid form resubmission
    header("Location: products.php");
    exit();
}

// Handle product deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_product'])) {
    $product_id = $_POST['product_id'];

    // Check if the product is referenced in the auctions table
    $check = $db->prepare("SELECT COUNT(*) FROM auctions WHERE product_id = ?");
    $check->bind_param("i", $product_id);
    $check->execute();
    $check->bind_result($count);
    $check->fetch();
    $check->close();

    if ($count > 0) {
        // Prevent deletion and show an error message
        echo "<script>alert('Cannot delete this product because it is associated with an auction.');</script>";
    } else {
        // Delete product from the database
        $stmt = $db->prepare("DELETE FROM products WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $stmt->close();

        // Redirect to avoid form resubmission
        header("Location: products.php");
        exit();
    }
}

// Fetch all products from the database
$products = $db->query("SELECT id, name, description, category, price, duration, image_url, created_at FROM products");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Admin Dashboard</title>
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
                    <li><a href="products.php" class="block px-4 py-2 rounded bg-gray-700 hover:bg-gray-600">Manage Products</a></li>
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
                <h1 class="text-3xl font-bold text-gray-800">Manage Products</h1>
                <button id="add-product-btn" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600" onclick="document.getElementById('product-modal').classList.remove('hidden')">Add Product</button>
            </header>

            <!-- Products Table -->
            <div class="bg-white p-6 rounded-lg shadow">
                <table class="w-full border-collapse border border-gray-200">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border border-gray-200 px-4 py-2 text-left">Product ID</th>
                            <th class="border border-gray-200 px-4 py-2 text-left">Name</th>
                            <th class="border border-gray-200 px-4 py-2 text-left">Category</th>
                            <th class="border border-gray-200 px-4 py-2 text-left">Price</th>
                            <th class="border border-gray-200 px-4 py-2 text-left">Duration</th>
                            <th class="border border-gray-200 px-4 py-2 text-left">Image</th>
                            <th class="border border-gray-200 px-4 py-2 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($products->num_rows > 0): ?>
                            <?php while ($product = $products->fetch_assoc()): ?>
                                <tr>
                                    <td class="border border-gray-200 px-4 py-2"><?php echo htmlspecialchars($product['id']); ?></td>
                                    <td class="border border-gray-200 px-4 py-2"><?php echo htmlspecialchars($product['name']); ?></td>
                                    <td class="border border-gray-200 px-4 py-2"><?php echo htmlspecialchars($product['category']); ?></td>
                                    <td class="border border-gray-200 px-4 py-2"><?php echo htmlspecialchars($product['price']); ?></td>
                                    <td class="border border-gray-200 px-4 py-2"><?php echo htmlspecialchars($product['duration']); ?> days</td>
                                    <td class="border border-gray-200 px-4 py-2"><img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="Product Image" class="w-16 h-16 object-cover"></td>
                                    <td class="border border-gray-200 px-4 py-2">
                                        <form method="POST" class="inline">
                                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                            <button type="submit" name="delete_product" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="border border-gray-200 px-4 py-2 text-center">No products found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Add Product Modal -->
    <div id="product-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg w-1/2">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold">Add Product</h2>
                <button class="text-gray-500 hover:text-gray-700" onclick="document.getElementById('product-modal').classList.add('hidden')">&times;</button>
            </div>
            <form method="POST">
                <input type="hidden" name="add_product" value="1">
                <div class="mb-4">
                    <label for="name" class="block text-gray-700">Product Name</label>
                    <input type="text" id="name" name="name" required class="border border-gray-300 rounded px-4 py-2 w-full">
                </div>
                <div class="mb-4">
                    <label for="description" class="block text-gray-700">Description</label>
                    <textarea id="description" name="description" required class="border border-gray-300 rounded px-4 py-2 w-full"></textarea>
                </div>
                <div class="mb-4">
                    <label for="category" class="block text-gray-700">Category</label>
                    <select id="category" name="category" required class="border border-gray-300 rounded px-4 py-2 w-full">
                        <option value="clothes">Clothes</option>
                        <option value="watches">Watches</option>
                        <option value="accessories">Accessories</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="price" class="block text-gray-700">Starting Price</label>
                    <input type="number" id="price" name="price" step="0.01" required class="border border-gray-300 rounded px-4 py-2 w-full">
                </div>
                <div class="mb-4">
                <label for="duration" class="block text-gray-700">Auction Duration</label>
                <select id="duration" name="duration" required class="border border-gray-300 rounded px-4 py-2 w-full">
                    <option value="1">1 Day</option>
                    <option value="3">3 Days</option>
                    <option value="5">5 Days</option>
                    <option value="7">7 Days</option>
                    <option value="10">10 Days</option>
                </select>
            </div>
                <div class="mb-4">
                    <label for="image_url" class="block text-gray-700">Image URL</label>
                    <input type="url" id="image_url" name="image_url" required class="border border-gray-300 rounded px-4 py-2 w-full">
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Add Product</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>