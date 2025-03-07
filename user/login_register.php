<?php
session_start();

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

$error_message = ''; // Biến để lưu thông báo lỗi

// Tạo tài khoản admin nếu chưa tồn tại
$admin_username = 'admin';
$admin_password = password_hash('admin123@', PASSWORD_DEFAULT); // Mã hóa mật khẩu admin
$admin_email = 'admin@example.com'; // Email của admin
$admin_full_name = 'Administrator';
$admin_role = 'admin';

// Kiểm tra xem tài khoản admin đã tồn tại hay chưa
$sql_check_admin = "SELECT * FROM User_Accounts WHERE username='$admin_username'";
$result_check = $conn->query($sql_check_admin);

if ($result_check->num_rows == 0) {
    $sql_create_admin = "INSERT INTO User_Accounts (username, password_hash, email, full_name, role) 
                         VALUES ('$admin_username', '$admin_password', '$admin_email', '$admin_full_name', '$admin_role')";
    $conn->query($sql_create_admin);
}

// Xử lý đăng ký
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Mã hóa mật khẩu
    $email = $_POST['email'];
    $full_name = $_POST['full_name'];
    $phone = $_POST['phone'];
    $gender = $_POST['gender'];
    $date_of_birth = $_POST['date_of_birth'];
    $role = 'user'; // Mặc định role là user

    // Chèn thông tin người dùng vào bảng User_Accounts
    $sql = "INSERT INTO User_Accounts (username, password_hash, email, full_name, phone, gender, date_of_birth, role) 
            VALUES ('$username', '$password', '$email', '$full_name', '$phone', '$gender', '$date_of_birth', '$role')";
    
    if ($conn->query($sql) === TRUE) {
        echo "Đăng ký thành công!";
    } else {
        echo "Lỗi: " . $sql . "<br>" . $conn->error;
    }
}

