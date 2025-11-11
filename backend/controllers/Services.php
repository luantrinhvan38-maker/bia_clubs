<?php
require_once __DIR__ . '/../models/Service.php';
require_once __DIR__ . '/../models/InvoiceDetail.php';

class ServicesController {
    private $pdo;
    private $serviceModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->serviceModel = new ServiceModel($pdo);
    }

    public function getAllServices() {
        return $this->serviceModel->getAll();
    }

    public function getServiceById($id) {
        return $this->serviceModel->getById($id);
    }

    public function getServiceByName($name) {
        return $this->serviceModel->getByName($name);
    }

    public function createService($data) {
        return $this->serviceModel->create($data);
    }

    public function updateService($id, $data) {
        return $this->serviceModel->update($id, $data);
    }

    public function deleteService($id) {
        return $this->serviceModel->delete($id);
    }

    public function addServiceToInvoice($invoiceId, $tableId, $serviceId, $numbers) {
        $service = $this->getServiceById($serviceId);
        $price = $service['Price_Service'] * $numbers;
        $invoiceDetailModel = new InvoiceDetailModel($this->pdo);
        // Kiểm tra xem dịch vụ đã tồn tại trên hóa đơn chưa
        $stmt = $this->pdo->prepare("SELECT * FROM InvoiceDetails WHERE InvoiceID = ? AND ServiceID = ? LIMIT 1");
        $stmt->execute([$invoiceId, $serviceId]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($existing) {
            // Nếu đã có, cộng dồn số lượng và giá
            $newNumbers = $existing['Numbers'] + $numbers;
            $newPrice = $service['Price_Service'] * $newNumbers;
            $updateData = [
                'InvoiceID' => $invoiceId,
                'TableID' => $tableId,
                'ServiceID' => $serviceId,
                'Numbers' => $newNumbers,
                'Price_Services' => $newPrice,
                'Note' => ''
            ];
            $invoiceDetailModel->update($existing['InvoiceDetailID'], $updateData);
            return true;
        } else {
            // Nếu chưa có, thêm mới
            $data = [
                'InvoiceID' => $invoiceId,
                'TableID' => $tableId,
                'ServiceID' => $serviceId,
                'Numbers' => $numbers,
                'Price_Services' => $price,
                'Note' => ''
            ];
            return $invoiceDetailModel->create($data);
        }
    }
}
?>