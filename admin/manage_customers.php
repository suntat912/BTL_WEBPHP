<?php
ob_start(); // Bắt đầu buffer output
session_start();
include __DIR__ . '/../includes/db.php';

// Lấy danh sách khách hàng
$stmt = $pdo->query("SELECT * FROM User_Accounts");
$customers = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Khách Hàng</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .container {
            width: 85%;
            margin: 0 auto;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            padding: 20px;
        }
        h2, h3 {
            color: #28a745;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
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
            background-color: #f9f9f9;
        }
        .button-container {
            margin-top: 30px;
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
        .form-container {
            background-color: #f9f9f9;
            padding: 20px;
            margin-top: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            font-weight: bold;
            display: inline-block;
            width: 200px;
        }
        input, select {
            width: calc(100% - 16px);
            padding: 10px;
            margin-top: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
            background-color: #f9f9f9;
        }
        button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Quản Lý Khách Hàng</h2>

        <h3>Danh Sách Khách Hàng</h3>
        <table>
            <thead>
                <tr>
                    <th>Tên Khách Hàng</th>
                    <th>Email</th>
                    <th>Điện Thoại</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($customers as $customer): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($customer['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($customer['email']); ?></td>
                        <td><?php echo htmlspecialchars($customer['phone']); ?></td>
                        <td>
                            <a href="edit_customer.php?id=<?php echo urlencode($customer['account_id']); ?>">
                                <i class="fas fa-edit"></i> Sửa</a> | 
                            <a href="delete_customer.php?id=<?php echo urlencode($customer['account_id']); ?>" 
                               onclick="return confirm('Bạn có chắc chắn muốn xóa khách hàng này không?');">
                                <i class="fas fa-trash"></i> Xóa</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Form thêm khách hàng -->
        <div class="form-container">
            <h3>Thêm Khách Hàng Mới</h3>
            <form action="add_customer.php" method="POST">
                <div class="form-group">
                    <label for="username">Tên Đăng Nhập:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Mật khẩu:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="full_name">Họ và Tên:</label>
                    <input type="text" id="full_name" name="full_name" required>
                </div>
                <div class="form-group">
                    <label for="phone">Số Điện Thoại:</label>
                    <input type="text" id="phone" name="phone">
                </div>
                <div class="form-group">
                    <label for="gender">Giới Tính:</label>
                    <select id="gender" name="gender">
                        <option value="male">Nam</option>
                        <option value="female">Nữ</option>
                        <option value="other">Khác</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="dob">Ngày Sinh:</label>
                    <input type="date" id="dob" name="dob">
                </div>
                <button type="submit"><i class="fas fa-plus"></i> Thêm Khách Hàng</button>
            </form>
        </div>

        <!-- Điều hướng các trang khác -->
        <div class="button-container">
            <a href="index_ad.php"><i class="fas fa-home"></i> Trở về trang chính</a>
            <a href="manage_products.php"><i class="fas fa-cogs"></i> Quản Lý Sản Phẩm</a>
            <a href="manage_orders.php"><i class="fas fa-box"></i> Quản Lý Đơn Hàng</a>
            <a href="manage_contacts.php"><i class="fas fa-envelope"></i> Quản Lý Liên Hệ</a>
            <a href="manage_categories.php"><i class="fas fa-th-list"></i> Quản Lý Danh Mục</a>
            <a href="manage_brands.php"><i class="fas fa-clipboard-list"></i> Quản Lý Thương Hiệu</a>
        </div>
    </div>

</body>
</html>