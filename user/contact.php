<?php
session_start();
include '../includes/db.php';
include '../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $message = $_POST['message'];
    try {
        $stmt = $pdo->prepare("INSERT INTO Contact (message) VALUES (?)");
        $stmt->execute([$message]);
        $success = "Tin nhắn đã được gửi!";
    } catch (PDOException $e) {
        echo "Lỗi: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liên Hệ</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        header {
            background: #333;
            color: #fff;
            padding: 15px 20px;
            text-align: center;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        textarea {
            width: 100%;
            height: 150px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
            margin-bottom: 15px;
            font-size: 14px;
            resize: none; /* Không cho phép thay đổi kích thước */
        }
        button {
            background: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            display:block;
            margin:10px auto;
            padding: 12px;
            cursor: pointer;
            font-size: 16px;
            width: auto;
            transition: background 0.3s;
        }
        button:hover {
            background: #218838;
        }
        .success-message {
            color: green;
            text-align: center;
            margin-top: 15px;
            font-weight: bold;
        }
        footer {
            text-align: center;
            padding: 10px;
            background: #333;
            color: white;
            position: relative;
            bottom: 0;
            width: 100%;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<header>
    <h1>Liên Hệ Chúng Tôi</h1>
</header>

<div class="container">
    <h2>Gửi Tin Nhắn</h2>
    <form method="POST" action="">
        <textarea name="message" placeholder="Nhập tin nhắn của bạn" required></textarea>
        <button type="submit">Gửi</button>
    </form>
    <?php if (isset($success)): ?>
        <p class="success-message"><?php echo htmlspecialchars($success); ?></p>
    <?php endif; ?>
</div>

<footer>
    <p>&copy; 2025 Công ty của bạn. Bản quyền thuộc về bạn.</p>
</footer>

</body>
</html>