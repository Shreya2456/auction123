<?php
// Set page title
$page_title = "Sell an Item - BidPulse";

// Include functions file
require_once "includes/functions.php";

// Check if user is logged in
if(!is_logged_in()){
    $_SESSION["error_message"] = "You must be logged in to sell an item.";
    redirect("login.php");
}

// Get all categories
$categories = get_categories();

// Define variables and initialize with empty values
$title = $description = $category_id = $starting_price = $duration = $image_url = "";
$title_err = $description_err = $category_id_err = $starting_price_err = $duration_err = $image_url_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Validate title
    if(empty(trim($_POST["title"]))){
        $title_err = "Please enter a title.";
    } else{
        $title = sanitize_input($_POST["title"]);
    }

    // Validate description
    if(empty(trim($_POST["description"]))){
        $description_err = "Please enter a description.";
    } else{
        $description = sanitize_input($_POST["description"]);
    }

    // Validate category
    if(empty($_POST["category_id"])){
        $category_id_err = "Please select a category.";
    } else{
        $category_id = (int)$_POST["category_id"];
    }

    // Validate starting price
    if(empty(trim($_POST["starting_price"]))){
        $starting_price_err = "Please enter a starting price.";
    } elseif(!is_numeric($_POST["starting_price"]) || floatval($_POST["starting_price"]) <= 0){
        $starting_price_err = "Please enter a valid positive number.";
    } else{
        $starting_price = floatval($_POST["starting_price"]);
    }

    // Validate duration
    if(empty($_POST["duration"])){
        $duration_err = "Please select an auction duration.";
    } elseif(!in_array($_POST["duration"], ["1", "3", "5", "7", "10"])){
        $duration_err = "Please select a valid duration.";
    } else{
        $duration = (int)$_POST["duration"];
    }

    // Validate image URL
    if(empty(trim($_POST["image_url"]))){
        $image_url_err = "Please enter an image URL.";
    } elseif(!filter_var($_POST["image_url"], FILTER_VALIDATE_URL)){
        $image_url_err = "Please enter a valid URL.";
    } else{
        $image_url = sanitize_input($_POST["image_url"]);
    }

    // Check input errors before inserting in database
    if(empty($title_err) && empty($description_err) && empty($category_id_err) && empty($starting_price_err) && empty($duration_err) && empty($image_url_err)){

        // Calculate end time based on duration
        $end_time = date('Y-m-d H:i:s', strtotime("+$duration days"));

        // Prepare an insert statement
        $sql = "INSERT INTO items (title, description, category_id, starting_price, current_price, image_url, end_time, seller_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        if($stmt = mysqli_prepare($conn, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssiddsis", $param_title, $param_description, $param_category_id, $param_starting_price, $param_current_price, $param_image_url, $param_end_time, $param_seller_id);

            // Set parameters
            $param_title = $title;
            $param_description = $description;
            $param_category_id = $category_id;
            $param_starting_price = $starting_price;
            $param_current_price = $starting_price; // Initially, the current price is the starting price
            $param_image_url = $image_url;
            $param_end_time = $end_time;
            $param_seller_id = $_SESSION["id"];

            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Get the item ID of the newly inserted item
                $item_id = mysqli_insert_id($conn);

                // Redirect to the item page
                $_SESSION["flash_message"] = "Your item has been listed successfully!";
                redirect("item.php?id=" . $item_id);
            } else{
                $_SESSION["error_message"] = "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
}

// Include header
include "includes/header.php";
?>

