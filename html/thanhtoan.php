<?php
require_once 'header.php';
require_once __DIR__ . '/../backend/config/database.php';
require_once __DIR__ . '/../backend/controllers/Pos.php';

$posController = new PosController($pdo);
// Giả sử lấy invoice ID từ GET hoặc session
$invoiceId = $_GET['id'] ?? 0;
$invoice = $posController->getInvoiceById($invoiceId);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh Toán</title>
    <link rel="stylesheet" href="../css/thanhtoan.css">
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
        <h1>Thanh Toán Hóa Đơn</h1>
        <p>Tổng tiền: <?php echo $invoice['TotalAmount']; ?></p>
        <form id="paymentForm" onsubmit="confirmPayment(event)">
            <input type="hidden" name="id" value="<?php echo $invoiceId; ?>">
            <select name="paymentMethod">
                <option value="Cash">Tiền mặt</option>
                <option value="Card">Thẻ</option>
                <option value="Other">Khác</option>
            </select>
            <button type="submit">Xác nhận thanh toán</button>
        </form>
    </div>
    <?php require_once 'footer.php'; ?>
    <script src="../js/thanhtoan.js"></script>
</body>
</html>