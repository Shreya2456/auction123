<?php
session_start();
// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../login.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Admin Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <div class="admin-container">
        <aside class="sidebar">
            <div class="logo">
                <h2>Admin Panel</h2>
            </div>
            <nav class="admin-nav">
                <ul>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="products.php" class="active">Manage Products</a></li>
                    <li><a href="auctions.php">Manage Auctions</a></li>
                    <li><a href="bids.php">Bidding History</a></li>
                    <li><a href="users.php">Manage Users</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </aside>
        
        <main class="admin-content">
            <header class="admin-header">
                <h1>Manage Products</h1>
                <button id="add-product-btn" class="btn">Add New Product</button>
            </header>
            
            <div class="filter-section">
                <div class="search-box">
                    <input type="text" id="product-search" placeholder="Search products...">
                </div>
                <div class="filter-box">
                    <select id="category-filter">
                        <option value="">All Categories</option>
                        <option value="clothes">Clothes</option>
                        <option value="accessories">Accessories</option>
                        <option value="watches">Watches</option>
                    </select>
                </div>
            </div>
            
            <div class="data-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Description</th>
                            <th>Added Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="products-list">
                        <tr>
                            <td colspan="7">Loading products...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Add Product Modal -->
            <div id="product-modal" class="modal">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <h2 id="modal-title">Add New Product</h2>
                    <form id="product-form" enctype="multipart/form-data">
                        <input type="hidden" id="product-id" name="product_id">
                        
                        <div class="form-group">
                            <label for="product-name">Product Name</label>
                            <input type="text" id="product-name" name="product_name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="product-category">Category</label>
                            <select id="product-category" name="product_category" required>
                                <option value="">Select Category</option>
                                <option value="clothes">Clothes</option>
                                <option value="accessories">Accessories</option>
                                <option value="watches">Watches</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="product-description">Description</label>
                            <textarea id="product-description" name="product_description" rows="4" required></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="product-image">Product Image</label>
                            <input type="file" id="product-image" name="product_image" accept="image/*">
                            <div id="image-preview" class="image-preview"></div>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn" id="save-product">Save Product</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script src="../js/admin-products.js"></script>
</body>
</html>