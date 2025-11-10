<?php
// require_once __DIR__ . '/../models/Invoice.php';
// require_once __DIR__ . '/../models/Service.php';

// class ReportsController {
//     private $pdo;
//     private $invoiceModel;
//     private $serviceModel;

//     public function __construct($pdo) {
//         $this->pdo = $pdo;
//         $this->invoiceModel = new InvoiceModel($pdo);
//         $this->serviceModel = new ServiceModel($pdo);
//     }

//     public function getRevenueByDate($date) {
//         $stmt = $this->pdo->prepare("SELECT SUM(TotalAmount) as total FROM Invoices WHERE DATE(InvoiceDate) = ?");
//         $stmt->execute([$date]);
//         return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
//     }

//     public function getPopularServices() {
//         $stmt = $this->pdo->prepare("SELECT s.ServiceName, COUNT(id.ServiceID) as count FROM InvoiceDetails id JOIN Services s ON id.ServiceID = s.ServiceID GROUP BY id.ServiceID ORDER BY count DESC");
//         $stmt->execute();
//         return $stmt->fetchAll(PDO::FETCH_ASSOC);
//     }

//     public function getActiveTables() {
//         $tableModel = new TableModel($this->pdo);
//         $tables = $tableModel->getAll();
//         return count(array_filter($tables, fn($t) => $t['Status'] == 'Playing'));
//     }
// }
?>

<?php
require_once __DIR__ . '/../models/Invoice.php';
require_once __DIR__ . '/../models/Service.php';
require_once __DIR__ . '/../models/Table.php'; // ĐÃ THÊM DÒNG NÀY - FIX LỖI

class ReportsController {
    private $pdo;
    private $invoiceModel;
    private $serviceModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->invoiceModel = new InvoiceModel($pdo);
        $this->serviceModel = new ServiceModel($pdo);
    }

    public function getRevenueByDate($date) {
        $stmt = $this->pdo->prepare("SELECT SUM(TotalAmount) as total FROM Invoices WHERE DATE(InvoiceDate) = ? AND IsPaid = 1");
        $stmt->execute([$date]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    public function getPopularServices() {
        $stmt = $this->pdo->prepare("
            SELECT s.ServiceName, COUNT(id.ServiceID) as count, SUM(id.Price_Services) as revenue 
            FROM InvoiceDetails id 
            JOIN Services s ON id.ServiceID = s.ServiceID 
            GROUP BY id.ServiceID 
            ORDER BY count DESC 
            LIMIT 5
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getActiveTables() {
        $tableModel = new TableModel($this->pdo); // ĐÃ SỬ DỤNG ĐÚNG
        $tables = $tableModel->getAll();
        return count(array_filter($tables, fn($t) => $t['Status'] == 'Playing'));
    }

    public function getTodayStats() {
        $today = date('Y-m-d');
        return [
            'revenue' => $this->getRevenueByDate($today),
            'active_tables' => $this->getActiveTables(),
            'total_sessions' => $this->pdo->query("SELECT COUNT(*) FROM Invoices WHERE DATE(InvoiceDate) = '$today'")->fetchColumn()
        ];
    }
}
?>