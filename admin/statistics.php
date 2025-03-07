<?php
session_start();
include __DIR__ . '/../includes/db.php';

// Thống kê tổng số sản phẩm
$stmt = $pdo->query("SELECT COUNT(*) as total_products FROM Products");
$total_products = $stmt->fetchColumn();

// Thống kê tổng số đơn hàng
$stmt = $pdo->query("SELECT COUNT(*) as total_orders, SUM(total_amount) as total_revenue FROM Orders");
$order_data = $stmt->fetch();
$total_orders = $order_data['total_orders'];
$total_revenue = $order_data['total_revenue'];

// Thống kê tổng số khách hàng
$stmt = $pdo->query("SELECT COUNT(*) as total_customers FROM User_Accounts");
$total_customers = $stmt->fetchColumn();

// Thống kê tổng số liên hệ
$stmt = $pdo->query("SELECT COUNT(*) as total_contacts FROM Contact");
$total_contacts = $stmt->fetchColumn();

// Thống kê tổng số danh mục
$stmt = $pdo->query("SELECT COUNT(*) as total_categories FROM Categories");
$total_categories = $stmt->fetchColumn();

// Thống kê tổng số thương hiệu
$stmt = $pdo->query("SELECT COUNT(*) as total_brands FROM Brands");
$total_brands = $stmt->fetchColumn();

