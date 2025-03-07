<?php
session_start();
include __DIR__ . '/../includes/db.php';

// Lấy thông tin chi tiết đơn hàng
$order_id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM Order_Details WHERE order_id = ?");
$stmt->execute([$order_id]);
$order_items = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Đơn Hàng</title>
    <!-- Thêm FontAwesome cho biểu tượng -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        h2 {
            color: #28a745;
            font-size: 36px;
            text-align: center;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 15px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #28a745;
            color: white;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .button-container {
            text-align: center;
            margin-top: 30px;
        }

        .button-container a {
            text-decoration: none;
            color: #007bff;
            font-size: 18px;
            font-weight: bold;
            transition: color 0.3s;
        }

        .button-container a:hover {
            color: #0056b3;
        }

        .button-container a i {
            margin-right: 8px;
        }

        /* Responsive design */
        @media screen and (max-width: 768px) {
            table {
                width: 100%;
            }
            th, td {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>

    <h2>Chi Tiết Đơn Hàng #<?php echo htmlspecialchars($order_id); ?></h2>

    <!-- Hiển thị chi tiết đơn hàng -->
    <table>
        <tr>
            <th>Tên Sản Phẩm</th>
            <th>Số Lượng</th>
            <th>Giá</th>
        </tr>
        <?php foreach ($order_items as $item): ?>
            <tr>
                <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                <td><?php echo number_format($item['price'], 0, ',', '.') . ' VNĐ'; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <div class="button-container">
        <a href="manage_orders.php"><i class="fas fa-arrow-left"></i> Quay lại</a>
    </div>

</body>
</html>
