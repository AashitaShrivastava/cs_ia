-- Step 1: Create Database
CREATE DATABASE viraasat_db;
USE viraasat_db;

-- Step 2: Create Users Table
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,  -- Store hashed passwords
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Step 3: Create Products Table
CREATE TABLE products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Step 4: Create Cart Table
CREATE TABLE cart (
    cart_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_id INT,
    quantity INT DEFAULT 1,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE
);

-- Step 5: Create Orders Table
CREATE TABLE orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    total_price DECIMAL(10,2) NOT NULL,
    status ENUM('Pending', 'Completed', 'Cancelled') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Step 6: Insert Sample Data

-- Insert Users
INSERT INTO users (name, email, password) 
VALUES 
('John Doe', 'john@example.com', SHA2('password123', 256)),
('Aditi Sharma', 'aditi@example.com', SHA2('aditi@secure', 256));

-- Insert Products
INSERT INTO products (name, description, price, image_url) 
VALUES 
('Hand-painted Wall Art', 'Traditional artwork', 2500.00, 'wall_art.jpg'),
('Handcrafted Wooden Box', 'Decorative storage box', 1500.00, 'wooden_box.jpg');

-- Insert Items in Cart
INSERT INTO cart (user_id, product_id, quantity) 
VALUES 
(1, 1, 1), 
(2, 2, 2);

-- Insert Orders
INSERT INTO orders (user_id, total_price, status) 
VALUES 
(1, 5000, 'Pending'),
(2, 3000, 'Completed');

-- Step 7: Querying Data

-- Get All Users
SELECT * FROM users;

-- Get All Products
SELECT * FROM products;

-- Get User's Cart
SELECT c.cart_id, p.name, p.price, c.quantity 
FROM cart c
JOIN products p ON c.product_id = p.product_id
WHERE c.user_id = 1;

-- Get All Orders
SELECT * FROM orders;

-- Step 8: Updating Data

-- Update Product Price
UPDATE products 
SET price = 3000.00 
WHERE product_id = 1;

-- Update Order Status
UPDATE orders 
SET status = 'Completed' 
WHERE order_id = 1;

-- Step 9: Deleting Data

-- Remove Item from Cart
DELETE FROM cart WHERE cart_id = 1;

-- Delete User (Will also delete their orders & cart due to CASCADE)
DELETE FROM users WHERE user_id = 2;
