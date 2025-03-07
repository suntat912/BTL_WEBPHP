<?php
session_start();
include __DIR__ . '/../includes/db.php';

// Kiểm tra CSRF token
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
    // Thêm danh mục
    if (isset($_POST['add_category'])) {
        $category_name = trim($_POST['category_name']);
        $description = isset($_POST['description']) ? trim($_POST['description']) : '';
        $product_ids = isset($_POST['product_ids']) ? $_POST['product_ids'] : [];

        if (!empty($category_name)) {
            $stmt = $pdo->prepare("INSERT INTO categories (category_name, description) VALUES (?, ?)");
            $stmt->execute([$category_name, $description]);
            $category_id = $pdo->lastInsertId();

            // Thêm sản phẩm vào danh mục
            foreach ($product_ids as $product_id) {
                $stmt = $pdo->prepare("INSERT INTO Product_Categories (product_id, category_id) VALUES (?, ?)");
                $stmt->execute([$product_id, $category_id]);
            }

            header("Location: manage_categories.php?success=added");
            exit;
        }
    }

    // Cập nhật danh mục
    if (isset($_POST['update_category']) && isset($_GET['edit_id'])) {
        $edit_id = $_GET['edit_id'];
        $category_name = trim($_POST['category_name']);
        $description = isset($_POST['description']) ? trim($_POST['description']) : '';
        $product_ids = isset($_POST['product_ids']) ? $_POST['product_ids'] : [];

        if (!empty($category_name)) {
            $stmt = $pdo->prepare("UPDATE categories SET category_name = ?, description = ? WHERE category_id = ?");
            $stmt->execute([$category_name, $description, $edit_id]);

            // Cập nhật sản phẩm thuộc danh mục
            $stmt = $pdo->prepare("DELETE FROM Product_Categories WHERE category_id = ?");
            $stmt->execute([$edit_id]);

            foreach ($product_ids as $product_id) {
                $stmt = $pdo->prepare("INSERT INTO Product_Categories (product_id, category_id) VALUES (?, ?)");
                $stmt->execute([$product_id, $edit_id]);
            }

            header("Location: manage_categories.php?success=updated");
            exit;
        }
    }
}

// Xóa danh mục
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $stmt = $pdo->prepare("DELETE FROM categories WHERE category_id = ?");
    $stmt->execute([$delete_id]);
    header("Location: manage_categories.php?success=deleted");
    exit;
}

// Lấy danh sách danh mục
$stmt = $pdo->prepare("SELECT * FROM categories");
$stmt->execute();
$categories = $stmt->fetchAll();

// Lấy danh sách sản phẩm để hiển thị
$stmt = $pdo->prepare("SELECT * FROM Products");
$stmt->execute();
$products = $stmt->fetchAll();

// Kiểm tra và lấy thông tin danh mục khi sửa
if (isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE category_id = ?");
    $stmt->execute([$edit_id]);
    $category = $stmt->fetch();

    // Kiểm tra xem danh mục có tồn tại không
    if (!$category) {
        header("Location: manage_categories.php?error=not_found");
        exit;
    }

    // Lấy danh sách sản phẩm đã được thêm vào danh mục
    $stmt = $pdo->prepare("SELECT product_id FROM Product_Categories WHERE category_id = ?");
    $stmt->execute([$edit_id]);
    $assigned_products = $stmt->fetchAll(PDO::FETCH_COLUMN);
}

