<?php
require_once __DIR__ . '/controllers/Dashboard.php';
require_once __DIR__ . '/controllers/Tables.php';
require_once __DIR__ . '/controllers/Services.php';
require_once __DIR__ . '/controllers/Pos.php';
require_once __DIR__ . '/controllers/Invoices.php';
require_once __DIR__ . '/controllers/Reports.php';
require_once __DIR__ . '/config/database.php';

$dashboardController = new DashboardController($pdo);
$tablesController = new TablesController($pdo);
$servicesController = new ServicesController($pdo);
$posController = new PosController($pdo);
$invoicesController = new InvoicesController($pdo);
$reportsController = new ReportsController($pdo);

$action = $_GET['action'] ?? '';

switch ($action) {
    // Dashboard
    case 'dashboard_data':
        echo json_encode($dashboardController->getDashboardData());
        break;

    // Tables
    case 'get_tables':
        echo json_encode($tablesController->getAllTables());
        break;
    case 'get_table':
        $id = $_GET['id'];
        echo json_encode($tablesController->getTableById($id));
        break;
    case 'create_table':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            $tablesController->createTable($data);
            echo json_encode(['success' => true]);
        }
        break;
    case 'update_table':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $data = $_POST;
            $tablesController->updateTable($id, $data);
            echo json_encode(['success' => true]);
        }
        break;
    case 'delete_table':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $tablesController->deleteTable($id);
            echo json_encode(['success' => true]);
        }
        break;
    case 'update_table_status':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $status = $_POST['status'];
            $tablesController->updateTableStatus($id, $status);
            echo json_encode(['success' => true]);
        }
        break;
    case 'create_session':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tableId = $_POST['tableId'];
            $tablesController->createSession($tableId);
            echo json_encode(['success' => true]);
        }
        break;
    case 'end_session':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $invoiceId = $_POST['invoiceId'];
            $tablesController->endSession($invoiceId);
            echo json_encode(['success' => true]);
        }
        break;

    // Services
    case 'get_services':
        echo json_encode($servicesController->getAllServices());
        break;
    case 'get_service':
        $id = $_GET['id'];
        echo json_encode($servicesController->getServiceById($id));
        break;
    case 'create_service':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            $servicesController->createService($data);
            echo json_encode(['success' => true]);
        }
        break;
    case 'update_service':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $data = $_POST;
            $servicesController->updateService($id, $data);
            echo json_encode(['success' => true]);
        }
        break;
    case 'delete_service':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $servicesController->deleteService($id);
            echo json_encode(['success' => true]);
        }
        break;
    case 'add_service_to_invoice':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $invoiceId = $_POST['invoiceId'];
            $tableId = $_POST['tableId'];
            $serviceId = $_POST['serviceId'];
            $numbers = $_POST['numbers'];
            $servicesController->addServiceToInvoice($invoiceId, $tableId, $serviceId, $numbers);
            echo json_encode(['success' => true]);
        }
        break;

    // POS
    case 'confirm_payment':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $paymentMethod = $_POST['paymentMethod'];
            $posController->confirmPayment($id, $paymentMethod);
            echo json_encode(['success' => true]);
        }
        break;

    // Invoices
    case 'get_invoices':
        echo json_encode($invoicesController->getAllInvoices());
        break;
    case 'get_invoice':
        $id = $_GET['id'];
        echo json_encode($invoicesController->getInvoiceById($id));
        break;
    case 'create_invoice':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            $invoicesController->createInvoice($data);
            echo json_encode(['success' => true]);
        }
        break;
    case 'update_invoice':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $data = $_POST;
            $invoicesController->updateInvoice($id, $data);
            echo json_encode(['success' => true]);
        }
        break;
    case 'delete_invoice':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $invoicesController->deleteInvoice($id);
            echo json_encode(['success' => true]);
        }
        break;

    // Reports
    case 'get_revenue':
        $date = $_GET['date'];
        echo json_encode(['revenue' => $reportsController->getRevenueByDate($date)]);
        break;
    case 'get_popular_services':
        echo json_encode($reportsController->getPopularServices());
        break;
    case 'get_active_tables':
        echo json_encode(['active' => $reportsController->getActiveTables()]);
        break;
}
?>