<?php
require_once 'header.php';
require_once __DIR__ . '/../backend/config/database.php';
require_once __DIR__ . '/../backend/controllers/Pos.php';

$posController = new PosController($pdo);
// Giả sử lấy invoice ID từ GET hoặc session
$invoiceId = $_GET['id'] ?? 0;
// Lấy hóa đơn theo ID
$invoice = $posController->getInvoiceById($invoiceId);

if (!$invoice || $invoice['IsPaid']) {
    echo '<div class="container"><h2 style="color:red;text-align:center;">Hóa đơn không tồn tại hoặc đã thanh toán!</h2></div>';
    require_once 'footer.php';
    exit;
}

// LẤY THÔNG TIN BÀN
$stmt = $pdo->prepare("SELECT TableName, HourlyRate FROM Tables WHERE TableID = ?");
$stmt->execute([$invoice['TableID']]);
$table = $stmt->fetch(PDO::FETCH_ASSOC);

// TÍNH TIỀN REALTIME
$startTime = new DateTime($invoice['StartTime']);
$endTime = $invoice['EndTime'] ? new DateTime($invoice['EndTime']) : new DateTime();
$hoursPlayed = $startTime->diff($endTime)->h + ($startTime->diff($endTime)->i / 60);
$hoursPlayed = round($hoursPlayed + ($startTime->diff($endTime)->days * 24), 2);

$tableFee = $hoursPlayed * $table['HourlyRate'];

// LẤY DỊCH VỤ ĐÃ ORDER
$stmt = $pdo->prepare("
    SELECT s.ServiceName, id.Numbers, id.Price_Services 
    FROM InvoiceDetails id 
    JOIN Services s ON id.ServiceID = s.ServiceID 
    WHERE id.InvoiceID = ?
");
$stmt->execute([$invoiceId]);
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);
$serviceTotal = array_sum(array_column($services, 'Price_Services'));

// TỔNG TIỀN
$totalAmount = $tableFee + $serviceTotal;
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
    <div class="container">
    <h1>Thanh Toán Hóa Đơn #<?php echo $invoice['InvoiceID']; ?></h1>

    <div class="invoice-info">
        <p><strong>Bàn:</strong> <?php echo htmlspecialchars($table['TableName']); ?></p>
        <p><strong>Giờ vào:</strong> <?php echo date('d/m/Y H:i', strtotime($invoice['StartTime'])); ?></p>
        <p><strong>Giờ ra:</strong> 
            <?php echo $invoice['EndTime'] ? date('d/m/Y H:i', strtotime($invoice['EndTime'])) : '<span style="color:#dc3545;">Đang chơi...</span>'; ?>
        </p>
        <p><strong>Thời gian chơi:</strong> <span style="color:#007bff;font-weight:600;"><?php echo number_format($hoursPlayed, 2); ?> giờ</span></p>
    </div>

    <div class="bill-details">
        <h2>Chi Tiết Hóa Đơn</h2>
        <table>
            <tr><th>Dịch vụ</th><th>Số lượng</th><th>Thành tiền</th></tr>
            <tr>
                <td>Thuê bàn (<?php echo number_format($table['HourlyRate']); ?>₫/giờ)</td>
                <td><?php echo number_format($hoursPlayed, 2); ?> giờ</td>
                <td><?php echo number_format($tableFee); ?>₫</td>
            </tr>
            <?php foreach ($services as $item): ?>
            <tr>
                <td><?php echo htmlspecialchars($item['ServiceName']); ?></td>
                <td>x<?php echo $item['Numbers']; ?></td>
                <td><?php echo number_format($item['Price_Services']); ?>₫</td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($services)): ?>
            <tr><td colspan="3" style="text-align:center;color:#666;">Chưa gọi món</td></tr>
            <?php endif; ?>
        </table>
    </div>

    <div class="total-amount">
        TỔNG TIỀN: <?php echo number_format($totalAmount); ?> ₫
    </div>

    <form id="paymentForm" onsubmit="confirmPayment(event)">
        <input type="hidden" name="id" value="<?php echo $invoiceId; ?>">
        <input type="hidden" name="total_amount" value="<?php echo $totalAmount; ?>">
        <select name="paymentMethod" required>
            <option value="Cash" <?php echo $invoice['PaymentMethod'] == 'Cash' ? 'selected' : ''; ?>>Tiền mặt</option>
            <option value="Card" <?php echo $invoice['PaymentMethod'] == 'Card' ? 'selected' : ''; ?>>Thẻ ngân hàng</option>
            <option value="Other" <?php echo $invoice['PaymentMethod'] == 'Other' ? 'selected' : ''; ?>>Chuyển khoản / Ví</option>
        </select>
        <button type="submit">XÁC NHẬN THANH TOÁN</button>
    </form>

    <?php if ($invoice['IsPaid']): ?>
        <div class="paid-stamp">ĐÃ THANH TOÁN</div>
    <?php endif; ?>
</div>

    <?php require_once 'footer.php'; ?>
    <script src="../js/thanhtoan.js"></script>
</body>
</html>