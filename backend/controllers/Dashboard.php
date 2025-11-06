<?php
require_once __DIR__ . '/../models/Table.php';
require_once __DIR__ . '/../models/Invoice.php';

class DashboardController {
    private $pdo;
    private $tableModel;
    private $invoiceModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->tableModel = new TableModel($pdo);
        $this->invoiceModel = new InvoiceModel($pdo);
    }

    public function getDashboardData() {
        $tables = $this->tableModel->getAll();
        $invoices = $this->invoiceModel->getAll();
        $totalRevenue = 0;
        foreach ($invoices as $invoice) {
            $totalRevenue += $invoice['TotalAmount'];
        }
        $activeTables = count(array_filter($tables, fn($t) => $t['Status'] == 'Playing'));
        return ['tables' => $tables, 'totalRevenue' => $totalRevenue, 'activeTables' => $activeTables, 'totalSessions' => count($invoices)];
    }
}
?>