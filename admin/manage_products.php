<?php
ob_start(); // Bắt đầu buffer output
session_start();
include __DIR__ . '/../includes/db.php';

// Bật hiển thị lỗi
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Khởi tạo biến thông báo
$success = "";
$error = "";

// Hàm sanitize để bảo vệ dữ liệu đầu vào
function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Đường dẫn đến thư mục uploads
$upload_dir = __DIR__ . '/../uploads/';

// Kiểm tra và tạo thư mục uploads nếu chưa tồn tại
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true); // Tạo thư mục với quyền ghi
}

// Xử lý form thêm hoặc cập nhật sản phẩm
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_product'])) {
        // Thêm sản phẩm
        $product_name = sanitize($_POST['product_name']);
        $description = sanitize($_POST['description']);
        $price = $_POST['price'];
        $stock_quantity = $_POST['stock_quantity'];
        
        // Kiểm tra tính hợp lệ của dữ liệu
        if ($price <= 0) {
            $error = "Giá sản phẩm phải lớn hơn 0!";
        } elseif ($stock_quantity < 0) {
            $error = "Số lượng tồn kho không thể âm!";
        }

        // Khởi tạo biến $image_url
        $image_url = null;

        // Kiểm tra và xử lý ảnh nếu có
        if ($_FILES['image']['error'] == 0) {
            $image = $_FILES['image'];
            $image_name = uniqid('product_', true) . '.' . pathinfo($image['name'], PATHINFO_EXTENSION);
            if (move_uploaded_file($image['tmp_name'], $upload_dir . $image_name)) {
                $image_url = '/uploads/' . $image_name; // Lưu đường dẫn hình ảnh
            } else {
                $error = "Lỗi khi tải lên hình ảnh!";
            }
        } else {
            $error = "Vui lòng chọn hình ảnh!";
        }

        // Nếu không có lỗi, thực hiện thêm sản phẩm vào cơ sở dữ liệu
        if (empty($error)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO Products (product_name, description, price, stock_quantity, image_url) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$product_name, $description, $price, $stock_quantity, $image_url]);
                $success = "Sản phẩm đã được thêm!";
            } catch (Exception $e) {
                $error = "Lỗi: " . $e->getMessage();
            }
        }
    } elseif (isset($_POST['update_product'])) {
        // Cập nhật sản phẩm
        $product_id = $_POST['product_id'];
        $product_name = sanitize($_POST['product_name']);
        $description = sanitize($_POST['description']);
        $price = $_POST['price'];
        $stock_quantity = $_POST['stock_quantity'];

        // Kiểm tra tính hợp lệ của dữ liệu
        if ($price <= 0) {
            $error = "Giá sản phẩm phải lớn hơn 0!";
        } elseif ($stock_quantity < 0) {
            $error = "Số lượng tồn kho không thể âm!";
        }

        // Khởi tạo biến $image_url
        $image_url = null;

        // Kiểm tra và xử lý ảnh nếu có
        if ($_FILES['image']['error'] == 0) {
            $image = $_FILES['image'];
            $image_name = uniqid('product_', true) . '.' . pathinfo($image['name'], PATHINFO_EXTENSION);
            if (move_uploaded_file($image['tmp_name'], $upload_dir . $image_name)) {
                $image_url = '/uploads/' . $image_name; // Lưu đường dẫn hình ảnh
            } else {
                $error = "Lỗi khi tải lên hình ảnh!";
            }
        } else {
            $image_url = $_POST['current_image']; // Giữ nguyên hình ảnh hiện tại nếu không có hình mới
        }

        // Nếu không có lỗi, thực hiện cập nhật sản phẩm vào cơ sở dữ liệu
        if (empty($error)) {
            try {
                $stmt = $pdo->prepare("UPDATE Products SET product_name = ?, description = ?, price = ?, stock_quantity = ?, image_url = ? WHERE product_id = ?");
                $stmt->execute([$product_name, $description, $price, $stock_quantity, $image_url, $product_id]);
                $success = "Sản phẩm đã được cập nhật!";
                
                // Chuyển hướng về trang danh sách sản phẩm
                header("Location: manage_products.php");
                exit(); // Dừng script sau khi chuyển hướng
            } catch (Exception $e) {
                $error = "Lỗi: " . $e->getMessage();
            }
        }
    }
}

// Xóa sản phẩm
if (isset($_GET['delete_id'])) {
    $product_id = $_GET['delete_id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM Products WHERE product_id = ?");
        $stmt->execute([$product_id]);
        $success = "Sản phẩm đã được xóa!";
    } catch (Exception $e) {
        $error = "Lỗi: " . $e->getMessage();
    }
}

// Lấy danh sách sản phẩm
$stmt = $pdo->query("SELECT * FROM Products");
$products = $stmt->fetchAll();

