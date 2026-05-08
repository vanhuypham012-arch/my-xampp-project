-- Tạo database với bảng mã chuẩn
-- 1. Tạo database với bảng mã chuẩn utf8mb4 để hỗ trợ tiếng Việt và Emoji
CREATE DATABASE IF NOT EXISTS fashion_club CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE fashion_club;

-- 2. Bảng lưu trữ thông tin người dùng
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    avatar_url VARCHAR(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Bảng lưu trữ danh sách sản phẩm
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    quantity INT DEFAULT 0,
    image_url VARCHAR(255),
    category VARCHAR(50),
    gender VARCHAR(20),
    description TEXT,
    is_video BOOLEAN DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Bảng lưu trữ thông tin đơn hàng tổng quát
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    total DECIMAL(10, 2),
    status VARCHAR(50) DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Bảng lưu trữ chi tiết từng sản phẩm trong đơn hàng
CREATE TABLE IF NOT EXISTS order_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    quantity INT,
    price DECIMAL(10, 2),
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Thêm tài khoản quản trị viên mặc định (Tên: admin | Mật khẩu: 123456)
REPLACE INTO users (username, password, role) VALUES ('admin', '$2y$10$7pXOL9jKCgGuYgXQJu/upO2TMtiAbqGEuPbcRZlHQEmtrHn4ZDnKG', 'admin');

-- 7. Thêm người dùng mẫu (Mật khẩu mặc định: 123456)
REPLACE INTO users (username, password, role) VALUES 
('khachhang1', '$2y$10$7pXOL9jKCgGuYgXQJu/upO2TMtiAbqGEuPbcRZlHQEmtrHn4ZDnKG', 'user'),
('khachhang2', '$2y$10$7pXOL9jKCgGuYgXQJu/upO2TMtiAbqGEuPbcRZlHQEmtrHn4ZDnKG', 'user');

-- 8. Thêm sản phẩm mẫu
-- Xóa toàn bộ sản phẩm cũ và reset ID về 1 để tránh việc cộng dồn lên 25 sản phẩm
DELETE FROM products;
ALTER TABLE products AUTO_INCREMENT = 1;

INSERT INTO products (name, price, quantity, image_url, category, gender, is_video) VALUES 
('Lisa Urban Brown Set', 110, 10, 'q1.jpg', 'pants', 'women', 0),
('Soft Ribbon Cargo', 85, 5, 'q2.jpg', 'pants', 'women', 0),
('Red Bandana Streetwear', 95, 0, 'q3.jpg', 'pants', 'women', 0),
('Dark Cyberpunk Trousers', 65, 2, 'q8.jpg', 'pants', 'men', 0),
('Vintage Acid Wash Denim', 45, 15, 'q9.jpg', 'pants', 'men', 0),
('Street Rocker White Slacks', 75, 8, 'q10.jpg', 'pants', 'men', 0),
('Rosé Pearl Corset Dress', 280, 0, 'v4.jpg', 'dress', 'women', 0),
('Jisoo Midnight Rose Gown', 350, 20, 'v5.jpg', 'dress', 'women', 0),
('Avant-Garde Fur Trim Set', 140, 12, 'v6.jpg', 'dress', 'women', 0),
('Jennie Floral Bustier Top', 125, 5, 't1.jpg', 'tops', 'women', 0),
('Draped Artistic Print Top', 55, 1, 't3.jpg', 'tops', 'women', 0),
('Pink Marble Strapless Top', 48, 0, 't5.jpg', 'tops', 'women', 0),
('Midnight Leather Bomber', 180, 3, 'k1.jpg', 'jacket', 'men', 0),
('Urban Techwear Windbreaker', 115, 3, 'k2.jpg', 'jacket', 'men', 0),
('Street Utility Overcoat', 165, 3, 'k3.jpg', 'jacket', 'men', 0),
('Dark Aesthetic Graphic Tee', 35, 3, 't7.jpg', 'tops', 'men', 0),
('Punk Rock Signature Shirt', 42, 3, 't8.jpg', 'tops', 'men', 0),
('Rebel Street Long Sleeve', 58, 3, 't9.jpg', 'tops', 'men', 0),
('Luxury Gold Watch', 550, 3, 'watch1.jpg', 'watch', 'men', 0),
('Chronos Motion Elite', 450, 5, 'w1.mp4', 'watch', 'men', 1),
('Grand Tourbillon Video', 1200, 2, 'w2.mp4', 'watch', 'men', 1),
('Classic Red Lipstick', 25, 50, 'makeup1.jpg', 'makeup', 'women', 0),
('Velvet Rose Gloss', 30, 20, 'makeup2.jpg', 'makeup', 'women', 0),
('Midnight Shadow Palette', 45, 15, 'makeup3.jpg', 'makeup', 'women', 0);

-- Lưu ý: Hãy đảm bảo các file ảnh (q1.jpg, v5.jpg...) tồn tại trong thư mục c:\xampp\htdocs\Fashion_club\img\