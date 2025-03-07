<?php
session_start();
ob_start();

include '../includes/db.php';
include '../includes/header.php';

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login_register.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM Orders WHERE user_id = ? ORDER BY order_date DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Lịch Sử Đơn Hàng</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        h2 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #ddd;
        }
    </style>
</head>
<body>

<h2>Lịch Sử Đơn Hàng</h2>
<table>
    <tr>
        <th>Mã Đơn Hàng</th>
        <th>Giá Gốc</th>
        <th>Số Tiền Giảm</th>
        <th>Tổng Tiền (Sau Giảm)</th>
        <th>Địa Chỉ Giao Hàng</th>
        <th>Ngày Đặt</th>
    </tr>
    <?php if (count($orders) > 0): ?>
        <?php foreach ($orders as $order): ?>
            <tr>
                <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                <td><?php echo htmlspecialchars(number_format($order['original_amount'], 0, ',', '.')) . " VNĐ"; ?></td>
                <td><?php echo htmlspecialchars(number_format($order['discount_amount'], 0, ',', '.')) . " VNĐ"; ?></td>
                <td><?php echo htmlspecialchars(number_format($order['total_amount'], 0, ',', '.')) . " VNĐ"; ?></td>
                <td><?php echo htmlspecialchars($order['shipping_address']); ?></td>
                <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($order['order_date']))); ?></td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="6" style="text-align: center;">Bạn chưa có đơn hàng nào.</td>
        </tr>
    <?php endif; ?>
</table>

</body>
</html>