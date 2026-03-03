<?php

class ReportController extends Controller
{
    public function index()
    {
        AuthMiddleware::hasRole(['Super Admin', 'Admin']);

        $type = isset($_GET['type']) ? $_GET['type'] : 'inventory';
        $format = isset($_GET['format']) ? $_GET['format'] : 'pdf';
        $startDate = isset($_GET['start_date']) ? $_GET['start_date'] : null;
        $endDate = isset($_GET['end_date']) ? $_GET['end_date'] : null;
        $clientId = isset($_GET['client_id']) ? $_GET['client_id'] : null;

        require_once __DIR__ . '/../helpers/report_pdf_helper.php';
        require_once __DIR__ . '/../helpers/excel_helper.php';

        $db = Database::getConnection();
        $data = [];
        $headers = [];
        $title = "";

        switch ($type) {
            case 'inventory':
                $title = "Inventory Report";
                $headers = ['ID', 'Serial', 'Type', 'Capacity', 'Status', 'Client'];
                $query = "SELECT f.id, f.serial_number, f.type, f.capacity, f.status, c.company_name 
                          FROM fire_extinguishers f 
                          LEFT JOIN clients c ON f.client_id = c.id";
                $stmt = $db->prepare($query);
                $stmt->execute();
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                break;

            case 'expired':
                $title = "Expired Extinguisher Report";
                $headers = ['ID', 'Serial', 'Type', 'Capacity', 'Expiry Date', 'Client'];
                $query = "SELECT f.id, f.serial_number, f.type, f.capacity, f.expiry_date, c.company_name 
                          FROM fire_extinguishers f 
                          LEFT JOIN clients c ON f.client_id = c.id 
                          WHERE f.expiry_date < CURDATE()";
                $stmt = $db->prepare($query);
                $stmt->execute();
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                break;

            case 'compliance':
                $title = "Inspection Compliance Report";
                $headers = ['Ext. ID', 'Serial', 'Type', 'Last Inspection', 'Next Due', 'Status'];
                $query = "SELECT f.id, f.serial_number, f.type, MAX(i.inspection_date) as last_insp, i.next_due_date, f.status
                          FROM fire_extinguishers f
                          JOIN inspections i ON f.id = i.extinguisher_id
                          GROUP BY f.id
                          HAVING i.next_due_date < CURDATE() OR i.next_due_date IS NULL";
                $stmt = $db->prepare($query);
                $stmt->execute();
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                break;

            case 'client':
                if (!$clientId)
                    $this->jsonResponse(["message" => "client_id is required for this report"], 400);
                $clientModel = new Client();
                $client = $clientModel->findById($clientId);
                $clientName = $client ? $client['company_name'] : "Unknown";

                $title = "Client Assets: " . $clientName;
                $headers = ['ID', 'Serial', 'Type', 'Capacity', 'Status', 'Expiry'];
                $query = "SELECT id, serial_number, type, capacity, status, expiry_date 
                          FROM fire_extinguishers 
                          WHERE client_id = :client_id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':client_id', $clientId);
                $stmt->execute();
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                break;

            case 'service':
                $title = "Service & Maintenance History";
                $headers = ['Ext. ID', 'Serial', 'Type', 'Service', 'Date', 'Admin'];
                $query = "SELECT f.id, f.serial_number, f.type, m.service_type, m.service_date, u.name 
                          FROM maintenance_records m
                          JOIN fire_extinguishers f ON m.extinguisher_id = f.id
                          JOIN users u ON m.performed_by = u.id";

                if ($startDate && $endDate) {
                    $query .= " WHERE m.service_date BETWEEN :start AND :end";
                }

                $stmt = $db->prepare($query);
                if ($startDate && $endDate) {
                    $stmt->bindParam(':start', $startDate);
                    $stmt->bindParam(':end', $endDate);
                }
                $stmt->execute();
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                break;

            default:
                $this->jsonResponse(["message" => "Invalid report type"], 400);
        }

        if ($format === 'csv') {
            $csvName = str_replace(' ', '_', strtolower($title)) . '_' . date('YmdHis') . '.csv';
            $csvPath = __DIR__ . '/../uploads/reports/' . $csvName;

            if (!file_exists(dirname($csvPath))) {
                mkdir(dirname($csvPath), 0777, true);
            }

            $output = fopen($csvPath, 'w');
            fputcsv($output, $headers);
            foreach ($data as $row) {
                fputcsv($output, $row);
            }
            fclose($output);

            $this->jsonResponse([
                "message" => "CSV Report generated",
                "file_path" => "/uploads/reports/" . $csvName
            ]);
        }
        else {
            $pdfPath = ReportPDFHelper::generate($title, $headers, $data);
            $this->jsonResponse([
                "message" => "PDF Report generated",
                "file_path" => $pdfPath
            ]);
        }
    }
}
