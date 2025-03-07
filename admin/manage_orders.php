<?php
session_start();
include __DIR__ . '/../includes/db.php';

// Xử lý xóa đơn hàng
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    $stmt = $pdo->prepare("DELETE FROM Orders WHERE order_id = :order_id");
    $stmt->bindParam(':order_id', $delete_id);
    if ($stmt->execute()) {
        // Sau khi xóa thành công, chuyển hướng lại trang quản lý đơn hàng
        header("Location: manage_orders.php");
        exit;
    } else {
        echo "Có lỗi khi xóa đơn hàng.";
    }
}

// Lấy danh sách đơn hàng cùng với tên khách hàng từ bảng User_Accounts
$stmt = $pdo->query("
    SELECT 
        o.order_id, 
        o.total_amount, 
        o.status, 
        u.full_name AS customer_name  -- Thay đổi ở đây để trỏ đến cột 'full_name' chứa tên khách hàng
    FROM Orders o
    JOIN User_Accounts u ON o.user_id = u.account_id
");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Đơn Hàng</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #28a745;
            color: white;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .button-container {
            margin-top: 20px;
            text-align: center;
        }
        a {
            text-decoration: none;
            color: #28a745;
            margin-right: 15px;
            font-weight: bold;
        }
        a:hover {
            text-decoration: underline;
        }
        .button-container a i {
            margin-right: 8px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            table, .button-container {
                width: 100%;
            }
            .button-container a {
                font-size: 16px;
                margin: 10px 0;
            }
        }
    </style>
</head>
<body>

    <h2>Quản Lý Đơn Hàng</h2>

    <h3>Danh Sách Đơn Hàng</h3>
    <table>
        <thead>
            <tr>
                <th>Mã Đơn Hàng</th>
                <th>Khách Hàng</th>
                <th>Tổng Giá</th>
                <th>Trạng Thái</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                    <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                    <td><?php echo number_format($order['total_amount'], 0, ',', '.') . ' VNĐ'; ?></td>
                    <td><?php echo htmlspecialchars($order['status']); ?></td>
                    <td>
                        <!-- Sửa đơn hàng -->
                        <a href="edit_order.php?id=<?php echo $order['order_id']; ?>"><i class="fas fa-edit"></i>Sửa</a>
                        
                        <!-- Xóa đơn hàng -->
                        <a href="?delete_id=<?php echo $order['order_id']; ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa đơn hàng này không?');">
                            <i class="fas fa-trash-alt"></i>Xóa
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="button-container">
        <a href="index_ad.php"><i class="fas fa-home"></i>Trở về trang chính</a>
        <a href="manage_products.php"><i class="fas fa-cogs"></i>Quản Lý Sản Phẩm</a>
        <a href="manage_customers.php"><i class="fas fa-users"></i>Quản Lý Khách Hàng</a>
        <a href="manage_contacts.php"><i class="fas fa-envelope"></i>Quản Lý Liên Hệ</a>
        <a href="manage_categories.php"><i class="fas fa-th-list"></i>Quản Lý Danh Mục</a>
        <a href="manage_brands.php"><i class="fas fa-tag"></i>Quản Lý Thương Hiệu</a>
    </div>

</body>
</html>
