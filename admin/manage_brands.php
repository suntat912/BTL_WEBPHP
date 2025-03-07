<?php
session_start();
include __DIR__ . '/../includes/db.php';

try {
    $stmt = $pdo->prepare("SELECT brand_id, brand_name FROM Brands ORDER BY brand_name ASC");
    $stmt->execute();
    $brands = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Lỗi kết nối CSDL: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Thương Hiệu</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .container {
            max-width: 900px;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        h2, h3 {
            color: #28a745;
            text-align: center;
        }
        .message {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 5px;
        }
        .success { background-color: #d4edda; color: #155724; }
        .error { background-color: #f8d7da; color: #721c24; }
        
        .table-container {
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #28a745;
            color: white;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .actions a {
            padding: 8px 12px;
            border-radius: 5px;
            text-decoration: none;
            color: white;
            font-size: 14px;
        }
        .edit-btn { background: #ffc107; }
        .delete-btn { background: #dc3545; }
        .actions a:hover { opacity: 0.8; }
        
        .button-container {
            text-align: center;
            margin-top: 20px;
        }
        .btn {
            display: inline-block;
            background-color: #28a745;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
        }
        .btn:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Quản Lý Thương Hiệu</h2>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="message success">Thương hiệu đã được xóa thành công!</div>
        <?php elseif (isset($_GET['error'])): ?>
            <div class="message error">Lỗi khi xóa thương hiệu! Vui lòng thử lại.</div>
        <?php endif; ?>
        
        <div class="button-container">
            <a href="add_brand.php" class="btn"><i class="fas fa-plus"></i> Thêm Thương Hiệu</a>
        </div>
        
        <h3>Danh Sách Thương Hiệu</h3>
        <div class="table-container">
            <?php if (!empty($brands)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Tên Thương Hiệu</th>
                            <th style="text-align: center;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($brands as $brand): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($brand['brand_name']); ?></td>
                                <td style="text-align: center;">
                                    <div class="actions">
                                        <a href="edit_brand.php?id=<?php echo urlencode($brand['brand_id']); ?>" class="edit-btn">
                                            <i class="fas fa-edit"></i> Sửa
                                        </a>
                                        <a href="delete_brand.php?id=<?php echo urlencode($brand['brand_id']); ?>" class="delete-btn" onclick="return confirm('Bạn có chắc chắn muốn xóa thương hiệu này không?');">
                                            <i class="fas fa-trash-alt"></i> Xóa
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="text-align: center; color: #d9534f;">Chưa có thương hiệu nào.</p>
            <?php endif; ?>
        </div>
        
        <div class="button-container">
            <a href="index_ad.php" class="btn"><i class="fas fa-home"></i> Trang Chủ</a>
            <a href="manage_products.php" class="btn"><i class="fas fa-cogs"></i> Sản Phẩm</a>
            <a href="manage_orders.php" class="btn"><i class="fas fa-box"></i> Đơn Hàng</a>
            <a href="manage_customers.php" class="btn"><i class="fas fa-users"></i> Khách Hàng</a>
            <a href="manage_contacts.php" class="btn"><i class="fas fa-envelope"></i> Liên Hệ</a>
            <a href="manage_categories.php" class="btn"><i class="fas fa-th-list"></i> Danh Mục</a>
        </div>
    </div>
</body>
</html>