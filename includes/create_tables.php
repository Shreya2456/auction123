<?php
// Include config file
require_once "config.php";

// Create tables

// Users table
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    registration_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    is_admin TINYINT(1) DEFAULT 0
)";

if(mysqli_query($conn, $sql)){
    echo "Table users created successfully.<br>";
} else {
    echo "ERROR: Could not create table users. " . mysqli_error($conn) . "<br>";
}

// Categories table
$sql = "CREATE TABLE IF NOT EXISTS categories (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE
)";

if(mysqli_query($conn, $sql)){
    echo "Table categories created successfully.<br>";
} else {
    echo "ERROR: Could not create table categories. " . mysqli_error($conn) . "<br>";
}

// Insert default categories if not exists
$categories = ["Clothes", "Watches", "Accessories"];
foreach($categories as $category) {
    $sql = "INSERT IGNORE INTO categories (name) VALUES ('" . $category . "')";
    mysqli_query($conn, $sql);
}

// Items table
$sql = "CREATE TABLE IF NOT EXISTS items (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    category_id INT NOT NULL,
    starting_price DECIMAL(10,2) NOT NULL,
    current_price DECIMAL(10,2) NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    end_time DATETIME NOT NULL,
    seller_id INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (seller_id) REFERENCES users(id)
)";

if(mysqli_query($conn, $sql)){
    echo "Table items created successfully.<br>";
} else {
    echo "ERROR: Could not create table items. " . mysqli_error($conn) . "<br>";
}

// Bids table
$sql = "CREATE TABLE IF NOT EXISTS bids (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    item_id INT NOT NULL,
    user_id INT NOT NULL,
    bid_amount DECIMAL(10,2) NOT NULL,
    bid_time DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (item_id) REFERENCES items(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
)";

if(mysqli_query($conn, $sql)){
    echo "Table bids created successfully.<br>";
} else {
    echo "ERROR: Could not create table bids. " . mysqli_error($conn) . "<br>";
}

echo "Database setup completed!";
?>
