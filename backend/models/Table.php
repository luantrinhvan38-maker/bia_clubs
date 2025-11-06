<?php
require_once __DIR__ . '/../config/database.php';

class TableModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAll() {
        $stmt = $this->pdo->prepare("SELECT * FROM Tables");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM Tables WHERE TableID = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $stmt = $this->pdo->prepare("INSERT INTO Tables (TableName, Status, HourlyRate, Description) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$data['TableName'], $data['Status'], $data['HourlyRate'], $data['Description']]);
    }

    public function update($id, $data) {
        $stmt = $this->pdo->prepare("UPDATE Tables SET TableName = ?, Status = ?, HourlyRate = ?, Description = ? WHERE TableID = ?");
        return $stmt->execute([$data['TableName'], $data['Status'], $data['HourlyRate'], $data['Description'], $id]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM Tables WHERE TableID = ?");
        return $stmt->execute([$id]);
    }

    public function updateStatus($id, $status) {
        $stmt = $this->pdo->prepare("UPDATE Tables SET Status = ? WHERE TableID = ?");
        return $stmt->execute([$status, $id]);
    }
}
?>