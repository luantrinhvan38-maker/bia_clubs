<?php
require_once 'header.php';
require_once __DIR__ . '/../backend/config/database.php';
require_once __DIR__ . '/../backend/controllers/Pos.php';

$posController = new PosController($pdo);
// Giả sử lấy invoice ID từ GET hoặc session
// Lấy tất cả bàn đang chơi
$stmt = $pdo->prepare("
    SELECT t.TableID, t.TableName, t.HourlyRate, i.InvoiceID, i.StartTime
    FROM Tables t
    JOIN Invoices i ON t.TableID = i.TableID
    WHERE t.Status = 'Playing' AND i.EndTime IS NULL AND i.IsPaid = 0
    ORDER BY i.StartTime DESC
");
$stmt->execute();
$playingTables = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <div class="container pos-container">
        <h1>QUẢN LÝ HÓA ĐƠN - POS</h1>
        <p style="text-align:center;color:#666;margin-bottom:20px;">Click vào bàn để xem & thêm món</p>

        <?php if (empty($playingTables)): ?>
            <div class="no-table">
                <i class="fas fa-table"></i>
                <p>Không có bàn nào đang chơi</p>
                <a href="quanlyban_phienchoi.php" class="btn-back">Quay lại quản lý bàn</a>
            </div>
        <?php else: ?>
            <div class="tables-grid">
                <?php foreach ($playingTables as $table): ?>
                    <div class="table-card" onclick="openInvoice(<?php echo $table['InvoiceID']; ?>, <?php echo $table['TableID']; ?>)">
                        <div class="table-name"><?php echo htmlspecialchars($table['TableName']); ?></div>
                        <div class="table-info">
                            <span>Hóa đơn #<?php echo $table['InvoiceID']; ?></span>
                            <span><?php echo date('H:i', strtotime($table['StartTime'])); ?> vào</span>
                        </div>
                        <div class="table-price"><?php echo number_format($table['HourlyRate']); ?>₫/giờ</div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Popup chi tiết hóa đơn -->
    <div id="invoiceModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Hóa Đơn <span id="modalInvoiceId"></span> - <span id="modalTableName"></span></h2>
            
            <div class="invoice-details">
                <div class="info-row">
                    <span>Giờ vào:</span>
                    <span id="startTime"></span>
                </div>
                <div class="info-row">
                    <span>Thời gian chơi:</span>
                    <span id="playTime">Đang tính...</span>
                </div>
                <div class="info-row">
                    <span>Tiền bàn:</span>
                    <span id="tableFee">0₫</span>
                </div>
            </div>

            <div class="services-list">
                <h3>Dịch vụ đã gọi</h3>
                <table id="servicesTable">
                    <thead>
                        <tr><th>Món</th><th>SL</th><th>Giá</th></tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <div class="add-service">
                <h3>Thêm món</h3>
                <select id="serviceSelect"></select>
                <input type="number" id="quantity" min="1" value="1" style="width:70px;">
                <button onclick="addService()">Thêm</button>
            </div>

            <div class="total-section">
                <div class="total-label">TỔNG TIỀN:</div>
                <div class="total-amount" id="totalAmount">0₫</div>
            </div>

            <div class="payment-actions">
                <select id="paymentMethod">
                    <option value="Cash">Tiền mặt</option>
                    <option value="Card">Thẻ</option>
                    <option value="Other">Chuyển khoản</option>
                </select>
                <button class="btn-pay-final" onclick="confirmPayment()">THANH TOÁN</button>
            </div>
        </div>
    </div>

    <?php require_once 'footer.php'; ?>
    <script src="../js/thanhtoan.js"></script>
</body>
</html>