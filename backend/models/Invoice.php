<?php
require_once __DIR__ . '/../config/database.php';

class InvoiceModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAll() {
        $stmt = $this->pdo->prepare("SELECT * FROM Invoices");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM Invoices WHERE InvoiceID = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $stmt = $this->pdo->prepare("INSERT INTO Invoices (TableID, StartTime, EndTime, TimePlay, TotalAmount, PaymentMethod, IsPaid) VALUES (?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$data['TableID'], $data['StartTime'], $data['EndTime'], $data['TimePlay'], $data['TotalAmount'], $data['PaymentMethod'], $data['IsPaid']]);
    }

    public function update($id, $data) {
        $stmt = $this->pdo->prepare("UPDATE Invoices SET TableID = ?, StartTime = ?, EndTime = ?, TimePlay = ?, TotalAmount = ?, PaymentMethod = ?, IsPaid = ? WHERE InvoiceID = ?");
        return $stmt->execute([$data['TableID'], $data['StartTime'], $data['EndTime'], $data['TimePlay'], $data['TotalAmount'], $data['PaymentMethod'], $data['IsPaid'], $id]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM Invoices WHERE InvoiceID = ?");
        return $stmt->execute([$id]);
    }

    public function endSession($id, $endTime, $timePlay, $totalAmount) {
        $stmt = $this->pdo->prepare("UPDATE Invoices SET EndTime = ?, TimePlay = ?, TotalAmount = ? WHERE InvoiceID = ?");
        return $stmt->execute([$endTime, $timePlay, $totalAmount, $id]);
    }

    public function confirmPayment($id, $paymentMethod, $isPaid) {
        $stmt = $this->pdo->prepare("UPDATE Invoices SET PaymentMethod = ?, IsPaid = ? WHERE InvoiceID = ?");
        return $stmt->execute([$paymentMethod, $isPaid, $id]);
    }
}
?>