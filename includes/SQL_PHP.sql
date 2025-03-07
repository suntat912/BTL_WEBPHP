-- Tạo cơ sở dữ liệu
CREATE DATABASE fashion_store;

-- Sử dụng cơ sở dữ liệu
USE fashion_store;

-- Bảng Người Dùng
CREATE TABLE User_Accounts (
    account_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    full_name VARCHAR(100),
    phone VARCHAR(15),
    gender ENUM('male', 'female', 'other'),
    date_of_birth DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
ALTER TABLE User_Accounts
ADD COLUMN role ENUM('admin', 'user') NOT NULL DEFAULT 'user';
SELECT * FROM User_Accounts;
INSERT INTO User_Accounts (username, password_hash, email, full_name, phone, gender, date_of_birth) VALUES
('nguyenvana', 'hashed_password_1', 'nguyenvana@gmail.com', 'Nguyễn Văn A', '0901234567', 'male', '1990-05-15'),
('tranthib', 'hashed_password_2', 'tranthib@gmail.com', 'Trần Thị B', '0912345678', 'female', '1995-08-22'),
('lehoangc', 'hashed_password_3', 'lehoangc@gmail.com', 'Lê Hoàng C', '0987654321', 'male', '1988-12-05'),
('phamthid', 'hashed_password_4', 'phamthid@gmail.com', 'Phạm Thị D', '0978123456', 'female', '1993-03-10'),
('votranh', 'hashed_password_5', 'votranh@gmail.com', 'Võ Trần H', '0969876543', 'other', '2000-07-07');


-- Bảng Sản Phẩm
CREATE TABLE Products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    stock_quantity INT NOT NULL DEFAULT 0,
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
ALTER TABLE Products MODIFY image_url LONGTEXT;
SELECT * FROM Products;
INSERT INTO Products (product_name, description, price, stock_quantity, image_url) VALUES
('Áo Thun Nam', 'Áo thun nam cotton, thoải mái và dễ chịu.', 250000.00, 100, 'https://example.com/images/ao_thun_nam.jpg'),
('Quần Jeans Nữ', 'Quần jeans nữ thời trang, phù hợp với nhiều phong cách.', 350000.00, 50, 'https://example.com/images/quan_jeans_nu.jpg'),
('Giày Thể Thao', 'Giày thể thao nam, thoải mái khi vận động.', 600000.00, 75, 'https://example.com/images/giay_the_thao.jpg'),
('Váy Đầm Công Sở', 'Váy đầm công sở thanh lịch, phù hợp với môi trường làm việc.', 450000.00, 30, 'https://example.com/images/vay_dam_cong_so.jpg'),
('Túi Xách Da', 'Túi xách da cao cấp, sang trọng và bền bỉ.', 750000.00, 20, 'https://example.com/images/tui_xach_da.jpg');

-- Bảng Hình Ảnh Sản Phẩm
CREATE TABLE Product_Images (
    image_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    FOREIGN KEY (product_id) REFERENCES Products(product_id) ON DELETE CASCADE
);

-- Bảng Đơn Hàng
CREATE TABLE Orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    shipping_address TEXT NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'completed', 'canceled') DEFAULT 'pending',
    FOREIGN KEY (user_id) REFERENCES User_Accounts(account_id) ON DELETE CASCADE
);
INSERT INTO Orders (user_id, total_amount, shipping_address, order_date, status) VALUES
(1, 2505000, '123 Đường Nguyễn Trãi, Quận 1, TP.HCM', '2024-02-01 10:15:00', 'pending'),
(2, 1207500, '456 Đường Lê Lợi, Quận 3, TP.HCM', '2024-02-02 14:30:00', 'completed'),
(3, 899900, '789 Đường Cách Mạng Tháng 8, Quận 10, TP.HCM', '2024-02-03 09:00:00', 'canceled'),
(4, 3100000, '12 Đường Pasteur, Quận 1, TP.HCM', '2024-02-04 16:45:00', 'pending'),
(5, 1502500, '99 Đường Phạm Ngũ Lão, Quận 1, TP.HCM', '2024-02-05 11:20:00', 'completed');

-- Bảng Chi Tiết Đơn Hàng
CREATE TABLE Order_Details (
    order_detail_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES Orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES Products(product_id) ON DELETE CASCADE
);

-- Bảng Danh Mục
CREATE TABLE Categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(50) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
ALTER TABLE Categories
ADD COLUMN description TEXT;
INSERT INTO Categories (category_name, description) VALUES ('Điện Thoại', 'Các loại điện thoại di động, smartphone các hãng nổi tiếng');
INSERT INTO Categories (category_name, description) VALUES ('Máy Tính', 'Máy tính để bàn và laptop từ các thương hiệu hàng đầu');
INSERT INTO Categories (category_name, description) VALUES ('Máy Ảnh', 'Máy ảnh kỹ thuật số, máy quay phim, phụ kiện máy ảnh');

SELECT * FROM Categories;
-- Bảng Thương Hiệu
CREATE TABLE Brands (
    brand_id INT AUTO_INCREMENT PRIMARY KEY,
    brand_name VARCHAR(50) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
INSERT INTO Brands (brand_name) VALUES 
('Apple'),
('Samsung'),
('Sony'),
('LG'),
('Xiaomi'),
('Asus'),
('HP'),
('Dell'),
('Lenovo'),
('Acer');
SELECT * FROM Brands;

-- Bảng Liên Kết Sản Phẩm với Danh Mục
CREATE TABLE Product_Categories (
    product_id INT NOT NULL,
    category_id INT NOT NULL,
    PRIMARY KEY (product_id, category_id),
    FOREIGN KEY (product_id) REFERENCES Products(product_id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES Categories(category_id) ON DELETE CASCADE
);

-- Bảng Liên Kết Sản Phẩm với Thương Hiệu
CREATE TABLE Product_Brands (
    product_id INT NOT NULL,
    brand_id INT NOT NULL,
    PRIMARY KEY (product_id, brand_id),
    FOREIGN KEY (product_id) REFERENCES Products(product_id) ON DELETE CASCADE,
    FOREIGN KEY (brand_id) REFERENCES Brands(brand_id) ON DELETE CASCADE
);

-- Bảng Bình Luận
CREATE TABLE Comments (
    comment_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_id INT NOT NULL,
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES Products(product_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES User_Accounts(account_id) ON DELETE CASCADE
);

-- Bảng Thông Tin Vận Chuyển
CREATE TABLE Shipping_Info (
    shipping_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    tracking_number VARCHAR(100),
    carrier VARCHAR(100),
    shipping_date TIMESTAMP,
    delivery_date TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES Orders(order_id) ON DELETE CASCADE
);
CREATE TABLE Contact (
    id INT AUTO_INCREMENT PRIMARY KEY,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
ALTER TABLE Contact
ADD COLUMN user_id INT NOT NULL,
ADD CONSTRAINT fk_contact_user FOREIGN KEY (user_id) REFERENCES User_Accounts(account_id) ON DELETE CASCADE;
-- Thêm dữ liệu vào bảng Contact
INSERT INTO Contact (message, user_id) VALUES ('Tôi cần hỗ trợ về sản phẩm của bạn.', 1);
INSERT INTO Contact (message, user_id) VALUES ('Tôi cần hỗ trợ về sản phẩm của bạn.', 2);
CREATE TABLE Feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    contact_id INT NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (contact_id) REFERENCES Contact(id) ON DELETE CASCADE
);