// Xử lý đăng nhập
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Kiểm tra thông tin đăng nhập
    $sql = "SELECT * FROM User_Accounts WHERE username='$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Kiểm tra mật khẩu
        if (password_verify($password, $row['password_hash'])) {
            // Đăng nhập thành công
            $_SESSION['username'] = $username; // Lưu tên đăng nhập vào session
            $_SESSION['user_id'] = $row['account_id']; // Lưu ID người dùng vào session
            
            // Chuyển hướng đến trang tương ứng
            if ($username === 'admin') {
                header("Location: /fashion_store/admin/index_ad.php"); // Chuyển đến trang admin
            } else {
                header("Location: /fashion_store/user/index.php"); // Chuyển đến trang người dùng
            }
            exit(); // Dừng thực thi script
        } else {
            $error_message = "Tên đăng nhập hoặc mật khẩu không đúng.";
        }
    } else {
        $error_message = "Tên đăng nhập hoặc mật khẩu không đúng.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập / Đăng Ký - Cửa Hàng Thời Trang</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 400px; /* Độ rộng của form */
            max-width: 90%; /* Để đảm bảo form không vượt quá màn hình trên thiết bị nhỏ */
        }
        h2 {
            text-align: center;
            color: #333;
        }
        input[type="text"], input[type="password"], input[type="email"], input[type="tel"], input[type="date"], select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }
        button {
            width: 100%;
            padding: 10px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background: #0056b3;
        }
        .error {
            color: red;
            margin-top: 10px;
            text-align: center; /* Căn giữa thông báo lỗi */
        }
        .toggle-link {
            text-align: center; /* Căn giữa nội dung */
            margin-top: 10px; /* Khoảng cách phía trên */
            cursor: pointer; /* Thay đổi con trỏ khi hover */
        }
        .toggle-link a, .toggle-link span {
            color: #007bff; /* Màu liên kết */
            text-decoration: none; /* Bỏ gạch chân mặc định */
        }
        .toggle-link a:hover, .toggle-link span:hover {
            text-decoration: underline; /* Gạch chân khi hover */
        }
        .remember-me {
            display: flex;
            align-items: center;
            margin: 10px 0;
        }
        .remember-me input {
            margin-right: 5px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2 id="formTitle">Đăng Nhập</h2>
    <form id="authForm" method="POST" action="">
        <input type="text" name="username" placeholder="Tên đăng nhập" required>
        <input type="password" name="password" id="password" placeholder="Mật khẩu" required>
        <input type="checkbox" id="showPassword" onclick="togglePasswordVisibility()">
        <label for="showPassword">Hiện mật khẩu</label>
        <div class="remember-me">
            <input type="checkbox" name="remember_me" id="remember_me">
            <label for="remember_me">Nhớ mật khẩu</label>
        </div>
        <button type="submit" name="login">Đăng Nhập</button>
        <div class="toggle-link">
            <a href="forgot_password.php">Quên mật khẩu?</a>
        </div>
        <div class="toggle-link" onclick="toggleForm()">
            <span>Chưa có tài khoản? Đăng ký</span>
        </div>
        <?php if ($error_message): ?>
            <div class="error"><?php echo $error_message; ?></div>
        <?php endif; ?>
    </form>
</div>

<script>
    function toggleForm() {
        const formTitle = document.getElementById('formTitle');
        const authForm = document.getElementById('authForm');

        if (formTitle.innerText === "Đăng Nhập") {
            formTitle.innerText = "Đăng Ký";
            authForm.innerHTML = `
                <input type="text" name="username" placeholder="Tên đăng nhập" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="text" name="full_name" placeholder="Họ và tên" required>
                <input type="tel" name="phone" placeholder="Số điện thoại (10 chữ số)" required>
                <select name="gender" required>
                    <option value="">Chọn giới tính</option>
                    <option value="male">Nam</option>
                    <option value="female">Nữ</option>
                    <option value="other">Khác</option>
                </select>
                <input type="date" name="date_of_birth" required>
                <input type="password" name="password" id="registerPassword" placeholder="Mật khẩu" required>
                <input type="checkbox" id="showRegisterPassword" onclick="toggleRegisterPasswordVisibility()">
                <label for="showRegisterPassword">Hiện mật khẩu</label>
                <input type="password" name="confirm_password" placeholder="Nhập lại mật khẩu" required>
                <button type="submit" name="register">Đăng Ký</button>
                <div class="toggle-link" onclick="toggleForm()">
                    <span>Đã có tài khoản? Đăng nhập</span>
                </div>
            `;
        } else {
            formTitle.innerText = "Đăng Nhập";
            authForm.innerHTML = `
                <input type="text" name="username" placeholder="Tên đăng nhập" required>
                <input type="password" name="password" id="password" placeholder="Mật khẩu" required>
                <input type="checkbox" id="showPassword" onclick="togglePasswordVisibility()">
                <label for="showPassword">Hiện mật khẩu</label>
                <div class="remember-me">
                    <input type="checkbox" name="remember_me" id="remember_me">
                    <label for="remember_me">Nhớ mật khẩu</label>
                </div>
                <button type="submit" name="login">Đăng Nhập</button>
                <div class="toggle-link">
                    <a href="forgot_password.php">Quên mật khẩu?</a>
                </div>
                <div class="toggle-link" onclick="toggleForm()">
                    <span>Chưa có tài khoản? Đăng ký</span>
                </div>
            `;
        }
    }

    function togglePasswordVisibility() {
        const passwordField = document.getElementById('password');
        const showPasswordCheckbox = document.getElementById('showPassword');
        passwordField.type = showPasswordCheckbox.checked ? "text" : "password";
    }

    function toggleRegisterPasswordVisibility() {
        const registerPasswordField = document.getElementById('registerPassword');
        const showRegisterPasswordCheckbox = document.getElementById('showRegisterPassword');
        registerPasswordField.type = showRegisterPasswordCheckbox.checked ? "text" : "password";
    }
</script>

</body>
</html>