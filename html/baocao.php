<?php
require_once 'header.php';
require_once __DIR__ . '/../backend/config/database.php';
require_once __DIR__ . '/../backend/controllers/Reports.php';

$reportsController = new ReportsController($pdo);
$revenue = $reportsController->getRevenueByDate(date('Y-m-d'));
$popularServices = $reportsController->getPopularServices();
$activeTables = $reportsController->getActiveTables();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Báo Cáo & Thống Kê</title>
    <link rel="stylesheet" href="../css/baocao.css">
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
        <h1>Báo Cáo & Thống Kê</h1>
        <p>Doanh thu hôm nay: <?php echo $revenue; ?></p>
        <p>Bàn hoạt động: <?php echo $activeTables; ?></p>
        <h2>Dịch vụ bán chạy</h2>
        <table>
            <tr>
                <th>Tên Dịch Vụ</th>
                <th>Số Lượng</th>
            </tr>
            <?php foreach ($popularServices as $service): ?>
                <tr>
                    <td><?php echo htmlspecialchars($service['ServiceName']); ?></td>
                    <td><?php echo htmlspecialchars($service['count']); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <?php require_once 'footer.php'; ?>
    <script src="../js/baocao.js"></script>
</body>
</html>