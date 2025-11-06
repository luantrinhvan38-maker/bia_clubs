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
        $data = ['TableID' => $tableId, 'StartTime' => date('Y-m-d H:i:s'), 'TotalAmount' => 0];
        $invoiceModel = new InvoiceModel($this->pdo);
        return $invoiceModel->create($data);
    }

    public function endSession($invoiceId) {
        $invoiceModel = new InvoiceModel($this->pdo);
        $invoice = $invoiceModel->getById($invoiceId);
        $endTime = date('Y-m-d H:i:s');
        $timePlay = (strtotime($endTime) - strtotime($invoice['StartTime'])) / 3600; // giờ
        $table = $this->getTableById($invoice['TableID']);
        $totalAmount = $timePlay * $table['HourlyRate'];
        $invoiceModel->endSession($invoiceId, $endTime, $timePlay, $totalAmount);
        $this->updateTableStatus($invoice['TableID'], 'Available');
    }
}
?>