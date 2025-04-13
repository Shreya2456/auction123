CREATE TABLE auctions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    start_price DECIMAL(10, 2) NOT NULL,
    current_bid DECIMAL(10, 2),
    end_date DATETIME NOT NULL,
    status ENUM('active', 'closed') DEFAULT 'active',
    FOREIGN KEY (product_id) REFERENCES products(id)
);
INSERT INTO auctions (product_id, start_price, current_bid, end_date, status) VALUES
(1, 100.00, 150.00, '2025-12-31 23:59:59', 'active'),
(2, 200.00, 250.00, '2025-12-31 23:59:59', 'closed'),
(3, 300.00, 350.00, '2025-12-31 23:59:59', 'active');

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO products (name, description, price) VALUES
('Product 1', 'Description for product 1', 100.00),
('Product 2', 'Description for product 2', 200.00),
('Product 3', 'Description for product 3', 300.00);