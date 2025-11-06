<?php
require_once 'header.php';
require_once __DIR__ . '/../backend/config/database.php';
require_once __DIR__ . '/../backend/controllers/Tables.php';

$tablesController = new TablesController($pdo);
$tables = $tablesController->getAllTables();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Bàn & Phiên Chơi</title>
    <link rel="stylesheet" href="../css/quanlyban_phienchoi.css">
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
        <h1>Quản lý Bàn & Phiên Chơi</h1>
        <!-- Form thêm bàn -->
        <form id="createTableForm" onsubmit="createTable(event)">
            <input type="text" name="TableName" placeholder="Tên bàn" required>
            <select name="Status">
                <option value="Available">Trống</option>
                <option value="Playing">Đang chơi</option>
                <option value="Maintenance">Bảo trì</option>
            </select>
            <input type="number" name="HourlyRate" placeholder="Giá giờ" required>
            <textarea name="Description" placeholder="Mô tả"></textarea>
            <button type="submit">Thêm bàn</button>
        </form>

        <h2>Danh sách bàn</h2>
        <table>
            <tr>
                <th>Mã Bàn</th>
                <th>Trạng Thái</th>
                <th>Hành Động</th>
            </tr>
            <?php foreach ($tables as $table): ?>
                <tr>
                    <td><?php echo htmlspecialchars($table['TableName']); ?></td>
                    <td><?php echo htmlspecialchars($table['Status']); ?></td>
                    <td>
                        <button onclick="updateStatus(<?php echo $table['TableID']; ?>, 'Available')">Trống</button>
                        <button onclick="updateStatus(<?php echo $table['TableID']; ?>, 'Playing')">Đang chơi</button>
                        <button onclick="deleteTable(<?php echo $table['TableID']; ?>)">Xóa</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <?php require_once 'footer.php'; ?>
    <script src="../js/quanlyban_phienchoi.js"></script>
</body>
</html>