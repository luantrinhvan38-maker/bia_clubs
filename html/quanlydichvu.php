<?php
require_once 'header.php';
require_once __DIR__ . '/../backend/config/database.php';
require_once __DIR__ . '/../backend/controllers/Services.php';

$servicesController = new ServicesController($pdo);
$services = $servicesController->getAllServices();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Dịch Vụ</title>
    <link rel="stylesheet" href="../css/quanlydichvu.css">
</head>
<body>
    <!-- <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="quanlyban_phienchoi.php">Quản lý Bàn & Phiên Chơi</a>
        <a href="quanlydichvu.php">Quản lý Dịch Vụ</a>
        <a href="thanhtoan.php">Thanh Toán</a>
        <a href="quanlyhoadon.php">Quản lý Hóa Đơn</a>
        <a href="baocao.php">Báo Cáo</a>
    </nav> -->
    <div class="container">
        <h1>Quản lý Dịch Vụ</h1>
        <!-- Form thêm dịch vụ -->
        <form id="createServiceForm" onsubmit="createService(event)">
            <input type="text" name="ServiceName" placeholder="Tên dịch vụ" required>
            <input type="number" name="Price_Service" placeholder="Giá" required>
            <select name="Category">
                <option value="Drink">Đồ uống</option>
                <option value="Food">Thức ăn</option>
                <option value="Snack">Đồ ăn vặt</option>
            </select>
            <input type="number" name="Numbers" placeholder="Số lượng">
            <button type="submit">Thêm dịch vụ</button>
        </form>

        <h2>Danh sách dịch vụ</h2>
        <table>
            <tr>
                <th>Tên Dịch Vụ</th>
                <th>Giá</th>
                <th>Hành Động</th>
            </tr>
            <?php foreach ($services as $service): ?>
                <tr>
                    <td><?php echo htmlspecialchars($service['ServiceName']); ?></td>
                    <td><?php echo htmlspecialchars($service['Price_Service']); ?></td>
                    <td>
                        <button onclick="deleteService(<?php echo $service['ServiceID']; ?>)">Xóa</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <?php require_once 'footer.php'; ?>
    <script src="../js/quanlydichvu.js"></script>
</body>
</html>