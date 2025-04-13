<?php
// Include functions file
require_once "includes/functions.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : "BidPulse - Online Auction"; ?></title>
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#6D28D9', // Deep purple
                        secondary: '#0D9488', // Teal
                        accent: '#F59E0B', // Amber
                    }
                }
            }
        }
    </script>
    <!-- Custom CSS -->
    <style type="text/tailwindcss">
        @layer utilities {
            .btn-primary {
                @apply bg-primary hover:bg-primary/90 text-white font-bold py-2 px-4 rounded transition-colors;
            }
            .btn-secondary {
                @apply bg-secondary hover:bg-secondary/90 text-white font-bold py-2 px-4 rounded transition-colors;
            }
            .btn-accent {
                @apply bg-accent hover:bg-accent/90 text-white font-bold py-2 px-4 rounded transition-colors;
            }
        }
    </style>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    <header class="bg-primary text-white shadow-md">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <a href="index.php" class="text-2xl font-bold flex items-center">
                    <i class="fas fa-gavel mr-2"></i> BidPulse
                </a>
                <nav class="hidden md:block">
                    <ul class="flex space-x-6">
                        <li><a href="index.php" class="hover:text-accent transition-colors">Home</a></li>
                        <li><a href="auctions.php" class="hover:text-accent transition-colors">Auctions</a></li>
                        <li><a href="categories.php" class="hover:text-accent transition-colors">Categories</a></li>
                        <?php if(is_logged_in()): ?>
                            <li><a href="add_item.php" class="hover:text-accent transition-colors">Sell Item</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <div class="flex items-center space-x-4">
                    <form action="search.php" method="get" class="hidden md:flex items-center">
                        <input type="text" name="query" placeholder="Search auctions..." class="px-3 py-1 rounded-l text-gray-700 focus:outline-none">
                        <button type="submit" class="bg-secondary px-3 py-1 rounded-r">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                    <div>
                        <?php if(is_logged_in()): ?>
                            <div class="relative group">
                                <button class="flex items-center hover:text-accent transition-colors">
                                    <i class="fas fa-user mr-1"></i> <?php echo htmlspecialchars($_SESSION["username"]); ?>
                                    <i class="fas fa-chevron-down ml-1 text-xs"></i>
                                </button>
                                <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-10">
                                    <a href="profile.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">Profile</a>
                                    <a href="my_bids.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">My Bids</a>
                                    <a href="my_items.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">My Items</a>
                                    <div class="border-t border-gray-200"></div>
                                    <a href="logout.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">Logout</a>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="flex space-x-2">
                                <a href="login.php" class="hover:text-accent transition-colors">Login</a>
                                <span class="text-gray-300">|</span>
                                <a href="register.php" class="hover:text-accent transition-colors">Register</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <!-- Mobile Menu Button -->
                <button class="md:hidden text-white" id="mobileMenuBtn">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>
        <!-- Mobile Menu -->
        <div class="md:hidden bg-primary/95 w-full hidden" id="mobileMenu">
            <div class="container mx-auto px-4 py-2">
                <nav class="flex flex-col space-y-2 pb-3">
                    <a href="index.php" class="hover:text-accent transition-colors">Home</a>
                    <a href="auctions.php" class="hover:text-accent transition-colors">Auctions</a>
                    <a href="categories.php" class="hover:text-accent transition-colors">Categories</a>
                    <?php if(is_logged_in()): ?>
                        <a href="add_item.php" class="hover:text-accent transition-colors">Sell Item</a>
                        <a href="profile.php" class="hover:text-accent transition-colors">Profile</a>
                        <a href="my_bids.php" class="hover:text-accent transition-colors">My Bids</a>
                        <a href="my_items.php" class="hover:text-accent transition-colors">My Items</a>
                        <a href="logout.php" class="hover:text-accent transition-colors">Logout</a>
                    <?php else: ?>
                        <a href="login.php" class="hover:text-accent transition-colors">Login</a>
                        <a href="register.php" class="hover:text-accent transition-colors">Register</a>
                    <?php endif; ?>
                </nav>
                <form action="search.php" method="get" class="flex items-center mt-2 pb-3">
                    <input type="text" name="query" placeholder="Search auctions..." class="px-3 py-1 rounded-l text-gray-700 focus:outline-none w-full">
                    <button type="submit" class="bg-secondary px-3 py-1 rounded-r">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </div>
    </header>
    <main class="flex-grow container mx-auto px-4 py-6">
        <?php if(isset($_SESSION['flash_message'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?php echo $_SESSION['flash_message']; ?></span>
                <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                    <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                </span>
            </div>
            <?php unset($_SESSION['flash_message']); ?>
        <?php endif; ?>
        <?php if(isset($_SESSION['error_message'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?php echo $_SESSION['error_message']; ?></span>
                <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                    <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                </span>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>
