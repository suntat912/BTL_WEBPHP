<?php
session_start();
include __DIR__ . '/../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    $email = trim($_POST['email']);
    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];

    $stmt = $pdo->prepare("INSERT INTO User_Accounts (username, password_hash, email, full_name, phone, gender, date_of_birth) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$username, $password, $email, $full_name, $phone, $gender, $dob]);
    header("Location: manage_customers.php?success=added");
    exit();
}
?>