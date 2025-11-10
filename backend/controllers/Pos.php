<?php
require_once __DIR__ . '/../models/Invoice.php';

class PosController {
    private $pdo;
    private $invoiceModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->invoiceModel = new InvoiceModel($pdo);
    }

    public function confirmPayment($id, $paymentMethod) {
        return $this->invoiceModel->confirmPayment($id, $paymentMethod, true);
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

