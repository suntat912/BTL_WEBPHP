<?php
session_start();

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['username'])) {
    header("Location: login_register.php"); // Chuyển hướng về trang đăng nhập nếu chưa đăng nhập
    exit();
}

// Kết nối đến cơ sở dữ liệu
$servername = "localhost";
$username = "root"; // Thay đổi với tên người dùng của bạn
$password = "Nghiacoi2212@"; // Thay đổi với mật khẩu của bạn
$dbname = "fashion_store"; // Tên cơ sở dữ liệu

$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Lấy danh sách sản phẩm từ cơ sở dữ liệu
$sql = "SELECT * FROM Products";
$result = $conn->query($sql);

$products = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

// Xử lý thêm vào giỏ hàng
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'] ?? 1; // Số lượng mặc định là 1
    // Thêm sản phẩm vào giỏ hàng
    $_SESSION['cart'][$product_id] = [
        'name' => $_POST['product_name'],
        'price' => $_POST['product_price'],
        'quantity' => $quantity,
    ];
    // Chuyển hướng trở lại để tránh gửi lại form
    header("Location: cart.php");
    exit();
}

$conn->close(); // Đóng kết nối
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Chính - Cửa Hàng Thời Trang</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
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
        .search-container {
            margin: 20px auto;
            text-align: center;
        }
        .search-container input[type="text"] {
            padding: 10px;
            width: 300px;
            border: 1px solid #ccc;
        }
        .search-container button {
            padding: 10px;
            background: #5cb85c;
            color: #fff;
            border: none;
            cursor: pointer;
        }
        .product-list {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            padding: 20px;
        }
        .product-item {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin: 10px;
            padding: 15px;
            width: 200px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Thêm đổ bóng */
        }
        .product-item img {
            max-width: 100%;
            height: auto;
        }
        footer {
            text-align: center;
            padding: 20px;
            background: #333;
            color: white;
            position: relative;
            bottom: 0;
            width: 100%;
        }
        .add-to-cart {
            margin-top: 15px; /* Tăng khoảng cách trên của nút */
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px 15px; /* Thay đổi padding để nút rộng hơn */
            cursor: pointer;
            display: block; /* Thay đổi thành block để căn giữa */
            width: 100%; /* Đặt chiều rộng nút bằng chiều rộng của sản phẩm */
            transition: background 0.3s, transform 0.2s; /* Thêm hiệu ứng chuyển động */
        }
        .add-to-cart:hover {
            background: #0056b3; /* Màu nền khi hover */
            transform: scale(1.05); /* Tăng kích thước nút khi hover */
        }
        input[type="number"] {
            width: 60px; /* Căn giữa ô nhập số */
            padding: 5px;
            margin-top: 10px; /* Thêm khoảng cách trên */
            border-radius: 5px;
            border: 1px solid #ced4da;
            text-align: center; /* Căn giữa nội dung trong ô nhập số */
            display: block; /* Đặt thành block để căn giữa */
            margin-left: auto; /* Căn giữa */
            margin-right: auto; /* Căn giữa */
        }
    </style>
</head>
<body>

<header>
    <h1>Cửa Hàng Thời Trang</h1>
</header>

<nav>
    <a href="index.php"><i class="fas fa-home"></i> Trang Chủ</a>
    <a href="contact.php"><i class="fas fa-envelope"></i> Liên Hệ</a>
    <a href="history.php"><i class="fas fa-history"></i> Lịch Sử Mua Hàng</a>
    <a href="products.php"><i class="fas fa-tshirt"></i> Sản Phẩm</a>
    <a href="cart.php"><i class="fas fa-shopping-cart"></i> Giỏ Hàng</a>
    <a href="profile.php"><i class="fas fa-user"></i> Tài Khoản</a>
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Đăng Xuất</a>
</nav>

<div class="search-container">
    <input type="text" placeholder="Tìm kiếm sản phẩm..." id="searchInput">
    <button onclick="searchProduct()">
        <i class="fas fa-search"></i> Tìm Kiếm
    </button>
</div>

<div class="product-list" id="productList">
    <?php foreach ($products as $product): ?>
    <div class="product-item">
        <img src="<?php echo $product['image_url']; ?>" alt="<?php echo $product['product_name']; ?>">
        <h2><?php echo $product['product_name']; ?></h2>
        <p>Giá: <?php echo number_format($product['price'], 0, ',', '.'); ?>đ</p>
        
        <!-- Form thêm vào giỏ hàng -->
        <form method="POST" action="">
            <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
            <input type="hidden" name="product_name" value="<?php echo $product['product_name']; ?>">
            <input type="hidden" name="product_price" value="<?php echo $product['price']; ?>">
            <input type="number" name="quantity" value="1" min="1" style="width: 60px; margin-top: 10px; text-align: center; display: block; margin-left: auto; margin-right: auto;">
            <button type="submit" name="add_to_cart" class="add-to-cart">
                <i class="fas fa-shopping-cart"></i> Thêm vào giỏ
            </button>
        </form>
    </div>
    <?php endforeach; ?>
</div>

<footer>
    <p>&copy; 2025 Cửa Hàng Thời Trang. Tất cả quyền được bảo lưu.</p>
</footer>

<script>
    function searchProduct() {
        const input = document.getElementById('searchInput').value.toLowerCase();
        const products = document.querySelectorAll('.product-item');

        products.forEach(product => {
            const productName = product.querySelector('h2').textContent.toLowerCase();
            if (productName.includes(input)) {
                product.style.display = 'block';
            } else {
                product.style.display = 'none';
            }
        });
    }
</script>

</body>
</html>