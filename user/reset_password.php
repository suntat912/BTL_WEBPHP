<?php
session_start();
$servername = "localhost";
$username = "root"; // Thay đổi với tên người dùng của bạn
$password = "Nghiacoi2212@"; // Thay đổi với mật khẩu của bạn
$dbname = "fashion_store"; // Tên cơ sở dữ liệu

$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_password = $_POST['new_password'];
    $token = $_POST['token'];

    // Cập nhật mật khẩu mới
    $sql = "UPDATE User_Accounts SET password = '$new_password', reset_token = NULL WHERE reset_token = '$token'";
    if ($conn->query($sql) === TRUE) {
        $message = "Mật khẩu đã được đặt lại thành công.";
    } else {
        $message = "Có lỗi xảy ra. Vui lòng thử lại.";
    }
}

// Kiểm tra token hợp lệ
$token = $_GET['token'] ?? '';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt Lại Mật Khẩu</title>
</head>
<body>
    <h2>Đặt Lại Mật Khẩu</h2>
    <form method="POST" action="">
        <input type="hidden" name="token" value="<?php echo $token; ?>">
        <input type="password" name="new_password" placeholder="Nhập mật khẩu mới" required>
        <button type="submit">Đặt lại mật khẩu</button>
    </form>
    <?php if ($message): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>
</body>
</html>