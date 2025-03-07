<?php
session_start();
include '../includes/db.php';
include '../includes/header.php';

$total_amount = 0; // Khai báo biến tổng

// Kiểm tra xem có sản phẩm trong giỏ hàng không
if (empty($_SESSION['cart'])) {
    header("Location: cart.php"); // Chuyển hướng về trang giỏ hàng nếu giỏ hàng trống
    exit;
}

// Tính toán tổng tiền và lấy thông tin sản phẩm
foreach ($_SESSION['cart'] as $product_id => $item) {
    $quantity = $item['quantity']; // Lấy số lượng
    $stmt = $pdo->prepare("SELECT price, product_name FROM Products WHERE product_id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();
    
    // Cộng dồn tổng tiền
    if ($product) {
        $total_amount += $product['price'] * $quantity; // Tính tổng
    }
}

// Xử lý đặt hàng
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $shipping_address = $_POST['shipping_address'];

    // Lưu thông tin đơn hàng vào cơ sở dữ liệu
    $stmt = $pdo->prepare("INSERT INTO Orders (user_id, total_amount, shipping_address) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $total_amount, $shipping_address]);

    // Xóa giỏ hàng
    $_SESSION['cart'] = [];
    header("Location: ../index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt Hàng</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        h2 {
            text-align: center;
        }
        .order-summary {
            background: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin: 20px auto;
            padding: 20px;
            max-width: 600px;
        }
        .product-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        .textarea-container {
            display: flex;
            justify-content: center; /* Căn giữa ô nhập liệu */
            margin-bottom: 20px; /* Khoảng cách dưới ô nhập liệu */
        }
        textarea {
            width: 100%;
            max-width: 500px; /* Đặt chiều rộng tối đa */
            height: 40px; /* Chiều cao nhỏ */
            padding: 5px; /* Padding nhỏ hơn */
            border-radius: 5px;
            border: 1px solid #ddd;
            resize: none; /* Không cho phép thay đổi kích thước */
            box-sizing: border-box; /* Đảm bảo padding không làm tăng chiều rộng */
        }
        button {
            display: block;
            width: auto;
            padding: 8px 15px;
            font-size: 14px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 0 auto; /* Canh giữa nút */
        }
        button:hover {
            background: #218838;
        }
    </style>
</head>
<body>
    <h2>Đặt Hàng</h2>
    <div class="order-summary">
        <h3>Sản Phẩm Trong Giỏ Hàng</h3>
        <?php if (empty($_SESSION['cart'])): ?>
            <p>Giỏ hàng của bạn đang trống.</p>
        <?php else: ?>
            <?php foreach ($_SESSION['cart'] as $product_id => $item): ?>
                <?php
                $quantity = $item['quantity']; // Lấy số lượng
                $stmt = $pdo->prepare("SELECT price, product_name FROM Products WHERE product_id = ?");
                $stmt->execute([$product_id]);
                $product = $stmt->fetch();
                ?>
                <div class="product-item">
                    <span><?php echo htmlspecialchars($product['product_name']); ?> (<?php echo $quantity; ?>)</span>
                    <span><?php echo number_format($product['price'], 0, ',', '.'); ?>đ</span>
                </div>
            <?php endforeach; ?>
            <div class="product-item">
                <strong>Tổng Cộng:</strong>
                <strong><?php echo number_format($total_amount, 0, ',', '.'); ?>đ</strong>
            </div>
        <?php endif; ?>
    </div>

    <form method="POST" action="">
        <div class="textarea-container">
            <textarea name="shipping_address" placeholder="Địa chỉ giao hàng" required></textarea>
        </div>
        <button type="submit">Xác Nhận Đặt Hàng</button>
    </form>
</body>
</html>