-- ===========================
-- BIKIN DATABASE
-- ===========================
CREATE DATABASE rating_app;
USE rating_app;

-- ===========================
-- Tabel users
-- ===========================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert admin default
INSERT INTO users (username, email, password, role) VALUES 
('Admin Kece', 'admin1@gmail.com', 'adminkece1', 'admin');

-- ===========================
-- Tabel products
-- ===========================
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_produk VARCHAR(255) NOT NULL,
    deskripsi TEXT,
    harga DECIMAL(10,2),
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- ===========================
-- Tabel product_assignments
-- ===========================
CREATE TABLE product_assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_id INT NOT NULL,
    status ENUM('pending','approved','rejected') DEFAULT 'pending',
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ===========================
-- Tabel reviews
-- ===========================
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    review_text TEXT,
    edit_count INT DEFAULT 0,
    deleted TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- ===========================
-- Tabel review_logs
-- ===========================
CREATE TABLE review_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    review_id INT NOT NULL,
    action ENUM('edit','delete') NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (review_id) REFERENCES reviews(id) ON DELETE CASCADE
);

-- ===========================
-- Tabel review_votes
-- ===========================
CREATE TABLE review_votes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    review_id INT NOT NULL,
    user_id INT NOT NULL,
    vote TINYINT(1) NOT NULL, -- 1 = membantu, 0 = tidak
    voted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY review_user (review_id, user_id),
    FOREIGN KEY (review_id) REFERENCES reviews(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ===========================
-- Tabel review_options
-- ===========================
CREATE TABLE review_options (
    id INT AUTO_INCREMENT PRIMARY KEY,
    option_text VARCHAR(255) NOT NULL
);

-- ===========================
-- Contoh insert opsi review
-- ===========================
INSERT INTO review_options (option_text) VALUES 
('Cepat'),
('Ramah'),
('Berkualitas'),
('Packaging Bagus'),
('Sesuai Deskripsi');

-- ===========================
-- Contoh produk & assignment
-- ===========================
INSERT INTO products (nama_produk, deskripsi, harga, created_by) VALUES
('Kopi Arabica 1kg', 'Kopi Arabica terbaik untuk pecinta kopi', 120000, 1),
('Teh Hijau Organik 500g', 'Teh hijau organik, sehat dan segar', 75000, 1);

-- Contoh user
INSERT INTO users (username, email, password, role) VALUES
('Putri', 'putri@gmail.com', 'putri123', 'user'),
('Dika', 'dika@gmail.com', 'dika123', 'user');

-- Assign produk ke user
INSERT INTO product_assignments (product_id, user_id, status) VALUES
(1, 2, 'pending'),
(2, 3, 'pending');

-- 
ALTER TABLE products ADD COLUMN media VARCHAR(255) DEFAULT NULL;


-- 
ALTER TABLE reviews ADD COLUMN media VARCHAR(255) DEFAULT NULL;
