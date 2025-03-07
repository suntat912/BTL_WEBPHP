<?php
$host = 'localhost'; // Địa chỉ máy chủ cơ sở dữ liệu
$db = 'fashion_store'; // Tên cơ sở dữ liệu
$user = 'root'; // Tên người dùng
$pass = 'Nghiacoi2212@'; // Mật khẩu

try {
    // Tạo kết nối PDO
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Thiết lập chế độ lỗi
} catch (PDOException $e) {
    // Xử lý lỗi kết nối
    die("Could not connect to the database $db :" . $e->getMessage());
}
?>