<div class="py-8">
    <div class="container mx-auto px-4">
        <div class="max-w-3xl mx-auto">
            <h1 class="text-3xl font-bold text-center text-gray-800 mb-6">Sell an Item</h1>

            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-6">
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="space-y-6">
                        <div>
                            <label for="title" class="block text-gray-700 font-semibold mb-2">Item Title *</label>
                            <input type="text" name="title" id="title" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary <?php echo (!empty($title_err)) ? 'border-red-500' : 'border-gray-300'; ?>" value="<?php echo $title; ?>" placeholder="Enter a descriptive title" required>
                            <span class="text-red-500 text-sm"><?php echo $title_err; ?></span>
                            <p class="text-gray-500 text-sm mt-1">Make your title clear and descriptive to attract bidders.</p>
                        </div>

                        <div>
                            <label for="description" class="block text-gray-700 font-semibold mb-2">Description *</label>
                            <textarea name="description" id="description" rows="5" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary <?php echo (!empty($description_err)) ? 'border-red-500' : 'border-gray-300'; ?>" placeholder="Provide a detailed description of your item" required><?php echo $description; ?></textarea>
                            <span class="text-red-500 text-sm"><?php echo $description_err; ?></span>
                            <p class="text-gray-500 text-sm mt-1">Include details like condition, brand, size, color, and any other relevant information.</p>
                        </div>

                        <div>
                            <label for="category_id" class="block text-gray-700 font-semibold mb-2">Category *</label>
                            <select name="category_id" id="category_id" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary <?php echo (!empty($category_id_err)) ? 'border-red-500' : 'border-gray-300'; ?>" required>
                                <option value="">Select a category</option>
                                <?php foreach($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>" <?php echo $category_id == $category['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <span class="text-red-500 text-sm"><?php echo $category_id_err; ?></span>
                        </div>

                        <div>
                            <label for="starting_price" class="block text-gray-700 font-semibold mb-2">Starting Price ($) *</label>
                            <input type="number" name="starting_price" id="starting_price" step="0.01" min="0.01" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary <?php echo (!empty($starting_price_err)) ? 'border-red-500' : 'border-gray-300'; ?>" value="<?php echo $starting_price; ?>" placeholder="Enter starting bid amount" required>
                            <span class="text-red-500 text-sm"><?php echo $starting_price_err; ?></span>
                            <p class="text-gray-500 text-sm mt-1">Set a competitive starting price to attract initial bids.</p>
                        </div>

                        <div>
                            <label for="duration" class="block text-gray-700 font-semibold mb-2">Auction Duration *</label>
                            <select name="duration" id="duration" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary <?php echo (!empty($duration_err)) ? 'border-red-500' : 'border-gray-300'; ?>" required>
                                <option value="">Select duration</option>
                                <option value="1" <?php echo $duration == 1 ? 'selected' : ''; ?>>1 day</option>
                                <option value="3" <?php echo $duration == 3 ? 'selected' : ''; ?>>3 days</option>
                                <option value="5" <?php echo $duration == 5 ? 'selected' : ''; ?>>5 days</option>
                                <option value="7" <?php echo $duration == 7 ? 'selected' : ''; ?>>7 days</option>
                                <option value="10" <?php echo $duration == 10 ? 'selected' : ''; ?>>10 days</option>
                            </select>
                            <span class="text-red-500 text-sm"><?php echo $duration_err; ?></span>
                        </div>

                        <div>
                            <label for="image_url" class="block text-gray-700 font-semibold mb-2">Image URL *</label>
                            <input type="url" name="image_url" id="image_url" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary <?php echo (!empty($image_url_err)) ? 'border-red-500' : 'border-gray-300'; ?>" value="<?php echo $image_url; ?>" placeholder="Enter the URL of your item's image" required>
                            <span class="text-red-500 text-sm"><?php echo $image_url_err; ?></span>
                            <p class="text-gray-500 text-sm mt-1">Provide a direct link to an image hosted online. You can use image hosting services or Google image URLs.</p>
                        </div>

                        <div class="pt-4">
                            <button type="submit" class="btn-primary w-full">List Item for Auction</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="mt-6 bg-yellow-50 border border-yellow-200 text-yellow-800 p-4 rounded-lg">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-yellow-400 mr-2 mt-1"></i>
                    <div>
                        <h3 class="font-bold">Important Information</h3>
                        <p class="text-sm mt-1">By listing an item, you agree to our terms and conditions. Once your item receives bids, you cannot cancel the auction. Make sure all information is accurate before listing.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>
