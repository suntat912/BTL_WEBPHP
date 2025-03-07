<?php
session_start();
ob_start(); // Bắt đầu buffer output

include '../includes/db.php';
include '../includes/header.php';

// Kết nối đến cơ sở dữ liệu
$servername = "localhost";
$username = "root"; // Tên người dùng
$password = "1234"; // Mật khẩu
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

    // Lấy thông tin sản phẩm từ cơ sở dữ liệu
    $sql_stock = "SELECT stock_quantity, sold_quantity FROM Products WHERE product_id = ?";
    $stmt = $conn->prepare($sql_stock);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $stock_result = $stmt->get_result();
    $product_data = $stock_result->fetch_assoc();

    if ($product_data) {
        $stock_quantity = $product_data['stock_quantity'];
        $sold_quantity = $product_data['sold_quantity'];

        // Kiểm tra nếu số lượng yêu cầu lớn hơn số lượng trong kho
        if ($quantity > $stock_quantity) {
            echo "<script>alert('Số lượng yêu cầu vượt quá số lượng trong kho!');</script>";
        } else {
            // Cập nhật số lượng đã bán và số lượng trong kho
            $new_sold_quantity = $sold_quantity + $quantity;
            $new_stock_quantity = $stock_quantity - $quantity;

            // Cập nhật vào cơ sở dữ liệu
            $update_sql = "UPDATE Products SET sold_quantity = ?, stock_quantity = ? WHERE product_id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("iii", $new_sold_quantity, $new_stock_quantity, $product_id);
            $update_stmt->execute();

            // Thêm sản phẩm vào giỏ hàng
            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id]['quantity'] += $quantity; // Cập nhật số lượng nếu sản phẩm đã có
            } else {
                $_SESSION['cart'][$product_id] = [
                    'name' => $_POST['product_name'],
                    'price' => $_POST['product_price'],
                    'quantity' => $quantity,
                ];
            }

            // Chuyển hướng trở lại để tránh gửi lại form
            header("Location: cart.php");
            exit();
        }
    }
}

