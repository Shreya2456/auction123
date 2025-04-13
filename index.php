<?php
// Set page title
$page_title = "BidPulse - Online Auction";

// Include functions file
require_once "includes/functions.php";

// Include header
include "includes/header.php";

// Get featured items (limited to 6)
$featured_items = get_items(6);
?>

<!-- Hero Section -->
<section class="py-12 bg-gradient-to-r from-primary/90 to-secondary/90 text-white rounded-lg mt-4">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
            <div>
                <h1 class="text-4xl md:text-5xl font-bold mb-4">Find and Bid on Exclusive Items</h1>
                <p class="text-lg mb-6">Discover unique clothes and watches from sellers around the world. Bid on your favorite items and win auctions at the best prices.</p>
                <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4">
                    <a href="auctions.php" class="btn-accent text-center">Browse Auctions</a>
                    <?php if(!is_logged_in()): ?>
                        <a href="register.php" class="btn-accent bg-white text-primary hover:bg-gray-100 text-center">Register Now</a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="hidden md:block">
                <img src="https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80">
            </div>
        </div>
    </div>
</section>

<!-- Featured Auctions -->
<section class="py-12">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold text-gray-800">Featured Auctions</h2>
            <a href="auctions.php" class="text-primary hover:text-secondary transition-colors">View All <i class="fas fa-arrow-right ml-1"></i></a>
        </div>

        <?php if(empty($featured_items)): ?>
            <div class="text-center py-8">
                <p class="text-gray-500 text-lg">No auction items available at the moment.</p>
                <?php if(is_logged_in()): ?>
                    <a href="add_item.php" class="btn-primary inline-block mt-4">Sell an Item</a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-3 gap-6">
                <?php foreach($featured_items as $item): ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow group">
                        <div class="relative h-64 overflow-hidden">
                            <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            <div class="absolute top-0 right-0 bg-accent text-white px-3 py-1 rounded-bl-lg">
                                <div class="flex items-center">
                                    <i class="fas fa-gavel mr-1"></i>
                                    <span><?php echo format_currency($item['current_price']); ?></span>
                                </div>
                            </div>
                            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-3">
                                <span class="text-white font-semibold"><?php echo time_remaining($item['end_time']); ?> left</span>
                            </div>
                        </div>
                        <div class="p-4">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="text-xl font-bold text-gray-800 hover:text-primary transition-colors">
                                    <a href="item.php?id=<?php echo $item['id']; ?>"><?php echo htmlspecialchars($item['title']); ?></a>
                                </h3>
                                <span class="bg-primary/10 text-primary text-sm px-2 py-1 rounded-full"><?php echo htmlspecialchars($item['category_name']); ?></span>
                            </div>
                            <p class="text-gray-600 text-sm mb-4 line-clamp-2"><?php echo htmlspecialchars(substr($item['description'], 0, 120)) . (strlen($item['description']) > 120 ? '...' : ''); ?></p>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500 text-sm">Seller: <?php echo htmlspecialchars($item['seller_name']); ?></span>
                                <a href="item.php?id=<?php echo $item['id']; ?>" class="btn-secondary text-sm py-1 px-3">Place Bid</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- How It Works -->
<section class="py-12 bg-gray-50">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-12">How It Works</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white p-6 rounded-lg shadow-md text-center">
                <div class="w-16 h-16 bg-primary/10 text-primary rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-user-plus text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">1. Register an Account</h3>
                <p class="text-gray-600">Create your free account to start bidding on or selling items on our platform.</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md text-center">
                <div class="w-16 h-16 bg-secondary/10 text-secondary rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-search text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">2. Find or List Items</h3>
                <p class="text-gray-600">Browse through available auctions or list your own items for others to bid on.</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md text-center">
                <div class="w-16 h-16 bg-accent/10 text-accent rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-gavel text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">3. Bid and Win</h3>
                <p class="text-gray-600">Place your bids on items you like and win them at the best price before time runs out.</p>
            </div>
        </div>
    </div>
</section>

