<?php
require_once __DIR__ . '/../models/Invoice.php';
require_once __DIR__ . '/../models/InvoiceDetail.php';

class InvoicesController {
    private $pdo;
    private $invoiceModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->invoiceModel = new InvoiceModel($pdo);
    }

    public function getAllInvoices() {
        return $this->invoiceModel->getAll();
    }

    public function getInvoiceById($id) {
        return $this->invoiceModel->getById($id);
    }

    public function createInvoice($data) {
        return $this->invoiceModel->create($data);
    }

    public function updateInvoice($id, $data) {
        return $this->invoiceModel->update($id, $data);
    }

    public function deleteInvoice($id) {
        // Xóa bản ghi chi tiết hóa đơn trước (InvoiceDetails)
        $stmt = $this->pdo->prepare("DELETE FROM InvoiceDetails WHERE InvoiceID = ?");
        $stmt->execute([$id]);
        
        // Sau đó xóa hóa đơn
        return $this->invoiceModel->delete($id);
    }
}
?>