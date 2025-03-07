<?php
session_start();
// Kết nối đến cơ sở dữ liệu
$servername = "localhost";
$username = "root"; // Tên người dùng
$password = "1234"; // Mật khẩu
$dbname = "fashion_store"; // Tên cơ sở dữ liệu

$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];

    // Kiểm tra xem email có tồn tại không
    $sql = "SELECT * FROM User_Accounts WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Tạo mã xác nhận và gửi email (cần cài đặt hàm gửi email)
        $token = bin2hex(random_bytes(50)); // Tạo mã xác nhận ngẫu nhiên
        // Lưu mã xác nhận vào cơ sở dữ liệu (có thể tạo bảng mới để lưu mã xác nhận)
        $conn->query("UPDATE User_Accounts SET reset_token = '$token' WHERE email = '$email'");

        // Gửi email chứa liên kết đổi mật khẩu (cần cài đặt hàm gửi email)
        // Ví dụ: gửi email chứa liên kết đến `reset_password.php?token=$token`

        $message = "Một liên kết để đặt lại mật khẩu đã được gửi đến email của bạn.";
    } else {
        $message = "Email không tồn tại.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên Mật Khẩu</title>
</head>
<body>
    <h2>Quên Mật Khẩu</h2>
    <?php if ($message): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>
    <form method="POST" action="">
        <input type="email" name="email" placeholder="Nhập email của bạn" required>
        <button type="submit">Gửi liên kết đặt lại mật khẩu</button>
    </form>
</body>
</html>