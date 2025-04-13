# BidPulse - Online Auction Website

BidPulse is a complete online auction platform built with HTML, Tailwind CSS, JavaScript, PHP, and MySQL. The platform allows users to register, login, bid on items, and list their own items for auction.

## Features

- **User Authentication**: Register, login, and profile management
- **Auction Listings**: Browse items by category or search
- **Bidding System**: Place bids on items with real-time updates
- **Seller Dashboard**: List items and track bidding activity
- **Responsive Design**: Mobile-friendly interface using Tailwind CSS
- **Real-time Countdowns**: Auction timers with JavaScript

## Tech Stack

- **Frontend**: HTML, Tailwind CSS, JavaScript
- **Backend**: PHP
- **Database**: MySQL
- **Icons**: Font Awesome
- **Image Hosting**: External URLs (Google, etc.)

## Installation

### Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)

### Setup Instructions

1. **Clone the repository**
   ```bash
   git clone https://github.com/yourusername/bidpulse.git
   cd bidpulse
   ```

2. **Database Configuration**
   - Create a MySQL database named `auction_db`
   - Update the database credentials in `includes/config.php` if necessary:
     ```php
     define('DB_SERVER', 'localhost');
     define('DB_USERNAME', 'root');
     define('DB_PASSWORD', '');
     define('DB_NAME', 'auction_db');
     ```

3. **Initialize Database**
   - Run the database setup script:
     ```bash
     php includes/create_tables.php
     ```

4. **Start the web server**
   - If using PHP's built-in server (for development):
     ```bash
     php -S localhost:8000
     ```
   - Or configure your Apache/Nginx to point to the project directory

5. **Access the website**
   - Open your browser and navigate to `http://localhost:8000`

## Usage

### For Buyers

1. Register a new account or login
2. Browse available items by category or use the search function
3. View item details and place bids
4. Track your bidding history in your profile

### For Sellers

1. Login to your account
2. Go to "Sell an Item" to list a new auction
3. Provide item details, starting price, and set auction duration
4. Track your listed items and bids in your profile

## Project Structure

```
auction_website/
├── css/                   # CSS files (if needed beyond Tailwind CDN)
├── img/                   # Image assets (if any local images)
├── includes/              # PHP includes
│   ├── config.php         # Database configuration
│   ├── create_tables.php  # Database initialization
│   ├── functions.php      # Utility functions
│   ├── header.php         # Common header
│   └── footer.php         # Common footer
├── js/                    # JavaScript files (if needed)
├── php/                   # PHP scripts
├── add_item.php           # Add auction item page
├── auctions.php           # Auction listings page
├── categories.php         # Browse by category
├── index.php              # Homepage
├── item.php               # Single item view
├── login.php              # User login
├── logout.php             # User logout
├── my_bids.php            # User's bidding history
├── my_items.php           # User's listed items
├── profile.php            # User profile
├── register.php           # User registration
└── search.php             # Search functionality
```

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Acknowledgements

- Tailwind CSS for the responsive design
- Font Awesome for the icons
- All image sources used in the project