// Lấy sản phẩm tiêu biểu
$sql_best_sellers = "SELECT * FROM Products ORDER BY sold_quantity DESC LIMIT 5";
$result_best_sellers = $conn->query($sql_best_sellers);
$best_sellers = [];
if ($result_best_sellers && $result_best_sellers->num_rows > 0) {
    while ($row = $result_best_sellers->fetch_assoc()) {
        $best_sellers[] = $row;
    }
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
            padding: 15px;
            text-align: center;
            font-size: 12px;
        }
        .search-container {
            margin: 20px auto;
            text-align: center;
        }
        .search-container input[type="text"] {
            padding: 10px;
            width: 300px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .search-container button {
            padding: 10px;
            background: #5cb85c;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .search-container button:hover {
            background: #4cae4c;
        }
        .slider {
            margin: 20px auto;
            overflow: hidden;
            white-space: nowrap;
            width: calc(100% - 40px);
            position: relative;
        }
        .slider-inner {
            display: inline-block;
            transition: transform 0.5s ease;
            /* Đặt chiều rộng cho slider-inner */
        }
        .slider-item {
            display: inline-block;
            width: 220px; /* Đặt chiều rộng cho mỗi sản phẩm */
            margin: 0 10px;
            text-align: center;
        }
        .slider-item img {
            max-width: 100%;
            height: auto;
            border-radius: 5px;
        }
        .arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.7);
            border: none;
            border-radius: 50%;
            padding: 10px;
            cursor: pointer;
            z-index: 10;
        }
        .arrow.left {
            left: 10px;
        }
        .arrow.right {
            right: 10px;
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
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }
        .product-item:hover {
            transform: scale(1.05);
        }
        .product-item img {
            max-width: 100%;
            height: auto;
            border-radius: 5px;
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
            margin-top: 15px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px 15px;
            cursor: pointer;
            display: block;
            width: 100%;
            transition: background 0.3s, transform 0.2s;
        }
        .add-to-cart:hover {
            background: #0056b3;
            transform: scale(1.05);
        }
        input[type="number"] {
            width: 60px;
            padding: 5px;
            margin-top: 10px;
            border-radius: 5px;
            border: 1px solid #ced4da;
            text-align: center;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
        .out-of-stock {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>

<header>
    <h1>Chào mừng đến với cửa hàng của nem</h1>
</header>

<div class="search-container">
    <input type="text" placeholder="Tìm kiếm sản phẩm..." id="searchInput">
    <button onclick="searchProduct()">
        <i class="fas fa-search"></i> Tìm Kiếm
    </button>
</div>

<header>
    <h1>Sản phẩm bán chạy</h1>
</header>
<!-- Slider cho sản phẩm bán chạy -->
<div class="slider">
    <button class="arrow left" onclick="moveSlide(-1)">
        <i class="fas fa-chevron-left"></i>
    </button>
    <div class="slider-inner" id="sliderInner">
        <?php foreach ($best_sellers as $product): ?>
        <div class="slider-item">
            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
            <h3><?php echo htmlspecialchars($product['product_name']); ?></h3>
        </div>
        <?php endforeach; ?>

        <!-- Lặp lại sản phẩm để tạo hiệu ứng nối đuôi -->
        <?php foreach ($best_sellers as $product): ?>
        <div class="slider-item">
            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
            <h3><?php echo htmlspecialchars($product['product_name']); ?></h3>
        </div>
        <?php endforeach; ?>
    </div>
    <button class="arrow right" onclick="moveSlide(1)">
        <i class="fas fa-chevron-right"></i>
    </button>
</div>

<header>
    <h1>Sản phẩm nổi bật</h1>
</header>

<div class="product-list" id="productList">
    <?php foreach ($products as $product): ?>
    <div class="product-item">
        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
        <h2><?php echo htmlspecialchars($product['product_name']); ?></h2>
        <p>Giá: <?php echo number_format($product['price'], 0, ',', '.'); ?>đ</p>
        <p>Số lượng còn lại: <?php echo $product['stock_quantity']; ?></p>

        <!-- Form thêm vào giỏ hàng -->
        <?php if ($product['stock_quantity'] > 0): ?>
            <form method="POST" action="">
                <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($product['product_name']); ?>">
                <input type="hidden" name="product_price" value="<?php echo $product['price']; ?>">
                <input type="number" name="quantity" value="1" min="1" max="<?php echo $product['stock_quantity']; ?>">
                <button type="submit" name="add_to_cart" class="add-to-cart">
                    <i class="fas fa-shopping-cart"></i> Thêm vào giỏ
                </button>
            </form>
        <?php else: ?>
            <p class="out-of-stock">Hết hàng</p>
        <?php endif; ?>
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

    let currentIndex = 0;
    const sliderInner = document.getElementById('sliderInner');
    const totalItems = sliderInner.children.length / 2; // Tổng số sản phẩm gốc

    function moveSlide(direction) {
        currentIndex += direction;
        if (currentIndex < 0) {
            currentIndex = totalItems - 1; // Quay về sản phẩm cuối cùng
        } else if (currentIndex >= totalItems) {
            currentIndex = 0; // Quay về sản phẩm đầu tiên
        }

        const itemWidth = 220; // Độ rộng của mỗi slider-item
        sliderInner.style.transform = `translateX(-${currentIndex * itemWidth}px)`;
    }

    // Tự động chạy slider
    let autoSlide = setInterval(() => moveSlide(1), 3000); // Chuyển slide mỗi 3 giây

    // Dừng tự động chạy khi người dùng tương tác
    const slider = document.querySelector('.slider');
    slider.addEventListener('mouseover', () => clearInterval(autoSlide));
    slider.addEventListener('mouseout', () => {
        autoSlide = setInterval(() => moveSlide(1), 3000);
    });
</script>

</body>
</html>
<?php ob_end_flush(); // Kết thúc buffer output và gửi nội dung ?>