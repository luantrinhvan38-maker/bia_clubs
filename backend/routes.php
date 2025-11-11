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
            if ($status === 'Playing') {
                $tablesController->createSession($id);
            } else {
                $tablesController->updateTableStatus($id, $status);
            }
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
            // Kiểm tra xem dịch vụ đã tồn tại chưa
            $existingService = $servicesController->getServiceByName($data['ServiceName']);
            if ($existingService) {
                echo json_encode(['success' => false, 'error' => 'Tên dịch vụ đã tồn tại!']);
            } else {
                $servicesController->createService($data);
                echo json_encode(['success' => true]);
            }
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
            $result = $posController->confirmPayment($id, $paymentMethod);
            echo json_encode(['success' => (bool)$result]);
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
            try {
                $id = $_POST['id'] ?? null;
                if (!$id) {
                    echo json_encode(['success' => false, 'error' => 'ID không hợp lệ']);
                    break;
                }
                
                error_log('Deleting invoice with ID: ' . $id);
                $result = $invoicesController->deleteInvoice($id);
                echo json_encode(['success' => (bool)$result, 'message' => 'Xóa thành công']);
            } catch (PDOException $e) {
                error_log('PDO Error in delete_invoice: ' . $e->getMessage());
                echo json_encode(['success' => false, 'error' => 'Lỗi cơ sở dữ liệu: ' . $e->getMessage()]);
            } catch (Exception $e) {
                error_log('General Error in delete_invoice: ' . $e->getMessage());
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Phương thức không được hỗ trợ']);
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

    case 'start_session':
        if (!isset($_POST['table_id'])) {
            echo json_encode(['success' => false, 'message' => 'Thiếu ID bàn']);
            exit;
        }
        $tableId = (int)$_POST['table_id'];
        
        // Kiểm tra bàn có trống không
        $stmt = $pdo->prepare("SELECT Status FROM Tables WHERE TableID = ?");
        $stmt->execute([$tableId]);
        $status = $stmt->fetchColumn();
        
        if ($status !== 'Available') {
            echo json_encode(['success' => false, 'message' => 'Bàn không trống!']);
            exit;
        }
        
        // Dùng controller createSession để đảm bảo đồng bộ logic (dùng DB NOW() cho StartTime)
        $invoice = $tablesController->createSession($tableId);
        if ($invoice && isset($invoice['InvoiceID'])) {
            echo json_encode(['success' => true, 'invoice_id' => $invoice['InvoiceID']]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Không thể tạo hóa đơn']);
        }
        break;

    

    // THÊM VÀO CUỐI switch ($action), trước default:
    case 'get_playing_tables':
        $stmt = $pdo->prepare("
            SELECT t.*, i.InvoiceID, i.StartTime 
            FROM Tables t 
            JOIN Invoices i ON t.TableID = i.TableID 
            WHERE t.Status = 'Playing' AND i.IsPaid = 0 AND i.EndTime IS NULL
        ");
        $stmt->execute();
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;

    case 'get_invoice_details':
        $id = $_GET['invoice_id'];
        $stmt = $pdo->prepare("SELECT i.*, t.TableName FROM Invoices i JOIN Tables t ON i.TableID = t.TableID WHERE i.InvoiceID = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode([
            'table_name' => $data['TableName'],
            'start_time' => date('d/m/Y H:i', strtotime($data['StartTime']))
        ]);
        break;

    case 'get_invoice_services':
        $id = $_GET['invoice_id'];
        $stmt = $pdo->prepare("
            SELECT s.ServiceName, id.Numbers, id.Price_Services 
            FROM InvoiceDetails id 
            JOIN Services s ON id.ServiceID = s.ServiceID 
            WHERE id.InvoiceID = ?
        ");
        $stmt->execute([$id]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;

    case 'get_playing_time':
        $id = $_GET['invoice_id'];
        $stmt = $pdo->prepare("
            SELECT i.StartTime, t.HourlyRate,
                COALESCE(SUM(id.Price_Services), 0) as service_total
            FROM Invoices i 
            JOIN Tables t ON i.TableID = t.TableID
            LEFT JOIN InvoiceDetails id ON i.InvoiceID = id.InvoiceID
            WHERE i.InvoiceID = ?
            GROUP BY i.InvoiceID
        ");
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Prevent negative hours if StartTime is in the future or invalid
        $startTs = strtotime($data['StartTime']);
        $nowTs = time();
        if ($startTs === false) {
            $hours = 0;
        } else {
            $hours = ($nowTs - $startTs) / 3600;
            if ($hours < 0) $hours = 0;
        }
        $tableFee = $hours * $data['HourlyRate'];
        if ($tableFee < 0) $tableFee = 0;
        
        echo json_encode([
            'hours' => round($hours, 2),
            'table_fee' => round($tableFee),
            'service_total' => (int)$data['service_total']
        ]);
        break;
}
?>