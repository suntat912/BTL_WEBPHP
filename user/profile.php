<?php
session_start();
include '../includes/header.php';

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    header("Location: login_register.php"); // Chuyển hướng về trang đăng nhập nếu chưa đăng nhập
    exit();
}

// Kết nối đến cơ sở dữ liệu
$servername = "localhost";
$username = "root"; // Tên người dùng
$password = "Nghiacoi2212@"; // Mật khẩu
$dbname = "fashion_store"; // Tên cơ sở dữ liệu

$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Lấy thông tin người dùng
$user_id = $_SESSION['user_id']; // Lưu ý: Đảm bảo user_id được lưu trong phiên là account_id
$sql = "SELECT * FROM User_Accounts WHERE account_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$user = null;
if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo "Không tìm thấy thông tin người dùng.";
}

// Cập nhật thông tin người dùng
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $gender = $_POST['gender'];
    $date_of_birth = $_POST['date_of_birth'];

    $update_sql = "UPDATE User_Accounts SET full_name = ?, email = ?, phone = ?, gender = ?, date_of_birth = ? WHERE account_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sssssi", $full_name, $email, $phone, $gender, $date_of_birth, $user_id);
    
    if ($update_stmt->execute()) {
        echo "<script>alert('Thông tin đã được cập nhật!');</script>";
        // Cập nhật thông tin mới từ cơ sở dữ liệu
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
    } else {
        echo "Cập nhật thông tin thất bại.";
    }

    $update_stmt->close();
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông Tin Người Dùng</title>
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
            padding: 10px 20px;
            text-align: center;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
        }
        .info, .form-group {
            margin: 10px 0;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input, .form-group select {
            width: calc(100% - 20px);
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }
        button {
            background: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            display: block; /* Đặt kiểu hiển thị là block */
            margin: 10px auto; /* Căn giữa */
            padding: 10px 15px;
            cursor: pointer;
            font-size: 16px;
            width: auto;
            margin-top: 10px;
        }

        button:hover {
            background: #218838;
        }
        footer {
            text-align: center;
            padding: 20px;
            background: #333;
            color: white;
            position: relative;
            bottom: 0;
            width: 100%;
        }
        #update-form {
            display: none; /* Ẩn form chỉnh sửa mặc định */
            margin-top: 20px;
            padding: 20px;
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
    </style>
    <script>
        function toggleUpdateForm() {
            const form = document.getElementById('update-form');
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }
    </script>
</head>
<body>

<header>
    <h1>Thông Tin Người Dùng</h1>
</header>

<div class="container">
    <?php if ($user): ?>
        <h2><?php echo htmlspecialchars($user['full_name']); ?></h2>
        <div class="info"><strong>Tên đăng nhập:</strong> <?php echo htmlspecialchars($user['username']); ?></div>
        <div class="info"><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></div>
        <div class="info"><strong>Số điện thoại:</strong> <?php echo htmlspecialchars($user['phone']); ?></div>
        <div class="info"><strong>Giới tính:</strong> <?php echo htmlspecialchars($user['gender']); ?></div>
        <div class="info"><strong>Ngày sinh:</strong> <?php echo htmlspecialchars($user['date_of_birth']); ?></div>

        <button onclick="toggleUpdateForm()">Cập Nhật Thông Tin</button>

        <div id="update-form">
            <h3>Chỉnh Sửa Thông Tin</h3>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="full_name">Họ và tên:</label>
                    <input type="text" name="full_name" id="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="phone">Số điện thoại:</label>
                    <input type="text" name="phone" id="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="gender">Giới tính:</label>
                    <select name="gender" id="gender" required>
                        <option value="Nam" <?php echo ($user['gender'] == 'Nam') ? 'selected' : ''; ?>>Nam</option>
                        <option value="Nữ" <?php echo ($user['gender'] == 'Nữ') ? 'selected' : ''; ?>>Nữ</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="date_of_birth">Ngày sinh:</label>
                    <input type="date" name="date_of_birth" id="date_of_birth" value="<?php echo htmlspecialchars($user['date_of_birth']); ?>" required>
                </div>
                <button type="submit" >Lưu Thay Đổi</button>
            </form>
        </div>
    <?php else: ?>
        <p>Không có thông tin để hiển thị.</p>
    <?php endif; ?>
</div>

<footer>
    <?php include '../includes/footer.php'; ?>
</footer>

</body>
</html>