// Lấy thông tin sản phẩm khi sửa
$product_to_edit = null;
if (isset($_GET['edit_id'])) {
    $product_id = $_GET['edit_id'];
    $stmt = $pdo->prepare("SELECT * FROM Products WHERE product_id = ?");
    $stmt->execute([$product_id]);
    $product_to_edit = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản Lý Sản Phẩm</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
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
            border-radius: 8px;
            overflow: hidden;
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

        td img {
            width: 150px; /* Thay đổi kích thước theo ý muốn */
            height: auto;
            border-radius: 8px;
        }

        .form-section {
            background-color: #fff;
            padding: 20px;
            margin-top: 30px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            border-radius: 8px;
        }

        .form-section input, .form-section textarea {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            background-color: #f9f9f9;
        }

        .form-section button {
            background-color: #28a745;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .form-section button:hover {
            background-color: #218838;
        }

        .success, .error {
            text-align: center;
            font-size: 18px;
            margin: 20px;
            padding: 15px;
            border-radius: 8px;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
        }

        /* Footer Styles */
        .footer-container {
            margin-top: 40px;
            text-align: center;
            background-color: #f4f4f4;
            padding: 15px 0;
            border-top: 1px solid #ddd;
        }

        .footer-container a {
            text-decoration: none;
            color: #28a745;
            margin-right: 15px;
            font-weight: bold;
        }

        .footer-container a:hover {
            text-decoration: underline;
        }

        .footer-container a i {
            margin-right: 8px;
        }

        @media (max-width: 768px) {
            table, .form-section {
                width: 100%;
            }

            .form-section input, .form-section textarea {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <h2>Quản Lý Sản Phẩm</h2>

    <!-- Hiển thị thông báo thành công hoặc lỗi -->
    <?php if ($success) echo "<p class='success'>$success</p>"; ?>
    <?php if ($error) echo "<p class='error'>$error</p>"; ?>

    <!-- Danh sách sản phẩm -->
    <h3>Danh Sách Sản Phẩm</h3>
    <table>
        <tr>
            <th>Tên sản phẩm</th>
            <th>Mô tả</th>
            <th>Giá</th>
            <th>Số lượng</th>
            <th>Hình ảnh</th>
            <th>Thao tác</th>
        </tr>
        <?php foreach ($products as $product): ?>
            <tr>
                <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                <td><?php echo htmlspecialchars($product['description']); ?></td>
                <td><?php echo number_format($product['price'], 2); ?> VNĐ</td>
                <td><?php echo $product['stock_quantity']; ?></td>
                <td>
                    <?php $img_url = htmlspecialchars($product['image_url']); ?>
                    <img src="<?php echo $img_url; ?>" alt="Hình ảnh sản phẩm">
                </td>
                <td>
                    <a href="?delete_id=<?php echo $product['product_id']; ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này không?');">
                        <i class="fas fa-trash-alt"></i> Xóa
                    </a> |
                    <a href="?edit_id=<?php echo $product['product_id']; ?>">
                        <i class="fas fa-edit"></i> Sửa
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <!-- Form thêm hoặc sửa sản phẩm -->
    <h3><?php echo $product_to_edit ? 'Sửa Sản Phẩm' : 'Thêm Sản Phẩm Mới'; ?></h3>
    <form method="POST" action="" class="form-section" enctype="multipart/form-data">
        <input type="hidden" name="product_id" value="<?php echo $product_to_edit ? $product_to_edit['product_id'] : ''; ?>">
        <input type="text" name="product_name" placeholder="Tên sản phẩm" required value="<?php echo $product_to_edit ? htmlspecialchars($product_to_edit['product_name']) : ''; ?>">
        <textarea name="description" placeholder="Mô tả" required><?php echo $product_to_edit ? htmlspecialchars($product_to_edit['description']) : ''; ?></textarea>
        <input type="number" name="price" placeholder="Giá" required step="0.01" value="<?php echo $product_to_edit ? $product_to_edit['price'] : ''; ?>">
        <input type="number" name="stock_quantity" placeholder="Số lượng tồn kho" required value="<?php echo $product_to_edit ? $product_to_edit['stock_quantity'] : ''; ?>">
        <input type="file" name="image" accept="image/*" required>
        <?php if ($product_to_edit): ?>
            <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($product_to_edit['image_url']); ?>">
            <p>Hình ảnh hiện tại: <img src="<?php echo htmlspecialchars($product_to_edit['image_url']); ?>" alt="Hình ảnh sản phẩm" width="100"></p>
        <?php endif; ?>
        <button type="submit" name="<?php echo $product_to_edit ? 'update_product' : 'add_product'; ?>">
            <?php echo $product_to_edit ? 'Cập Nhật Sản Phẩm' : 'Thêm Sản Phẩm'; ?>
        </button>
    </form>

    <!-- Footer -->
    <div class="footer-container">
        <a href="index_ad.php"><i class="fas fa-home"></i>Trở về trang chính</a>
        <a href="manage_products.php"><i class="fas fa-cogs"></i>Quản Lý Sản Phẩm</a>
        <a href="manage_orders.php"><i class="fas fa-box"></i>Quản Lý Đơn Hàng</a>
        <a href="manage_customers.php"><i class="fas fa-users"></i>Quản Lý Khách Hàng</a>
        <a href="manage_contacts.php"><i class="fas fa-envelope"></i>Quản Lý Liên Hệ</a>
        <a href="manage_categories.php"><i class="fas fa-th-list"></i>Quản Lý Danh Mục</a>
        <a href="manage_brands.php"><i class="fas fa-tag"></i>Quản Lý Thương Hiệu</a>
    </div>

    <?php ob_end_flush(); // Gửi nội dung đã lưu trong buffer ?>
</body>
</html>