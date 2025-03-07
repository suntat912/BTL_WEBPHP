<?php
session_start();
ob_start();
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

// Lấy danh sách danh mục từ cơ sở dữ liệu
$categories = [];
$sql_categories = "SELECT * FROM Categories";
$result_categories = $conn->query($sql_categories);
if ($result_categories && $result_categories->num_rows > 0) {
    while ($row = $result_categories->fetch_assoc()) {
        $categories[] = $row;
    }
}

// Lấy sản phẩm theo từng danh mục
$products_by_category = [];
foreach ($categories as $category) {
    $cat_id = $category['category_id'];
    $sql_products = "SELECT p.* FROM Products p
                     JOIN Product_Categories pc ON p.product_id = pc.product_id
                     WHERE pc.category_id = ?";
    $stmt = $conn->prepare($sql_products);
    $stmt->bind_param("i", $cat_id);
    $stmt->execute();
    $result_products = $stmt->get_result();

    if ($result_products && $result_products->num_rows > 0) {
        while ($row = $result_products->fetch_assoc()) {
            $products_by_category[$cat_id][] = $row;
        }
    }
}

// Xử lý thêm vào giỏ hàng
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'] ?? 1; // Số lượng mặc định là 1

    // Kiểm tra số lượng trong kho
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
        .menu {
            text-align: center;
            margin: 20px 0;
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
        .category {
            margin: 20px;
        }
        .slider {
            position: relative;
            max-width: 100%;
            overflow: hidden;
            margin: 20px auto;
        }
        .slides {
            display: flex;
            transition: transform 0.5s ease;
            width: calc(100% * 2); /* Nhân đôi chiều rộng để tạo hiệu ứng nối đuôi */
        }
        .slider-item {
            display: inline-block;
            width: 220px; /* Chiều rộng cho mỗi sản phẩm */
            margin: 0 10px;
            text-align: center;
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
        }
        .product-item img {
            max-width: 100%;
            height: auto;
        }
        .arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.5);
            border: none;
            padding: 10px;
            cursor: pointer;
            z-index: 10;
        }
        .arrow-left {
            left: 10px;
        }
        .arrow-right {
            right: 10px;
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
    <h1>Danh mục các sản phẩm</h1>
</header>

<div class="search-container">
    <input type="text" placeholder="Tìm kiếm sản phẩm..." id="searchInput">
    <button onclick="searchProduct()">
        <i class="fas fa-search"></i> Tìm Kiếm
    </button>
</div>

<?php foreach ($categories as $category): ?>
    <div class="category" id="category<?php echo $category['category_id']; ?>">
        <h2><?php echo htmlspecialchars($category['category_name']); ?></h2>
        <div class="slider">
            <button class="arrow arrow-left" onclick="moveSlide(event, -1)">&#10094;</button>
            <div class="slides" data-current-index="0">
                <?php if (isset($products_by_category[$category['category_id']])): ?>
                    <?php foreach ($products_by_category[$category['category_id']] as $product): ?>
                        <div class="slider-item">
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
                        </div>
                    <?php endforeach; ?>
                    <?php foreach ($products_by_category[$category['category_id']] as $product): // Nhân bản sản phẩm để tạo hiệu ứng nối đuôi ?>
                        <div class="slider-item">
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
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Không có sản phẩm nào trong danh mục này.</p>
                <?php endif; ?>
            </div>
            <button class="arrow arrow-right" onclick="moveSlide(event, 1)">&#10095;</button>
        </div>
    </div>
<?php endforeach; ?>

<footer>
    <p>&copy; 2025 Cửa Hàng Thời Trang. Tất cả quyền được bảo lưu.</p>
</footer>

<script>
    function moveSlide(event, direction) {
        const slider = event.target.closest('.slider');
        const slides = slider.querySelector('.slides');
        const totalSlides = slides.children.length / 2; // Tổng số sản phẩm gốc
        let currentIndex = parseInt(slides.dataset.currentIndex) || 0;

        currentIndex = (currentIndex + direction + totalSlides) % totalSlides;
        slides.dataset.currentIndex = currentIndex; // Lưu trạng thái hiện tại

        const itemWidth = 220; // Chiều rộng của mỗi sản phẩm
        slides.style.transform = `translateX(${-currentIndex * itemWidth}px)`;
    }

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
<?php ob_end_flush(); // Kết thúc buffer output và gửi nội dung ?>