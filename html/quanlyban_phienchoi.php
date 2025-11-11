<?php
require_once 'header.php';
require_once __DIR__ . '/../backend/config/database.php';
require_once __DIR__ . '/../backend/controllers/Tables.php';
require_once __DIR__ . '/../backend/controllers/Pos.php';

$tablesController = new TablesController($pdo);
$posController = new PosController($pdo);
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
            <!-- Form thêm bàn -->
        <form id="createTableForm" onsubmit="createTable(event)">
            <input type="text" name="TableName" placeholder="Tên bàn (VD: VIP 01)" required>
            <input type="number" name="HourlyRate" placeholder="Giá giờ (VD: 150000)" required>
            <textarea name="Description" placeholder="Mô tả bàn"></textarea>
            <button type="submit">Thêm Bàn Mới</button>
        </form>

        <h2>Danh sách bàn</h2>
        <table>
            <tr>
                <th>Bàn</th>
                <th>Trạng Thái</th>
                <th>Giá/Giờ</th>
                <th>Hóa Đơn</th>
                <th>Hành Động</th>
            </tr>
            <?php foreach ($tables as $table): 
            $invoice = $posController->getActiveInvoiceByTable($table['TableID']);
            ?>
                <tr data-table-id="<?php echo $table['TableID']; ?>">
                <td><strong><?php echo htmlspecialchars($table['TableName']); ?></strong></td>
                <td>
                    <span class="status-badge status-<?php echo strtolower($table['Status']); ?>">
                        <?php echo $table['Status'] == 'Available' ? 'Trống' : ($table['Status'] == 'Playing' ? 'Đang chơi' : 'Bảo trì'); ?>
                    </span>
                </td>
                <td><?php echo number_format($table['HourlyRate']); ?> ₫</td>
                <td>
                    <?php if ($invoice): ?>
                        <span style="color:#28a745;font-weight:600;">#<?php echo $invoice['InvoiceID']; ?></span>
                    <?php else: ?>
                        <span style="color:#999;">Chưa có</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($table['Status'] == 'Available'): ?>
                        <button class="btn-start" onclick="startPlaying(<?php echo $table['TableID']; ?>)">
                            Bắt Đầu Chơi
                        </button>
                    <?php endif; ?>
                    
                    <?php 
                    // ÉP KIỂM TRA SIÊU CHẮC CHẮN
                    $hasActiveInvoice = ($table['Status'] == 'Playing');
                    if ($hasActiveInvoice): 
                    ?>
                        <button class="btn-pay" onclick="window.location.href='thanhtoan.php'">
                            Quản lý hóa đơn
                        </button>
                    <?php endif; ?>
                    <button onclick="updateStatus(<?php echo $table['TableID']; ?>, 'Available')" <?php echo $table['Status'] == 'Available' ? 'disabled' : ''; ?>>Trống</button>
                    <button onclick="updateStatus(<?php echo $table['TableID']; ?>, 'Playing')" <?php echo $table['Status'] == 'Playing' ? 'disabled' : ''; ?>>Đang chơi</button>
                    <button onclick="updateStatus(<?php echo $table['TableID']; ?>, 'Maintenance')" <?php echo $table['Status'] == 'Maintenance' ? 'disabled' : ''; ?>>Bảo trì</button>
                    <button class="btn-delete" onclick="deleteTable(<?php echo $table['TableID']; ?>)">Xóa</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <?php require_once 'footer.php'; ?>
    <script src="../js/quanlyban_phienchoi.js"></script>
</body>
</html>
