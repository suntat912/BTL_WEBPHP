<?php
// Bắt đầu session và kiểm tra đăng nhập
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../user/login_register.php"); // Chuyển hướng về trang đăng nhập nếu chưa đăng nhập
    exit();
}

// Kết nối đến cơ sở dữ liệu bằng PDO
$servername = "localhost";
$username = "root"; // Tên người dùng cơ sở dữ liệu
$password = "Nghiacoi2212@"; // Mật khẩu cơ sở dữ liệu
$dbname = "fashion_store"; // Tên cơ sở dữ liệu

try {
    // Kết nối PDO với cơ sở dữ liệu MySQL
    $dsn = "mysql:host=$servername;dbname=$dbname;charset=utf8";
    $conn = new PDO($dsn, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Xử lý lỗi nếu không thể kết nối
    die("Kết nối thất bại: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        header {
            background: #333;
            color: #fff;
            padding: 20px;
            text-align: center;
        }
        .banner {
            height: 150px; /* Chiều cao của banner */
            margin: 20px auto; /* Căn giữa banner */
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden; /* Đảm bảo hình ảnh không vượt quá khu vực banner */
        }
        .banner img {
            max-width: 100%;
            height: auto;
            min-height: 100%; /* Đảm bảo hình ảnh chiếm toàn bộ chiều cao */
            object-fit: cover; /* Cắt hình ảnh để phù hợp với khu vực banner */
        }
        h2 {
            color: #333;
            text-align: center;
        }
        nav {
            display: flex;
            justify-content: center;
            background: #444;
            padding: 10px;
        }
        nav a {
            color: #fff;
            margin: 0 15px;
            text-decoration: none;
            font-size: 18px;
            padding: 10px;
            border-radius: 5px;
            transition: background 0.3s;
        }
        nav a:hover {
            background: #5cb85c; /* Màu xanh khi hover */
        }
        .card {
            background: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin: 20px;
            padding: 20px;
            text-align: center;
        }
        .card a {
            display: block;
            margin: 10px 0;
            font-weight: bold;
            color: #333;
            text-decoration: none;
        }
        .card a:hover {
            color: #5cb85c; /* Màu xanh khi hover */
        }
        footer {
            text-align: center;
            padding: 20px;
            background: #333;
            color: white;
            position: relative;
            bottom: 0;
            width: 100%;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Cửa Hàng Thời Trang - Dashboard Admin</h1>
    </header>
    
    <div class="banner">
        <img src="https://cdn.s99.vn/ss1/prod/product/7fbc91a7d40777a3be598dac25cbf166.jpg" alt="Banner">
    </div>

    <h2>Quản Lý Hệ Thống</h2>
    <nav>
        <a href="manage_products.php"><i class="fas fa-box"></i> Quản Lý Sản Phẩm</a>
        <a href="manage_orders.php"><i class="fas fa-shopping-cart"></i> Quản Lý Đơn Hàng</a>
        <a href="manage_customers.php"><i class="fas fa-users"></i> Quản Lý Khách Hàng</a>
        <a href="manage_contacts.php"><i class="fas fa-envelope"></i> Quản Lý Liên Hệ</a>
        <a href="manage_categories.php"><i class="fas fa-tags"></i> Quản Lý Danh Mục</a>
        <a href="manage_brands.php"><i class="fas fa-briefcase"></i> Quản Lý Thương Hiệu</a>
        <a href="statistics.php"><i class="fas fa-chart-bar"></i> Thống Kê</a> <!-- Thêm liên kết đến trang thống kê -->
        <a href="../user/logout.php"><i class="fas fa-sign-out-alt"></i> Đăng Xuất</a>
    </nav>

    <div class="card">
        <a href="manage_products.php">Quản Lý Sản Phẩm</a>
        <a href="manage_orders.php">Quản Lý Đơn Hàng</a>
        <a href="manage_customers.php">Quản Lý Khách Hàng</a>
        <a href="manage_contacts.php">Quản Lý Liên Hệ</a>
        <a href="manage_categories.php">Quản Lý Danh Mục</a>
        <a href="manage_brands.php">Quản Lý Thương Hiệu</a>
        <a href="statistics.php"><i class="fas fa-chart-bar"></i> Thống Kê</a> <!-- Thêm liên kết đến trang thống kê -->
        <a href="../user/logout.php"><i class="fas fa-sign-out-alt"></i> Đăng Xuất</a>
    </div>

    <footer>
        <p>&copy; 2025 Cửa Hàng Thời Trang. Tất cả quyền được bảo lưu.</p>
    </footer>
</body>
</html>