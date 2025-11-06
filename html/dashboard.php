<?php
require_once 'header.php';
require_once __DIR__ . '/../backend/config/database.php';
require_once __DIR__ . '/../backend/controllers/Dashboard.php';

$dashboardController = new DashboardController($pdo);
$data = $dashboardController->getDashboardData();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../css/dashboard.css">
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
        <h1>Tổng Quan</h1>
        <p>Bàn hoạt động: <?php echo $data['activeTables']; ?></p>
        <p>Tổng doanh thu: <?php echo $data['totalRevenue']; ?></p>
        <p>Tổng phiên chơi: <?php echo $data['totalSessions']; ?></p>
        <h2>Danh sách bàn</h2>
        <table>
            <tr>
                <th>Mã Bàn</th>
                <th>Trạng Thái</th>
            </tr>
            <?php foreach ($data['tables'] as $table): ?>
                <tr>
                    <td><?php echo htmlspecialchars($table['TableName']); ?></td>
                    <td><?php echo htmlspecialchars($table['Status']); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <?php require_once 'footer.php'; ?>
    <script src="../js/dashboard.js"></script>
</body>
</html>