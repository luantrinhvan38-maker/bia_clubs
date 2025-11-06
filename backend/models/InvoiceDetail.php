<?php
require_once __DIR__ . '/../config/database.php';

class InvoiceDetailModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAll() {
        $stmt = $this->pdo->prepare("SELECT * FROM InvoiceDetails");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM InvoiceDetails WHERE InvoiceDetailID = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $stmt = $this->pdo->prepare("INSERT INTO InvoiceDetails (InvoiceID, TableID, ServiceID, Numbers, Price_Services, Note) VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$data['InvoiceID'], $data['TableID'], $data['ServiceID'], $data['Numbers'], $data['Price_Services'], $data['Note']]);
    }

    public function update($id, $data) {
        $stmt = $this->pdo->prepare("UPDATE InvoiceDetails SET InvoiceID = ?, TableID = ?, ServiceID = ?, Numbers = ?, Price_Services = ?, Note = ? WHERE InvoiceDetailID = ?");
        return $stmt->execute([$data['InvoiceID'], $data['TableID'], $data['ServiceID'], $data['Numbers'], $data['Price_Services'], $data['Note'], $id]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM InvoiceDetails WHERE InvoiceDetailID = ?");
        return $stmt->execute([$id]);
    }
}
?>