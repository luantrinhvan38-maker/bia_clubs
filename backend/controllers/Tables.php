<?php
require_once __DIR__ . '/../models/Table.php';

class TablesController {
    private $pdo;
    private $tableModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->tableModel = new TableModel($pdo);
    }

    public function getAllTables() {
        return $this->tableModel->getAll();
    }

    public function getTableById($id) {
        return $this->tableModel->getById($id);
    }

    public function createTable($data) {
        return $this->tableModel->create($data);
    }

    public function updateTable($id, $data) {
        return $this->tableModel->update($id, $data);
    }

    public function deleteTable($id) {
        return $this->tableModel->delete($id);
    }

    public function updateTableStatus($id, $status) {
        return $this->tableModel->updateStatus($id, $status);
    }

    public function createSession($tableId) {
        $this->updateTableStatus($tableId, 'Playing');
        $invoiceModel = new InvoiceModel($this->pdo);
        
        // Kiểm tra hóa đơn chưa thanh toán cho bàn này
        $stmt = $this->pdo->prepare("SELECT * FROM Invoices WHERE TableID = ? AND IsPaid = 0 LIMIT 1");
        $stmt->execute([$tableId]);
        $existingInvoice = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existingInvoice) {
            // Đã có hóa đơn chưa thanh toán, trả về hóa đơn đó
            return $existingInvoice;
        } else {
            // Chưa có, tạo mới hóa đơn dùng NOW() từ DB để đảm bảo timezone đúng
            $stmt = $this->pdo->prepare("INSERT INTO Invoices (TableID, StartTime, EndTime, TimePlay, TotalAmount, PaymentMethod, IsPaid) VALUES (?, NOW(), NULL, NULL, 0, NULL, 0)");
            $stmt->execute([$tableId]);
            
            // Lấy hóa đơn vừa tạo để trả về
            $stmt = $this->pdo->prepare("SELECT * FROM Invoices WHERE TableID = ? AND IsPaid = 0 ORDER BY InvoiceID DESC LIMIT 1");
            $stmt->execute([$tableId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }

    public function endSession($invoiceId) {
        $invoiceModel = new InvoiceModel($this->pdo);
        $invoice = $invoiceModel->getById($invoiceId);
        
        // DEBUG: Log thông tin
        error_log("=== endSession DEBUG ===");
        error_log("InvoiceID: " . $invoiceId);
        error_log("StartTime (from DB): " . ($invoice['StartTime'] ?? 'NULL'));
        error_log("Server timezone: " . date_default_timezone_get());
        
        if (!$invoice || empty($invoice['StartTime'])) {
            error_log("ERROR: Invoice not found or StartTime empty");
            // Dùng NOW() từ DB để đảm bảo timezone
            $stmt = $this->pdo->prepare("UPDATE Invoices SET EndTime = NOW(), TimePlay = 0, TotalAmount = 0 WHERE InvoiceID = ?");
            $stmt->execute([$invoiceId]);
            return;
        }

        $startTs = strtotime($invoice['StartTime']);
        // Lấy EndTime từ DB để đảm bảo timezone (thay vì dùng date() PHP)
        $stmt = $this->pdo->prepare("SELECT NOW() as now_time");
        $stmt->execute();
        $dbNow = $stmt->fetch(PDO::FETCH_ASSOC)['now_time'];
        $endTs = strtotime($dbNow);
        
        error_log("StartTime timestamp: " . $startTs . " (" . date('Y-m-d H:i:s', $startTs) . ")");
        error_log("EndTime (from DB NOW()): " . $dbNow);
        error_log("EndTime timestamp: " . $endTs . " (" . date('Y-m-d H:i:s', $endTs) . ")");
        error_log("Diff (endTs - startTs): " . ($endTs - $startTs) . " seconds");
        
        $timePlay = 0;
        if ($startTs !== false && $endTs !== false) {
            $timePlay = ($endTs - $startTs) / 3600; // giờ
            error_log("TimePlay (hours): " . $timePlay);
            if ($timePlay < 0) {
                error_log("WARNING: TimePlay is negative! Clamping to 0");
                $timePlay = 0;
            }
        }

        $table = $this->getTableById($invoice['TableID']);
        $hourly = isset($table['HourlyRate']) ? floatval($table['HourlyRate']) : 0;
        $totalAmount = $timePlay * $hourly;
        error_log("HourlyRate: " . $hourly);
        error_log("TotalAmount (before clamp): " . $totalAmount);
        
        if ($totalAmount < 0) {
            error_log("WARNING: TotalAmount is negative! Clamping to 0");
            $totalAmount = 0;
        }

        // Round to 2 decimals
        $timePlay = round($timePlay, 2);
        $totalAmount = round($totalAmount, 2);
        
        error_log("Final TimePlay: " . $timePlay);
        error_log("Final TotalAmount: " . $totalAmount);
        error_log("=== endSession DEBUG END ===");

        // Dùng $dbNow từ DB để đảm bảo EndTime đúng timezone
        $invoiceModel->endSession($invoiceId, $dbNow, $timePlay, $totalAmount);
        $this->updateTableStatus($invoice['TableID'], 'Available');
    }
}
?>