<!-- Categories -->
<section class="py-12">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-gray-800 mb-8">Shop by Category</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <a href="auctions.php?category=1" class="relative h-60 rounded-lg overflow-hidden group">
                <img src="https://media.burford.co.uk/images/SNY04089.jpg_edit.width-640_ln7jm6QxYVkHFHaT.jpg" alt="Clothes" class="w-full h-full object-cover group-hover:scale-105 transition-all duration-300"> 
                <div class="absolute inset-0 bg-black/40 flex items-center justify-center">
                    <div class="text-white text-center">
                        <h3 class="text-2xl font-bold mb-2">Clothes</h3>
                        <span class="inline-block border-b-2 border-accent pb-1">Shop Now</span>
                    </div>
                </div>
            </a>
            <a href="auctions.php?category=2" class="relative h-60 rounded-lg overflow-hidden group">
                <img src="https://www.kapoorwatch.com/blogs/wp-content/uploads/Banner1470x680-6.webp" alt="Watches" class="w-full h-full object-cover group-hover:scale-105 transition-all duration-300">
                <div class="absolute inset-0 bg-black/40 flex items-center justify-center">
                    <div class="text-white text-center">
                        <h3 class="text-2xl font-bold mb-2">Watches</h3>
                        <span class="inline-block border-b-2 border-accent pb-1">Shop Now</span>
                    </div>
                </div>
            </a>
            <a href="auctions.php?category=3" class="relative h-60 rounded-lg overflow-hidden group">
                <img src="https://images.unsplash.com/photo-1584917865442-de89df76afd3?q=80&w=1035&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="Accessories" class="w-full h-full object-cover group-hover:scale-105 transition-all duration-300">
                <div class="absolute inset-0 bg-black/40 flex items-center justify-center">
                    <div class="text-white text-center">
                        <h3 class="text-2xl font-bold mb-2">Accessories</h3>
                        <span class="inline-block border-b-2 border-accent pb-1">Shop Now</span>
                    </div>
                </div>
            </a>
        </div>
    </div>
</section>

<!-- Testimonials -->
<section class="py-12 bg-primary/5">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-12">What Our Users Say</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="text-accent mb-4">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p class="text-gray-600 mb-4">"I found a vintage Rolex watch on BidPulse that I've been searching for years. The bidding process was easy and secure. Highly recommend!"</p>
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-primary text-white rounded-full flex items-center justify-center mr-3">
                        <span class="font-bold">JD</span>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-800">John Doe</h4>
                        <p class="text-gray-500 text-sm">Watch Collector</p>
                    </div>
                </div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="text-accent mb-4">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p class="text-gray-600 mb-4">"As a seller, I've had a great experience on BidPulse. My items always get good bids, and the payment process is smooth and fast."</p>
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-secondary text-white rounded-full flex items-center justify-center mr-3">
                        <span class="font-bold">JS</span>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-800">Jane Smith</h4>
                        <p class="text-gray-500 text-sm">Fashion Retailer</p>
                    </div>
                </div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="text-accent mb-4">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star-half-alt"></i>
                </div>
                <p class="text-gray-600 mb-4">"The real-time bidding updates make the whole experience exciting. I've won several designer clothes at great prices. Customer service is also top-notch!"</p>
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-accent text-white rounded-full flex items-center justify-center mr-3">
                        <span class="font-bold">RJ</span>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-800">Robert Johnson</h4>
                        <p class="text-gray-500 text-sm">Fashion Enthusiast</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="py-12 bg-gradient-to-r from-secondary to-primary text-white rounded-lg my-12">
    <div class="container mx-auto px-4 text-center">
        <h2 class="text-3xl font-bold mb-4">Ready to Start Bidding?</h2>
        <p class="text-xl mb-8">Join thousands of users buying and selling on our platform every day.</p>
        <div class="flex flex-col sm:flex-row justify-center space-y-4 sm:space-y-0 sm:space-x-4">
            <?php if(is_logged_in()): ?>
                <a href="auctions.php" class="btn-accent">Browse Auctions</a>
                <a href="add_item.php" class="btn-accent bg-white text-primary hover:bg-gray-100">Sell an Item</a>
            <?php else: ?>
                <a href="register.php" class="btn-accent">Register Now</a>
                <a href="login.php" class="btn-accent bg-white text-primary hover:bg-gray-100">Login</a>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include "includes/footer.php"; ?>
