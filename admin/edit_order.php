<?php
session_start();
include __DIR__ . '/../includes/db.php';

// Kiểm tra xem có id đơn hàng trong URL không
if (!isset($_GET['id'])) {
    echo "Không tìm thấy đơn hàng!";
    exit;
}

$order_id = (int)$_GET['id'];

// Lấy thông tin đơn hàng để sửa
$stmt = $pdo->prepare("SELECT * FROM Orders WHERE order_id = :order_id");
$stmt->bindParam(':order_id', $order_id);
$stmt->execute();
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    echo "Không tìm thấy đơn hàng!";
    exit;
}

// Xử lý sửa đơn hàng
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $status = $_POST['status'];
    $stmt = $pdo->prepare("UPDATE Orders SET status = :status WHERE order_id = :order_id");
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':order_id', $order_id);
    $stmt->execute();

    // Thông báo và chuyển hướng
    echo "<script>alert('Cập nhật trạng thái đơn hàng thành công!'); window.location.href = 'manage_orders.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa Đơn Hàng</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        .form-container {
            max-width: 500px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        label {
            font-size: 16px;
            margin-bottom: 10px;
            display: block;
            font-weight: bold;
            color: #333;
        }

        select, button {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            margin-top: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        button {
            background-color: #28a745;
            color: #fff;
            font-weight: bold;
            cursor: pointer;
            border: none;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #218838;
        }

        a {
            display: block;
            text-align: center;
            margin-top: 20px;
            text-decoration: none;
            color: #28a745;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
        }

        .alert {
            background-color: #28a745;
            color: white;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
            text-align: center;
        }

        .alert.error {
            background-color: #dc3545;
        }
    </style>
</head>
<body>

    <h2>Sửa Đơn Hàng #<?php echo $order['order_id']; ?></h2>

    <!-- Thông báo thành công hoặc lỗi -->
    <?php if (isset($order)): ?>
        <div class="alert">
            <strong>Đơn hàng hiện tại:</strong> <?php echo $order['status'] == 'pending' ? 'Chờ xử lý' : ($order['status'] == 'completed' ? 'Hoàn thành' : 'Đã hủy'); ?>
        </div>
    <?php endif; ?>

    <div class="form-container">
        <form action="" method="POST">
            <label for="status">Trạng Thái Đơn Hàng:</label>
            <select name="status" id="status">
                <option value="pending" <?php echo ($order['status'] == 'pending') ? 'selected' : ''; ?>>Chờ xử lý</option>
                <option value="completed" <?php echo ($order['status'] == 'completed') ? 'selected' : ''; ?>>Hoàn thành</option>
                <option value="canceled" <?php echo ($order['status'] == 'canceled') ? 'selected' : ''; ?>>Đã hủy</option>
            </select>

            <button type="submit">Cập nhật</button>
        </form>
    </div>

    <a href="manage_orders.php"><i class="fas fa-arrow-left"></i> Quay lại danh sách đơn hàng</a>

</body>
</html>
