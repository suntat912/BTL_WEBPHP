<?php
session_start();
include __DIR__ . '/../includes/db.php';

// Kiểm tra ID hợp lệ
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: manage_brands.php?error=1");
    exit;
}

$brand_id = intval($_GET['id']);

try {
    // Kiểm tra xem thương hiệu có tồn tại không
    $stmt = $pdo->prepare("SELECT * FROM Brands WHERE brand_id = ?");
    $stmt->execute([$brand_id]);
    $brand = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$brand) {
        header("Location: manage_brands.php?error=1");
        exit;
    }

    // Xóa thương hiệu
    $stmt = $pdo->prepare("DELETE FROM Brands WHERE brand_id = ?");
    $stmt->execute([$brand_id]);

    // Chuyển hướng với thông báo thành công
    header("Location: manage_brands.php?success=1");
    exit;
} catch (PDOException $e) {
    header("Location: manage_brands.php?error=1");
    exit;
}
?>
