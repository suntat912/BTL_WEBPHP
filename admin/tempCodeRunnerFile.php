<?php
session_start();
include __DIR__ . '/../includes/db.php';

// Lấy danh sách liên hệ và thông tin người dùng
$stmt = $pdo->query("SELECT c.id, c.message, c.created_at, u.full_name, u.email 
                     FROM Contact c 
                     LEFT JOIN User_Accounts u ON c.user_id = u.account_id");
$contacts = $stmt->fetchAll();

// Xử lý phản hồi từ người dùng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_id'])) {
    $contact_id = $_POST['contact_id'];
    $feedback_message = trim($_POST['feedback_message']);

    if (!empty($feedback_message)) {
        // Giả sử bạn có bảng Feedback để lưu phản hồi
        $stmt = $pdo->prepare("INSERT INTO Feedback (contact_id, message) VALUES (?, ?)");
        $stmt->execute([$contact_id, $feedback_message]);
        $_SESSION['success'] = "Phản hồi của bạn đã được gửi thành công!";
    } else {
        $_SESSION['error'] = "Vui lòng nhập nội dung phản hồi!";
    }

    header("Location: manage_contacts.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Liên Hệ</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        h2 {
            text-align: center;
            color: #28a745;
            font-size: 36px;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        th, td {
            padding: 15px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #28a745;
            color: white;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .button-container {
            text-align: center;
            margin-top: 30px;
        }

        .button-container a {
            text-decoration: none;
            color: #28a745;
            font-size: 18px;
            display: inline-flex;
            align-items: center;
            font-weight: bold;
            margin: 10px;
            transition: color 0.3s;
        }

        .button-container a:hover {
            color: #218838;
        }

        .back-btn {
            color: #007bff;
        }

        .back-btn:hover {
            color: #0056b3;
        }

        .delete-btn {
            color: #d9534f;
            font-size: 16px;
        }

        .delete-btn:hover {
            color: #c9302c;
            text-decoration: underline;
        }

        /* Thông báo thành công hoặc lỗi */
        .success, .error {
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            text-align: center;
            font-size: 16px;
        }

        .success {
            background-color: #28a745;
            color: white;
        }

        .error {
            background-color: #dc3545;
            color: white;
        }

        /* Form phản hồi */
        .feedback-form {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-top: 30px;
        }

        .feedback-form textarea {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            resize: vertical;
        }

        .feedback-form button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }

        .feedback-form button:hover {
            background-color: #218838;
        }

        /* Responsive */
        @media screen and (max-width: 768px) {
            table, .form-section {
                width: 100%;
            }

            .form-section input, .form-section textarea {
                font-size: 14px;
            }
        }
    </style>
    <script>
        // Xác nhận trước khi xóa
        function confirmDelete(id) {
            if (confirm("Bạn có chắc chắn muốn xóa liên hệ này?")) {
                window.location.href = "delete_contact.php?id=" + id;
            }
        }

        // Hiện form phản hồi cho liên hệ đã chọn
        function showFeedbackForm(contactId, message) {
            document.getElementById('contact_id').value = contactId;
            document.getElementById('feedback_message').value = message;
            document.getElementById('feedback-modal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('feedback-modal').style.display = 'none';
        }
    </script>
</head>
<body>
    <h2>Quản Lý Liên Hệ</h2>

    <!-- Hiển thị thông báo thành công hoặc lỗi -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <!-- Danh sách liên hệ -->
    <h3>Danh Sách Liên Hệ</h3>
    <table>
        <tr>
            <th>Tên Người Gửi</th>
            <th>Email</th>
            <th>Nội Dung</th>
            <th>Ngày</th>
            <th>Thao tác</th>
        </tr>
        <?php foreach ($contacts as $contact): ?>
            <tr>
                <td><?php echo isset($contact['full_name']) && $contact['full_name'] !== null ? htmlspecialchars($contact['full_name']) : 'N/A'; ?></td>
                <td><?php echo isset($contact['email']) && $contact['email'] !== null ? htmlspecialchars($contact['email']) : 'N/A'; ?></td>
                <td><?php echo isset($contact['message']) && $contact['message'] !== null ? htmlspecialchars($contact['message']) : 'N/A'; ?></td>
                <td><?php echo isset($contact['created_at']) && $contact['created_at'] !== null ? htmlspecialchars($contact['created_at']) : 'N/A'; ?></td>
                <td>
                    <a href="javascript:void(0);" onclick="showFeedbackForm(<?php echo $contact['id']; ?>, '<?php echo htmlspecialchars($contact['message']); ?>')" class="button">
                        <i class="fas fa-reply"></i> Phản hồi
                    </a>
                    <a href="javascript:void(0);" onclick="confirmDelete(<?php echo $contact['id']; ?>)" class="delete-btn">
                        <i class="fas fa-trash-alt"></i> Xóa
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <!-- Form phản hồi -->
    <div id="feedback-modal" class="feedback-form" style="display:none;">
        <h3>Gửi Phản Hồi</h3>
        <form action="" method="POST">
            <input type="hidden" id="contact_id" name="contact_id">
            <textarea id="feedback_message" name="feedback_message" rows="4" placeholder="Nhập phản hồi của bạn..." required></textarea>
            <button type="submit">Gửi Phản Hồi</button>
            <button type="button" onclick="closeModal()">Đóng</button>
        </form>
    </div>

    <!-- Nút điều hướng -->
    <div class="button-container">
        <a href="index_ad.php" class="back-btn">
            <i class="fas fa-arrow-left"></i> Trở về trang chính
        </a>
        <a href="manage_products.php">
            <i class="fas fa-cogs"></i> Quản Lý Sản Phẩm
        </a>
        <a href="manage_orders.php">
            <i class="fas fa-box"></i> Quản Lý Đơn Hàng
        </a>
        <a href="manage_customers.php">
            <i class="fas fa-users"></i> Quản Lý Khách Hàng
        </a>
        <a href="manage_categories.php">
            <i class="fas fa-th-list"></i> Quản Lý Danh Mục
        </a>
        <a href="manage_brands.php">
            <i class="fas fa-clipboard-list"></i> Quản Lý Thương Hiệu
        </a>
    </div>
</body>
</html>