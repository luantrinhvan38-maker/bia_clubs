<?php
require_once __DIR__ . '/../models/Invoice.php';
require_once __DIR__ . '/../models/Table.php';
require_once __DIR__ . '/Tables.php';

class PosController {
    private $pdo;
    private $invoiceModel;
    private $tablesController;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->invoiceModel = new InvoiceModel($pdo);
        $this->tablesController = new TablesController($pdo);
    }

    public function confirmPayment($id, $paymentMethod) {
        $invoice = $this->invoiceModel->getById($id);
        if (!$invoice) {
            return false;
        }
        
        // Trước khi xác nhận thanh toán, gọi endSession để cập nhật EndTime, TimePlay, TotalAmount
        $this->tablesController->endSession($id);
        
        // Sau đó cập nhật PaymentMethod và IsPaid
        $result = $this->invoiceModel->confirmPayment($id, $paymentMethod, true);
        
        // Cập nhật trạng thái bàn về Available
        if (isset($invoice['TableID'])) {
            $tableModel = new TableModel($this->pdo);
            $tableModel->updateStatus($invoice['TableID'], 'Available');
        }
        return $result;
    }

    public function getInvoiceById($id) {
        return $this->invoiceModel->getById($id);
    }

    public function getActiveInvoiceByTable($tableId) {
    $stmt = $this->pdo->prepare("
            SELECT i.* FROM Invoices i 
            WHERE i.TableID = ? 
              AND i.EndTime IS NULL 
              AND i.IsPaid = 0 
            ORDER BY i.InvoiceID DESC 
            LIMIT 1
        ");
    $stmt->execute([$tableId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result !== false ? $result : null; // Trả về null nếu không có
    }
}
?>

