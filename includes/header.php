<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
ob_start();
include 'db.php'; // Đảm bảo rằng đường dẫn đúng đến db.php

// Lấy danh sách danh mục từ cơ sở dữ liệu
$stmt = $pdo->prepare("SELECT * FROM Categories");
$stmt->execute();
$categories = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cửa Hàng Thời Trang</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        header {
            background: #333;
            color: #fff;
            padding: 10px 20px;
            text-align: center;
        }

        nav {
            background: #444;
            color: #fff;
            padding: 10px;
            text-align: center;
        }

        nav a {
            color: #fff;
            margin: 0 15px;
            text-decoration: none;
            font-size: 18px;
        }

        nav a:hover {
            text-decoration: underline;
        }

        nav i {
            margin-right: 5px; /* Khoảng cách giữa biểu tượng và văn bản */
        }

        .banner {
            width: 100%;
            max-height: 400px; /* Chiều cao tối đa cho banner */
            overflow: hidden;
        }

        .banner img {
            width: 100%; /* Hình ảnh chiếm toàn bộ chiều rộng của banner */
            height: auto; /* Đảm bảo giữ tỉ lệ khung hình */
            object-fit: cover; /* Cắt phần thừa nếu cần */
        }

        .dropdown {
            display: inline-block;
            position: relative;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #444; /* Màu nền cho dropdown */
            min-width: 160px; /* Chiều rộng tối thiểu */
            z-index: 1; /* Đảm bảo dropdown nằm trên các phần tử khác */
        }

        .dropdown-content a {
            color: #fff; /* Màu chữ */
            padding: 12px 16px; /* Padding cho các mục trong dropdown */
            text-decoration: none; /* Không gạch chân */
            display: block; /* Hiển thị thành khối */
        }

        .dropdown:hover .dropdown-content {
            display: block; /* Hiển thị dropdown khi hover */
        }

        .dropdown-content a:hover {
            background-color: #555; /* Màu nền khi hover */
        }
    </style>
</head>
<body>

<!-- Banner -->
<div class="banner">
    <img src="https://northrepublic.com/cdn/shop/files/123456.jpg?v=1703604970&width=1500" alt="Banner">
</div>

<header>
    <h1>Cửa Hàng Thời Trang</h1>
</header>

<nav>
    <a href="index.php"><i class="fas fa-home"></i> Trang Chủ</a>
    <a href="../user/contact.php"><i class="fas fa-envelope"></i> Liên Hệ</a>
    <a href="../user/history.php"><i class="fas fa-history"></i> Lịch Sử Mua Hàng</a>
    <div class="dropdown">
        <a href="products.php"><i class="fas fa-tshirt"></i> Sản Phẩm</a>
        <div class="dropdown-content">
            <?php foreach ($categories as $category): ?>
                <a href="products.php#category<?php echo $category['category_id']; ?>"><?php echo htmlspecialchars($category['category_name']); ?></a>
            <?php endforeach; ?>
        </div>
    </div>
    <a href="../user/cart.php"><i class="fas fa-shopping-cart"></i> Giỏ Hàng</a>
    <a href="../user/profile.php"><i class="fas fa-user"></i> Tài Khoản</a>
    <a href="/login_register.php">Đăng Nhập/Đăng Ký</a>
    <a href="../user/logout.php"><i class="fas fa-sign-out-alt"></i> Đăng Xuất</a>
</nav>

</body>
</html>
<?php ob_end_flush(); // Kết thúc buffer output và gửi nội dung ?>