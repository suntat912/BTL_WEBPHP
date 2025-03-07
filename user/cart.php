<?php
session_start();
include '../includes/db.php';
include '../includes/header.php';

// Kiểm tra nếu người dùng đã thêm sản phẩm vào giỏ
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Xử lý xóa sản phẩm
if (isset($_GET['action']) && $_GET['action'] == 'remove') {
    $product_id = $_GET['id'];
    unset($_SESSION['cart'][$product_id]);
}

// Xử lý sửa sản phẩm
if (isset($_POST['update'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $_SESSION['cart'][$product_id]['quantity'] = $quantity;
}

// Không thêm sản phẩm mẫu vào giỏ hàng
$total = 0;

// Tính tổng tiền
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ Hàng</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa; /* Màu nền nhẹ nhàng */
        }

        header {
            background: #343a40; /* Màu tối cho header */
            color: #fff;
            padding: 15px 20px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Đổ bóng cho header */
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1); /* Đổ bóng nhẹ cho container */
        }

        h2 {
            text-align: center;
            color: #343a40; /* Màu tối cho tiêu đề */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 15px;
            border: 1px solid #dee2e6; /* Viền sáng cho ô */
            text-align: left;
        }

        th {
            background: #007bff; /* Màu nền cho tiêu đề bảng */
            color: #fff;
        }

        td {
            background: #f9f9f9; /* Màu nền nhẹ cho ô dữ liệu */
        }

        td:nth-child(even) {
            background: #f1f1f1; /* Đổi màu cho các ô chẵn */
        }

        input[type="number"] {
            width: 60px;
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ced4da; /* Viền sáng cho ô nhập số */
        }

        button {
            background: #28a745; /* Màu xanh cho nút */
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px 15px;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover {
            background: #218838; /* Màu xanh đậm khi hover */
        }

        .remove-button {
            background: #dc3545; /* Màu đỏ cho nút xóa */
            color: white;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 5px;
        }

        .remove-button:hover {
            background: #c82333; /* Màu đỏ đậm khi hover */
        }

        .payment-button {
            margin-top: 20px;
            background: #007bff; /* Màu xanh cho nút thanh toán */
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            display: block;
            width: 100%;
            text-align: center;
            transition: background 0.3s;
        }

        .payment-button:hover {
            background: #0056b3; /* Màu xanh đậm khi hover */
        }
    </style>
</head>
<body>

<header>
    <h1>Giỏ Hàng Của Bạn</h1>
</header>

<div class="container">
    <h2>Danh Sách Sản Phẩm</h2>
    <table>
        <thead>
            <tr>
                <th>Tên Sản Phẩm</th>
                <th>Giá</th>
                <th>Số Lượng</th>
                <th>Tổng</th>
                <th>Hành Động</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($_SESSION['cart'])): ?>
                <tr>
                    <td colspan="5" style="text-align: center;">Giỏ hàng trống.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($_SESSION['cart'] as $id => $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                        <td><?php echo number_format($item['price'], 0, ',', '.'); ?> VNĐ</td>
                        <td>
                            <form method="POST" action="">
                                <input type="hidden" name="product_id" value="<?php echo $id; ?>">
                                <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" required>
                                <button type="submit" name="update">Cập Nhật</button>
                            </form>
                        </td>
                        <td><?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?> VNĐ</td>
                        <td>
                            <a href="?action=remove&id=<?php echo $id; ?>" class="remove-button">
                                <i class="fas fa-trash-alt"></i> Xóa
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <h3>Tổng Cộng: <?php echo number_format($total, 0, ',', '.'); ?> VNĐ</h3>

    <!-- Nút thanh toán -->
    <form method="GET" action="checkout.php">
        <button type="submit" class="payment-button">Thanh Toán</button>
    </form>
</div>

</body>
</html>