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
}
?>