<?php
// Set page title
$page_title = "Search Results - BidPulse";

// Include functions file
require_once "includes/functions.php";

// Get search query
$search_query = isset($_GET['query']) ? sanitize_input($_GET['query']) : "";

// If no search query provided, redirect to auctions page
if(empty($search_query)){
    redirect("auctions.php");
}

// Get all categories for filter
$categories = get_categories();

// Get all items based on search
$items = get_items(100, null, $search_query);

// Include header
include "includes/header.php";
?>

<div class="py-8">
    <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row justify-between items-start mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">
                    Search Results for "<?php echo htmlspecialchars($search_query); ?>"
                </h1>
                <p class="text-gray-600"><?php echo count($items); ?> items found</p>
            </div>
        </div>

        <div class="flex flex-col md:flex-row">
            <!-- Sidebar/Filters -->
            <div class="w-full md:w-64 bg-white p-4 rounded-lg shadow-md mb-6 md:mb-0 md:mr-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Refine Search</h3>

                <!-- Categories Filter -->
                <div class="mb-6">
                    <h4 class="font-semibold text-gray-700 mb-2">Categories</h4>
                    <ul class="space-y-2">
                        <li>
                            <a href="search.php?query=<?php echo urlencode($search_query); ?>" class="text-primary font-semibold transition-colors">
                                All Categories
                            </a>
                        </li>
                        <?php foreach($categories as $category): ?>
                            <li>
                                <a href="search.php?query=<?php echo urlencode($search_query); ?>&category=<?php echo $category['id']; ?>" class="text-gray-600 hover:text-primary transition-colors">
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Price Range (Demo Only) -->
                <div class="mb-6">
                    <h4 class="font-semibold text-gray-700 mb-2">Price Range</h4>
                    <div class="px-2">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm text-gray-600">$0</span>
                            <span class="text-sm text-gray-600">$1000+</span>
                        </div>
                        <input type="range" min="0" max="1000" value="500" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                    </div>
                </div>

                <!-- Auction Status -->
                <div class="mb-6">
                    <h4 class="font-semibold text-gray-700 mb-2">Auction Status</h4>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="checkbox" class="rounded text-primary focus:ring-primary" checked>
                            <span class="ml-2 text-gray-700">Active Auctions</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" class="rounded text-primary focus:ring-primary">
                            <span class="ml-2 text-gray-700">Ending Soon</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" class="rounded text-primary focus:ring-primary">
                            <span class="ml-2 text-gray-700">Completed Auctions</span>
                        </label>
                    </div>
                </div>

                <!-- Search Form -->
                <div>
                    <h4 class="font-semibold text-gray-700 mb-2">New Search</h4>
                    <form action="search.php" method="get" class="flex flex-col">
                        <input type="text" name="query" placeholder="Search auctions..." class="px-3 py-2 border border-gray-300 rounded-md mb-2" value="<?php echo htmlspecialchars($search_query); ?>">
                        <button type="submit" class="btn-primary py-2">
                            <i class="fas fa-search mr-2"></i> Search
                        </button>
                    </form>
                </div>
            </div>

            <!-- Main Content/Items -->
            <div class="flex-1">
                <?php if(empty($items)): ?>
                    <div class="bg-white rounded-lg shadow-md p-8 text-center">
                        <i class="fas fa-search text-5xl text-gray-300 mb-4"></i>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">No Results Found</h3>
                        <p class="text-gray-600 mb-4">We couldn't find any auction items matching "<?php echo htmlspecialchars($search_query); ?>".</p>
                        <div class="space-y-2">
                            <p class="text-gray-600">Suggestions:</p>
                            <ul class="list-disc list-inside text-gray-600 text-left max-w-md mx-auto">
                                <li>Check your spelling</li>
                                <li>Try more general keywords</li>
                                <li>Try different keywords</li>
                                <li>Try browsing by category instead</li>
                            </ul>
                        </div>
                        <a href="auctions.php" class="btn-primary inline-block mt-4">Browse All Auctions</a>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php foreach($items as $item): ?>
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
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>
