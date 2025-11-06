<?php
require_once __DIR__ . '/../models/Service.php';

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
        $data = ['InvoiceID' => $invoiceId, 'TableID' => $tableId, 'ServiceID' => $serviceId, 'Numbers' => $numbers, 'Price_Services' => $price, 'Note' => ''];
        $invoiceDetailModel = new InvoiceDetailModel($this->pdo);
        return $invoiceDetailModel->create($data);
    }
}
?>