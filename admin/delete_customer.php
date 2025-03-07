<?php
session_start();
include __DIR__ . '/../includes/db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM User_Accounts WHERE account_id = ?");
    $stmt->execute([$id]);
    header("Location: manage_customers.php?success=deleted");
    exit();
}
?>