// Truy vấn doanh thu theo ngày
$stmt = $pdo->query("
    SELECT DATE(order_date) as order_date, SUM(total_amount) as daily_revenue 
    FROM Orders 
    GROUP BY DATE(order_date)
    ORDER BY DATE(order_date)
");
$revenue_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Chuyển đổi dữ liệu cho biểu đồ
$dates = array_column($revenue_data, 'order_date');
$revenues = array_column($revenue_data, 'daily_revenue');

// Truy vấn tất cả các đơn hàng và chi tiết sản phẩm của chúng
$stmt = $pdo->query("SELECT 
                        o.order_id, 
                        o.user_id, 
                        o.total_amount, 
                        o.shipping_address, 
                        o.order_date, 
                        o.status, 
                        od.product_id, 
                        od.quantity, 
                        od.price, 
                        p.product_name 
                    FROM Orders o 
                    JOIN Order_Details od ON o.order_id = od.order_id
                    JOIN Products p ON od.product_id = p.product_id");

$orders = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: []; // Khởi tạo $orders là mảng rỗng nếu không có dữ liệu
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thống Kê Tổng Quan</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        .statistic-table {
            width: 100%;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin: 20px 0;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #28a745;
            color: white;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .button-container {
            margin-top: 30px;
            text-align: center;
        }
        a {
            text-decoration: none;
            color: #28a745;
            margin: 0 15px;
            font-weight: bold;
        }
        a:hover {
            text-decoration: underline;
        }
        .button-container a i {
            margin-right: 8px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .statistic-table {
                margin: 10px 0;
            }
            table, .button-container {
                width: 100%;
            }
            .button-container a {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>

<h2>Thống Kê Tổng Quan</h2>

<!-- Biểu đồ doanh thu -->
<div class="statistic-table">
    <h3>Biểu Đồ Doanh Thu</h3>
    <canvas id="revenueChart" width="400" height="200"></canvas>
</div>

<script>
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(ctx, {
        type: 'line', // Kiểu biểu đồ
        data: {
            labels: <?php echo json_encode($dates); ?>, // Ngày đặt hàng
            datasets: [{
                label: 'Doanh Thu',
                data: <?php echo json_encode($revenues); ?>, // Doanh thu
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

<!-- Thống kê tổng quan -->
<div class="statistic-table">
    <h3>Sản Phẩm</h3>
    <table>
        <tr>
            <th>Tổng số sản phẩm</th>
            <td><strong><?php echo htmlspecialchars($total_products); ?></strong></td>
        </tr>
    </table>
</div>

<div class="statistic-table">
    <h3>Đơn Hàng</h3>
    <table>
        <tr>
            <th>Tổng số đơn hàng</th>
            <td><strong><?php echo htmlspecialchars($total_orders); ?></strong></td>
        </tr>
        <tr>
            <th>Tổng doanh thu</th>
            <td><strong><?php echo htmlspecialchars(number_format($total_revenue, 2)); ?> VNĐ</strong></td>
        </tr>
    </table>
</div>

<div class="statistic-table">
    <h3>Khách Hàng</h3>
    <table>
        <tr>
            <th>Tổng số khách hàng</th>
            <td><strong><?php echo htmlspecialchars($total_customers); ?></strong></td>
        </tr>
    </table>
</div>

<div class="statistic-table">
    <h3>Liên Hệ</h3>
    <table>
        <tr>
            <th>Tổng số liên hệ</th>
            <td><strong><?php echo htmlspecialchars($total_contacts); ?></strong></td>
        </tr>
    </table>
</div>

<div class="statistic-table">
    <h3>Danh Mục</h3>
    <table>
        <tr>
            <th>Tổng số danh mục</th>
            <td><strong><?php echo htmlspecialchars($total_categories); ?></strong></td>
        </tr>
    </table>
</div>

<div class="statistic-table">
    <h3>Thương Hiệu</h3>
    <table>
        <tr>
            <th>Tổng số thương hiệu</th>
            <td><strong><?php echo htmlspecialchars($total_brands); ?></strong></td>
        </tr>
    </table>
</div>

<!-- Chi Tiết Đơn Hàng -->
<?php
$current_order_id = null;
if (!empty($orders)) {
    foreach ($orders as $order) {
        if ($order['order_id'] !== $current_order_id) {
            if ($current_order_id !== null) {
                echo "</table><br>"; // Đóng bảng của đơn hàng cũ
            }
            $current_order_id = $order['order_id'];
            
            echo "<div class='statistic-table'>";
            echo "<h3>Đơn Hàng #".$order['order_id']."</h3>";
            echo "<p>Ngày đặt hàng: ".$order['order_date']."</p>";
            echo "<p>Tổng số tiền: ".number_format($order['total_amount'], 2)." VNĐ</p>";
            echo "<p>Địa chỉ giao hàng: ".$order['shipping_address']."</p>";
            echo "<p>Trạng thái: ".$order['status']."</p>";
            
            echo "<h4>Chi Tiết Sản Phẩm:</h4>";
            echo "<table>
                    <tr>
                        <th>Sản Phẩm</th>
                        <th>Số Lượng</th>
                        <th>Đơn Giá</th>
                        <th>Tổng Tiền</th>
                    </tr>";
        }

        // Hiển thị chi tiết từng sản phẩm trong đơn hàng
        $product_name = $order['product_name'];
        $quantity = $order['quantity'];
        $price = $order['price'];
        $total_price = $quantity * $price;
        
        echo "<tr>
                <td>".$product_name."</td>
                <td>".$quantity."</td>
                <td>".number_format($price, 2)." VNĐ</td>
                <td>".number_format($total_price, 2)." VNĐ</td>
              </tr>";
    }
} else {
    echo "<p>Không có đơn hàng nào!</p>";
}

echo "</table></div>"; // Đóng bảng của đơn hàng cuối cùng
?>

<div class="button-container">
    <a href="index_ad.php"><i class="fas fa-home"></i>Trở về trang chính</a>
    <a href="manage_products.php"><i class="fas fa-cogs"></i>Quản Lý Sản Phẩm</a>
    <a href="manage_orders.php"><i class="fas fa-box"></i>Quản Lý Đơn Hàng</a>
    <a href="manage_customers.php"><i class="fas fa-users"></i>Quản Lý Khách Hàng</a>
    <a href="manage_contacts.php"><i class="fas fa-envelope"></i>Quản Lý Liên Hệ</a>
    <a href="manage_categories.php"><i class="fas fa-th-list"></i>Quản Lý Danh Mục</a>
    <a href="manage_brands.php"><i class="fas fa-tag"></i>Quản Lý Thương Hiệu</a>
</div>

</body>
</html>