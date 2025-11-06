<?php
require_once __DIR__ . '/../config/database.php';

class ServiceModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAll() {
        $stmt = $this->pdo->prepare("SELECT * FROM Services");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM Services WHERE ServiceID = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $stmt = $this->pdo->prepare("INSERT INTO Services (ServiceName, Price_Service, Category, Numbers) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$data['ServiceName'], $data['Price_Service'], $data['Category'], $data['Numbers']]);
    }

    public function update($id, $data) {
        $stmt = $this->pdo->prepare("UPDATE Services SET ServiceName = ?, Price_Service = ?, Category = ?, Numbers = ? WHERE ServiceID = ?");
        return $stmt->execute([$data['ServiceName'], $data['Price_Service'], $data['Category'], $data['Numbers'], $id]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM Services WHERE ServiceID = ?");
        return $stmt->execute([$id]);
    }
}
?>