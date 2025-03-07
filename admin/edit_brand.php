<?php
session_start();
include __DIR__ . '/../includes/db.php';

// Kiểm tra xem có ID hợp lệ không
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID không hợp lệ.");
}

$brand_id = intval($_GET['id']);
$message = "";

// Lấy thông tin thương hiệu hiện tại
try {
    $stmt = $pdo->prepare("SELECT brand_name FROM Brands WHERE brand_id = ?");
    $stmt->execute([$brand_id]);
    $brand = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$brand) {
        die("Thương hiệu không tồn tại.");
    }
} catch (PDOException $e) {
    die("Lỗi khi lấy dữ liệu: " . $e->getMessage());
}

// Xử lý khi gửi form cập nhật
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_brand_name = trim($_POST['brand_name']);

    // Kiểm tra đầu vào
    if (empty($new_brand_name)) {
        $message = "Tên thương hiệu không được để trống.";
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE Brands SET brand_name = ?, updated_at = NOW() WHERE brand_id = ?");
            $stmt->execute([$new_brand_name, $brand_id]);
            $message = "Cập nhật thành công!";
            $brand['brand_name'] = $new_brand_name; // Cập nhật giá trị hiển thị
        } catch (PDOException $e) {
            $message = "Lỗi khi cập nhật: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh Sửa Thương Hiệu</title>
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
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            background: white;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        h2 {
            text-align: center;
            color: #28a745;
        }
        .message {
            text-align: center;
            font-size: 16px;
            margin-bottom: 15px;
            color: #d9534f;
        }
        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
        }
        button:hover {
            background-color: #218838;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            text-decoration: none;
            color: #28a745;
            font-weight: bold;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Chỉnh Sửa Thương Hiệu</h2>
    
    <?php if ($message): ?>
        <p class="message"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <form method="post">
        <label for="brand_name">Tên thương hiệu:</label>
        <input type="text" id="brand_name" name="brand_name" value="<?php echo htmlspecialchars($brand['brand_name']); ?>" required>
        <button type="submit"><i class="fas fa-save"></i> Lưu</button>
    </form>

    <a href="manage_brands.php" class="back-link"><i class="fas fa-arrow-left"></i> Quay lại danh sách</a>
</div>

</body>
</html>
