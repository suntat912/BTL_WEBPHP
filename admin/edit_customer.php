<?php
session_start();
include __DIR__ . '/../includes/db.php';

// Lấy thông tin khách hàng
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM User_Accounts WHERE account_id = ?");
    $stmt->execute([$id]);
    $customer = $stmt->fetch();

    if (!$customer) {
        header("Location: manage_customers.php?error=not_found");
        exit();
    }
}

// Cập nhật khách hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];

    $stmt = $pdo->prepare("UPDATE User_Accounts SET full_name = ?, email = ?, phone = ?, gender = ?, date_of_birth = ? WHERE account_id = ?");
    $stmt->execute([$full_name, $email, $phone, $gender, $dob, $id]);
    header("Location: manage_customers.php?success=updated");
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa Khách Hàng</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        h2 {
            color: #28a745;
            text-align: center;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            font-weight: bold;
            margin-bottom: 5px;
        }
        input, select {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            background-color: #f9f9f9;
            width: 100%;
        }
        button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
        }
        button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Sửa Khách Hàng</h2>
    <form action="" method="POST">
        <div class="form-group">
            <label for="full_name">Họ và Tên:</label>
            <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($customer['full_name']); ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($customer['email']); ?>" required>
        </div>
        <div class="form-group">
            <label for="phone">Số Điện Thoại:</label>
            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($customer['phone']); ?>">
        </div>
        <div class="form-group">
            <label for="gender">Giới Tính:</label>
            <select id="gender" name="gender">
                <option value="male" <?php echo $customer['gender'] === 'male' ? 'selected' : ''; ?>>Nam</option>
                <option value="female" <?php echo $customer['gender'] === 'female' ? 'selected' : ''; ?>>Nữ</option>
                <option value="other" <?php echo $customer['gender'] === 'other' ? 'selected' : ''; ?>>Khác</option>
            </select>
        </div>
        <div class="form-group">
            <label for="dob">Ngày Sinh:</label>
            <input type="date" id="dob" name="dob" value="<?php echo htmlspecialchars($customer['date_of_birth']); ?>">
        </div>
        <button type="submit"><i class="fas fa-save"></i> Cập Nhật Khách Hàng</button>
    </form>
</div>

</body>
</html>