<?php
require_once 'header.php';
require_once __DIR__ . '/../backend/config/database.php';
require_once __DIR__ . '/../backend/controllers/Invoices.php';

$invoicesController = new InvoicesController($pdo);
$invoices = $invoicesController->getAllInvoices();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Hóa Đơn</title>
    <link rel="stylesheet" href="../css/quanlyhoadon.css">
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
        <h1>Quản lý Hóa Đơn</h1>
        <table>
            <tr>
                <th>Mã Hóa Đơn</th>
                <th>Tổng Tiền</th>
                <th>Trạng Thái</th>
                <th>Hành Động</th>
            </tr>
            <?php foreach ($invoices as $invoice): ?>
                <tr>
                    <td><?php echo htmlspecialchars($invoice['InvoiceID']); ?></td>
                    <td><?php echo htmlspecialchars($invoice['TotalAmount']); ?></td>
                    <td><?php echo htmlspecialchars($invoice['IsPaid'] ? 'Đã thanh toán' : 'Chưa thanh toán'); ?></td>
                    <td>
                        <button onclick="deleteInvoice(<?php echo $invoice['InvoiceID']; ?>)">Xóa</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <?php require_once 'footer.php'; ?>
    <script src="../js/quanlyhoadon.js"></script>
</body>
</html>