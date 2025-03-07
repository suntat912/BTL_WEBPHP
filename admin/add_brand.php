<?php
session_start();
include __DIR__ . '/../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $brand_name = trim($_POST["brand_name"]);

    if (!empty($brand_name)) {
        try {
            // Kiểm tra xem thương hiệu đã tồn tại chưa
            $stmt = $pdo->prepare("SELECT * FROM Brands WHERE brand_name = ?");
            $stmt->execute([$brand_name]);

            if ($stmt->rowCount() > 0) {
                header("Location: manage_brands.php?error=Thương hiệu đã tồn tại.");
                exit;
            }

            // Chèn thương hiệu mới
            $stmt = $pdo->prepare("INSERT INTO Brands (brand_name) VALUES (?)");
            $stmt->execute([$brand_name]);

            header("Location: manage_brands.php?success=Thêm thương hiệu thành công!");
            exit;
        } catch (PDOException $e) {
            header("Location: manage_brands.php?error=Lỗi khi thêm thương hiệu.");
            exit;
        }
    } else {
        header("Location: manage_brands.php?error=Vui lòng nhập tên thương hiệu.");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Thương Hiệu</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
            color: #333;
            text-align: center;
        }
        h2 {
            color: #28a745;
        }
        form {
            background-color: white;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            width: 40%;
            margin: 0 auto;
            border-radius: 8px;
        }
        input[type="text"] {
            width: 90%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            background-color: #28a745;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
        }
        a {
            display: block;
            margin-top: 15px;
            color: #007bff;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <h2>Thêm Thương Hiệu</h2>

    <form method="POST">
        <label for="brand_name">Tên Thương Hiệu:</label><br>
        <input type="text" id="brand_name" name="brand_name" required><br>
        <button type="submit">Thêm</button>
    </form>

    <a href="manage_brands.php"><i class="fas fa-arrow-left"></i> Quay lại</a>

</body>
</html>
