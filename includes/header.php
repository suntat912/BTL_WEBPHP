<!-- header.php -->
<header>
    <h1>Cửa Hàng Thời Trang</h1>
</header>

<nav>
    <a href="index.php"><i class="fas fa-home"></i> Trang Chủ</a>
    <a href="../user/contact.php"><i class="fas fa-envelope"></i> Liên Hệ</a>
    <a href="../user/history.php"><i class="fas fa-history"></i> Lịch Sử Mua Hàng</a>
    <a href="products.php"><i class="fas fa-tshirt"></i> Sản Phẩm</a>
    <a href="../user/cart.php"><i class="fas fa-shopping-cart"></i> Giỏ Hàng</a>
    <a href="../user/profile.php"><i class="fas fa-user"></i> Tài Khoản</a>
    <a href="../user/logout.php"><i class="fas fa-sign-out-alt"></i> Đăng Xuất</a>
</nav>

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
</style>

<!-- Thêm liên kết tới Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">