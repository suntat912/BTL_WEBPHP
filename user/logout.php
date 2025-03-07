<?php
session_start();

// Xóa tất cả các biến phiên
$_SESSION = [];

// Hủy phiên
session_destroy();

// Chuyển hướng về trang đăng nhập
header("Location: login_register.php");
exit();
?>