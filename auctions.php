<?php
// Set page title
$page_title = "Auctions - BidPulse";

// Include database connection and functions
require_once "includes/db.php";
require_once "includes/functions.php";

// Get category ID if set
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : null;

// Get search query if set
$search_query = isset($_GET['query']) ? sanitize_input($_GET['query']) : null;

// Get all categories for the filter
$categories = get_categories();

// Fetch all items based on filters
$items = [];
$sql = "SELECT p.id, p.name AS title, p.description, p.price AS current_price, p.image_url, c.name AS category_name, p.created_at, p.duration, 
        DATE_ADD(p.created_at, INTERVAL p.duration DAY) AS end_time 
        FROM products p 
        LEFT JOIN categories c ON p.category = c.name 
        WHERE 1=1";

// Apply category filter
if ($category_id) {
    $sql .= " AND c.id = ?";
}

// Apply search query filter
if ($search_query) {
    $sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
}

$sql .= " ORDER BY p.created_at DESC";

$stmt = $conn->prepare($sql);

// Bind parameters dynamically
if ($category_id && $search_query) {
    $search_term = "%" . $search_query . "%";
    $stmt->bind_param("iss", $category_id, $search_term, $search_term);
} elseif ($category_id) {
    $stmt->bind_param("i", $category_id);
} elseif ($search_query) {
    $search_term = "%" . $search_query . "%";
    $stmt->bind_param("ss", $search_term, $search_term);
}

$stmt->execute();
$result = $stmt->get_result();
if ($result) {
    $items = $result->fetch_all(MYSQLI_ASSOC);
}
$stmt->close();

// Include header
include "includes/header.php";
?>

<div class="py-8">
    <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row justify-between items-start mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">
                    <?php
                    if ($category_id && !empty($categories)) {
                        foreach ($categories as $category) {
                            if ($category['id'] == $category_id) {
                                echo htmlspecialchars($category['name']) . " Auctions";
                                break;
                            }
                        }
                    } elseif ($search_query) {
                        echo "Search Results for \"" . htmlspecialchars($search_query) . "\"";
                    } else {
                        echo "All Auctions";
                    }
                    ?>
                </h1>
                <p class="text-gray-600"><?php echo count($items); ?> items found</p>
            </div>

            <?php if (is_logged_in()): ?>
                <a href="add_item.php" class="btn-accent mt-4 md:mt-0">
                    <i class="fas fa-plus mr-2"></i> Sell an Item
                </a>
            <?php endif; ?>
        </div>

        <div class="flex flex-col md:flex-row">
            <!-- Sidebar/Filters -->
            <div class="w-full md:w-64 bg-white p-4 rounded-lg shadow-md mb-6 md:mb-0 md:mr-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Filters</h3>

                <!-- Categories Filter -->
                <div class="mb-6">
                    <h4 class="font-semibold text-gray-700 mb-2">Categories</h4>
                    <ul class="space-y-2">
                        <li>
                            <a href="auctions.php" class="<?php echo !$category_id ? 'text-primary font-semibold' : 'text-gray-600 hover:text-primary'; ?> transition-colors">
                                All Categories
                            </a>
                        </li>
                        <?php foreach ($categories as $category): ?>
                            <li>
                                <a href="auctions.php?category=<?php echo $category['id']; ?>" class="<?php echo $category_id == $category['id'] ? 'text-primary font-semibold' : 'text-gray-600 hover:text-primary'; ?> transition-colors">
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Search Form -->
                <div>
                    <h4 class="font-semibold text-gray-700 mb-2">Search</h4>
                    <form action="auctions.php" method="get" class="flex flex-col">
                        <?php if ($category_id): ?>
                            <input type="hidden" name="category" value="<?php echo $category_id; ?>">
                        <?php endif; ?>
                        <input type="text" name="query" placeholder="Search auctions..." class="px-3 py-2 border border-gray-300 rounded-md mb-2" value="<?php echo $search_query ? htmlspecialchars($search_query) : ''; ?>">
                        <button type="submit" class="btn-primary py-2">
                            <i class="fas fa-search mr-2"></i> Search
                        </button>
                    </form>
                </div>
            </div>

            <!-- Main Content/Items -->
            <div class="flex-1">
                <?php if (empty($items)): ?>
                    <div class="bg-white rounded-lg shadow-md p-8 text-center">
                        <i class="fas fa-search text-5xl text-gray-300 mb-4"></i>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">No Items Found</h3>
                        <p class="text-gray-600 mb-4">We couldn't find any auction items matching your criteria.</p>
                        <a href="auctions.php" class="btn-primary">View All Auctions</a>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-6">
                        <?php foreach ($items as $item): ?>
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
                                        <span class="text-gray-500 text-sm">Ends in: <?php echo time_remaining($item['end_time']); ?></span>
                                        <a href="javascript:void(0);" onclick="toggleBidModal(<?php echo $item['id']; ?>)" class="btn-secondary text-sm py-1 px-3">Place Bid</a>
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

<!-- Place Bid Modal -->
<div id="place-bid-modal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded-lg shadow-lg w-1/3">
        <h2 class="text-2xl font-bold mb-4">Place Your Bid</h2>
        <form id="place-bid-form" method="POST">
            <input type="text" name="item_id" id="modal-item-id">
            <div class="mb-4">
                <label for="bid_amount" class="block text-gray-700">Bid Amount</label>
                <input type="number" name="bid_amount" id="bid_amount" class="w-full bg-gray-200 px-4 py-2 rounded" required>
            </div>
            <div class="flex justify-end space-x-4">
                <button type="button" onclick="toggleBidModal()" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Cancel</button>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Place Bid</button>
            </div>
        </form>
    </div>
</div>

<!-- <script>
    function toggleBidModal(itemId = null) {
        const modal = document.getElementById('place-bid-modal');
        const itemIdField = document.getElementById('modal-item-id');
        if (itemId) {
            itemIdField.value = itemId; // Set the item ID in the hidden input field
        }
        modal.classList.toggle('hidden');
    }
</script> -->
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Make sure the user is logged in and the session variable exists
$userId = isset($_SESSION['id']) ? $_SESSION['id'] : null;
?>
<script>
    function toggleBidModal(itemId = null) {
        const modal = document.getElementById('place-bid-modal');
        const itemIdField = document.getElementById('modal-item-id');
        if (itemId) {
            itemIdField.value = itemId; // Set the item ID in the hidden input field
        }
        modal.classList.toggle('hidden');
    }

    document.getElementById('place-bid-form').addEventListener('submit', function (e) {
        e.preventDefault(); // Prevent default form submission

        const itemId = document.getElementById('modal-item-id').value;
        const bidAmount = document.getElementById('bid_amount').value;  
        const userId = <?php echo json_encode($userId); ?>;

        
        // Send an AJAX request to place the bid
        fetch('insert_bid.php', {
            method: 'POST',
        
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ user_id:userId,item_id: itemId, bid_amount: bidAmount }),
        }).then(response=>response.json())
        .then(data=>
            {if (data.success){
            alert('Bid placed successfully!');
        } else {
            alert('Failed to place bid: ' + data.message);      
        }}).catch(error=>{
            console.error(error);
            alert('An error occurred while placing the bid.');
        });   

        // Close the modal
        toggleBidModal();
    });
</script>

<?php include "includes/footer.php"; ?>