// Tạo CSRF token nếu chưa có
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản Lý Danh Mục</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: 50px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h1, h2 {
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }
        .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
        }
        .message.success {
            background-color: #d4edda;
            color: #155724;
        }
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
        }
        label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
        }
        input, textarea, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        input:focus, textarea:focus, select:focus {
            border-color: #007bff;
            outline: none;
        }
        button {
            width: auto;
            text-align: center;
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            cursor: pointer;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            transition: background-color 0.3s, transform 0.2s;
        }
        button:hover {
            background-color: #218838;
            transform: scale(1.05);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }
        table th {
            background-color: #f2f2f2;
        }
        .action-links a {
            margin: 0 5px;
            color: #007bff;
            text-decoration: none;
        }
        .action-links a:hover {
            text-decoration: underline;
        }
        .action-links .delete {
            color: #dc3545;
        }
        .action-links .delete:hover {
            color: #c82333;
        }
        .action-links .edit {
            color: #17a2b8;
        }
        .action-links .edit:hover {
            color: #138496;
        }
        .button-container {
            margin-top: 30px;
            text-align: center;
        }
        .button-container a {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 0 10px;
            font-size: 16px;
            transition: background-color 0.3s, transform 0.2s;
        }
        .button-container a:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }
        .button-container a i {
            margin-right: 5px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Quản Lý Danh Mục</h1>

    <?php if (isset($_GET['error']) && $_GET['error'] === 'not_found'): ?>
        <div class="message error">Danh mục không tồn tại hoặc đã bị xóa.</div>
    <?php endif; ?>

    <?php if (isset($_GET['success'])): ?>
        <div class="message success">
            <?php 
                if ($_GET['success'] === 'added') echo "Danh mục đã được thêm thành công.";
                if ($_GET['success'] === 'updated') echo "Danh mục đã được cập nhật thành công.";
                if ($_GET['success'] === 'deleted') echo "Danh mục đã được xóa thành công.";
            ?>
        </div>
    <?php endif; ?>

    <h2>Thêm Danh Mục Mới</h2>
    <form method="POST" action="">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <label for="category_name">Tên danh mục:</label>
        <input type="text" name="category_name" id="category_name" placeholder="Tên danh mục" required>
        <label for="description">Mô tả:</label>
        <textarea name="description" id="description" placeholder="Mô tả"></textarea>
        
        <label for="products">Chọn sản phẩm thuộc danh mục:</label>
        <select name="product_ids[]" id="products" multiple>
            <?php foreach ($products as $product): ?>
                <option value="<?php echo $product['product_id']; ?>">
                    <?php echo htmlspecialchars($product['product_name']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit" name="add_category"><i class="fas fa-plus"></i> Thêm Danh Mục</button>
    </form>

    <?php if (isset($_GET['edit_id']) && isset($category)): ?>
        <h2>Sửa Danh Mục</h2>
        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <label for="category_name">Tên danh mục:</label>
            <input type="text" name="category_name" id="category_name" value="<?php echo htmlspecialchars($category['category_name']); ?>" required>
            <label for="description">Mô tả:</label>
            <textarea name="description" id="description"><?php echo htmlspecialchars($category['description']); ?></textarea>
            
            <label for="products">Chọn sản phẩm thuộc danh mục:</label>
            <select name="product_ids[]" id="products" multiple>
                <?php foreach ($products as $product): ?>
                    <option value="<?php echo $product['product_id']; ?>" 
                        <?php echo in_array($product['product_id'], $assigned_products) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($product['product_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit" name="update_category">Cập Nhật Danh Mục</button>
        </form>
    <?php endif; ?>

    <h2>Danh Sách Danh Mục</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Tên Danh Mục</th>
            <th>Mô Tả</th>
            <th>Ngày Tạo</th>
            <th>Hành Động</th>
        </tr>
        <?php if (!empty($categories)): ?>
            <?php foreach ($categories as $category): ?>
                <tr>
                    <td><?php echo htmlspecialchars($category['category_id']); ?></td>
                    <td><?php echo htmlspecialchars($category['category_name']); ?></td>
                    <td><?php echo htmlspecialchars($category['description']); ?></td>
                    <td><?php echo htmlspecialchars($category['created_at']); ?></td>
                    <td class="action-links">
                        <a href="?edit_id=<?php echo $category['category_id']; ?>" class="edit" title="Sửa"><i class="fas fa-edit"></i> Sửa</a>
                        <a href="?delete_id=<?php echo $category['category_id']; ?>" class="delete" onclick="return confirm('Bạn có chắc chắn muốn xóa danh mục này không?');" title="Xóa"><i class="fas fa-trash-alt"></i> Xóa</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5">Không có danh mục nào.</td>
            </tr>
        <?php endif; ?>
    </table>
</div>

<div class="button-container">
    <a href="index_ad.php"><i class="fas fa-home"></i> Trở về trang chính</a>
    <a href="manage_products.php"><i class="fas fa-cogs"></i> Quản Lý Sản Phẩm</a>
    <a href="manage_orders.php"><i class="fas fa-box"></i> Quản Lý Đơn Hàng</a>
    <a href="manage_customers.php"><i class="fas fa-users"></i> Quản Lý Khách Hàng</a>
    <a href="manage_contacts.php"><i class="fas fa-envelope"></i> Quản Lý Liên Hệ</a>
    <a href="manage_categories.php"><i class="fas fa-th-list"></i> Quản Lý Danh Mục</a>
    <a href="manage_brands.php"><i class="fas fa-tag"></i> Quản Lý Thương Hiệu</a>
</div>
</body>
</html>