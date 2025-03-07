<?php
session_start();
ob_start();

include 'C:/xampp/htdocs/fashion_store/includes/db.php';
include 'C:/xampp/htdocs/fashion_store/includes/header.php';

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login_register.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Kiểm tra nếu giỏ hàng trống
if (empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit();
}

// Tính tổng tiền
$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['quantity'];
}

// Mã giảm giá và tính toán
$discount = 0;
$discount_message = "";
$discount_amount = 0;

if (isset($_POST['discount_code']) && !empty($_POST['discount_code'])) {
    $discount_codes = [
        'DISCOUNT10' => 10,
        'SAVE15' => 15,
        'FASHION20' => 20,
    ];
    $code = $_POST['discount_code'];
    if (array_key_exists($code, $discount_codes)) {
        $discount = $discount_codes[$code];
        $discount_amount = ($discount / 100) * $total;
        $total_after_discount = $total - $discount_amount;
        $discount_message = "Mã giảm giá đã được áp dụng!";
    } else {
        $discount_message = "Mã giảm giá không hợp lệ!";
    }
} else {
    $total_after_discount = $total;
}

// Xử lý thanh toán
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['checkout'])) {
    $address = $_POST['address'];
    $payment_method = $_POST['payment_method'];

    $stmt = $pdo->prepare("INSERT INTO Orders (user_id, original_amount, discount_amount, total_amount, shipping_address, order_date) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $user_id,
        $total,
        $discount_amount,
        $total_after_discount,
        $address,
        date('Y-m-d H:i:s')
    ]);

    unset($_SESSION['cart']);
    header("Location: index.php");
    exit();
}

ob_end_flush();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh Toán</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        header {
            background: #343a40;
            color: #fff;
            padding: 15px 20px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #343a40;
        }
        .total {
            font-size: 18px;
            margin: 10px 0;
            color: #333;
        }
        .discount-container {
            margin: 20px 0;
        }
        .discount-input, .address-input {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ced4da;
            margin-top: 10px;
        }
        button {
            background: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px 15px;
            cursor: pointer;
            transition: background 0.3s;
            width: 100%;
            margin-top: 10px;
        }
        button:hover {
            background: #218838;
        }
        .message {
            color: red;
            text-align: center;
            margin: 10px 0;
        }
        .qr-code {
            display: none; /* Ẩn mã QR mặc định */
            text-align: center;
            margin-top: 20px;
        }
        .qr-code img {
            max-width: 100%;
            height: auto;
        }
    </style>
    <script>
        function toggleQRCode() {
            const qrCodeDiv = document.getElementById('qrCode');
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;

            if (paymentMethod === 'qr') {
                qrCodeDiv.style.display = 'block';
            } else {
                qrCodeDiv.style.display = 'none';
            }
        }
    </script>
</head>
<body>

<header>
    <h1>Thanh Toán</h1>
</header>

<div class="container">
    <h2>Thông Tin Thanh Toán</h2>
    
    <p class="total">Tổng Tiền: <?php echo number_format($total, 0, ',', '.'); ?> VNĐ</p>
    <p class="total">Giảm giá: <?php echo $discount; ?>%</p>
    <p class="total">Số tiền giảm: <?php echo number_format($discount_amount, 0, ',', '.'); ?> VNĐ</p>
    <p class="total">Tổng Tiền Sau Giảm Giá: <?php echo number_format($total_after_discount, 0, ',', '.'); ?> VNĐ</p>
    
    <?php if ($discount_message): ?>
        <div class="message"><?php echo $discount_message; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="discount-container">
            <label for="discount_code">Nhập Mã Giảm Giá:</label>
            <input type="text" id="discount_code" name="discount_code" class="discount-input" placeholder="Nhập mã giảm giá">
        </div>

        <div class="payment-method">
            <label>
                <input type="radio" name="payment_method" value="qr" required onchange="toggleQRCode()"> Thanh Toán Qua Mã QR
            </label>
            <label>
                <input type="radio" name="payment_method" value="cod" required onchange="toggleQRCode()"> Thanh Toán Khi Nhận Hàng
            </label>
        </div>
        
        <label for="address">Địa Chỉ Giao Hàng:</label>
        <input type="text" id="address" name="address" class="address-input" placeholder="Nhập địa chỉ giao hàng" required>

        <button type="submit" name="checkout">Xác Nhận Thanh Toán</button>
    </form>

    <div id="qrCode" class="qr-code">
        <h3>Mã QR Thanh Toán</h3>
        <img id="qrImage" src="https://th.bing.com/th/id/OIP.EkHkZtNy08DSPXM3uiItjgAAAA?w=399&h=458&rs=1&pid=ImgDetMain" alt="QR Code" />
        <p>Quét mã QR này để thanh toán.</p>
    </div>
</div>

</body>
</html>