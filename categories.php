<?php
// Set page title
$page_title = "Categories - BidPulse";

// Include functions file
require_once "includes/functions.php";

// Get all categories
$categories = get_categories();

// Include header
include "includes/header.php";
?>

<div class="py-8">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Browse Categories</h1>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <?php foreach($categories as $category):
                // Set category image based on name
                $category_image = "https://via.placeholder.com/800x600";
                $category_description = "Browse a wide selection of items in this category.";

                if(strtolower($category['name']) == "clothes") {
                    $category_image = "https://media.burford.co.uk/images/SNY04089.jpg_edit.width-640_ln7jm6QxYVkHFHaT.jpg";
                    $category_description = "From designer brands to everyday wear, find the perfect clothes for your style.";
                } elseif(strtolower($category['name']) == "watches") {
                    $category_image = "https://www.kapoorwatch.com/blogs/wp-content/uploads/Banner1470x680-6.webp";
                    $category_description = "Discover luxury timepieces, vintage watches, and modern designs from top brands.";
                } elseif(strtolower($category['name']) == "accessories") {
                    $category_image = "https://images.unsplash.com/photo-1584917865442-de89df76afd3?q=80&w=1035&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D";
                    $category_description = "Complete your look with belts, jewelry, bags, and other fashion accessories.";
                }

                // Get item count for this category
                $item_count = 0;
                $sql = "SELECT COUNT(*) as item_count FROM items WHERE category_id = ?";
                if($stmt = mysqli_prepare($conn, $sql)){
                    mysqli_stmt_bind_param($stmt, "i", $category['id']);

                    if(mysqli_stmt_execute($stmt)){
                        $result = mysqli_stmt_get_result($stmt);
                        $row = mysqli_fetch_assoc($result);
                        $item_count = $row['item_count'];
                    }

                    mysqli_stmt_close($stmt);
                }
            ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                    <div class="relative h-60 overflow-hidden">
                        <img src="<?php echo $category_image; ?>" alt="<?php echo htmlspecialchars($category['name']); ?>" class="w-full h-full object-cover transition-transform duration-300 hover:scale-105">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent flex items-end">
                            <div class="p-6">
                                <h2 class="text-2xl font-bold text-white mb-2"><?php echo htmlspecialchars($category['name']); ?></h2>
                                <p class="text-white/80 text-sm"><?php echo $item_count; ?> items available</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <p class="text-gray-600 mb-4"><?php echo $category_description; ?></p>
                        <a href="auctions.php?category=<?php echo $category['id']; ?>" class="btn-primary w-full block text-center">Browse <?php echo htmlspecialchars($category['name']); ?></a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="bg-gray-50 rounded-lg shadow-md mt-12 p-8">
            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Why Choose BidPulse?</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Our online auction platform offers a secure and exciting way to buy and sell items in various categories.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-16 h-16 bg-primary/10 text-primary rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-shield-alt text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Secure Bidding</h3>
                    <p class="text-gray-600">Our platform ensures all transactions are secure and user information is protected.</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-secondary/10 text-secondary rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-tag text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Great Deals</h3>
                    <p class="text-gray-600">Find unique items at competitive prices with our dynamic bidding system.</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-accent/10 text-accent rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-user-friends text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Community</h3>
                    <p class="text-gray-600">Join our community of buyers and sellers to exchange unique and valuable items.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>
