<?php

class ExtinguisherController extends Controller
{
    private $extModel;

    public function __construct()
    {
        $this->extModel = new Extinguisher();
    }

    public function index()
    {
        AuthMiddleware::check();
        $extinguishers = $this->extModel->findAll();
        $this->jsonResponse($extinguishers);
    }

    public function store()
    {
        AuthMiddleware::hasRole(['Super Admin', 'Admin']);
        $data = $this->getJsonInput();

        require_once __DIR__ . '/../helpers/qr_helper.php';
        require_once __DIR__ . '/../helpers/pdf_helper.php';

        if (!$this->validateRequiredParams(['type', 'capacity', 'client_id'], $data)) {
            $this->jsonResponse(["message" => "Type, capacity and client_id are required fields"], 400);
        }

        // Generate unique serial number (e.g., FEMS-YYYYMMDD-uniqid)
        $data['serial_number'] = 'FEMS-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));

        // Note: QR code generation would logically happen here using a helper.
        // File path assigned preemptively.
        $data['qr_code_path'] = '/uploads/qrcodes/' . $data['serial_number'] . '.png';

        if (!empty($data['manufacturing_date']) && !empty($data['filling_date'])) {
            if (strtotime($data['manufacturing_date']) > strtotime($data['filling_date'])) {
                $this->jsonResponse(["message" => "Manufacturing date cannot be after filling date"], 400);
            }
        }

        if (!empty($data['filling_date']) && !empty($data['expiry_date'])) {
            if (strtotime($data['filling_date']) > strtotime($data['expiry_date'])) {
                $this->jsonResponse(["message" => "Filling date cannot be after expiry date"], 400);
            }
        }

        $id = $this->extModel->create($data);
        if ($id) {
            // Generate QR Code
            $qrPath = QRHelper::generate($id, $data['serial_number']);

            // Generate Initial Inspection Label (PDF)
            // Fetch client name for the label
            $clientModel = new Client();
            $client = $clientModel->findById($data['client_id']);
            $clientName = $client ? $client['company_name'] : 'Unknown';

            require_once __DIR__ . '/../helpers/pdf_helper.php';
            $pdfPath = PDFHelper::generateLabel($id, [
                'serial_number' => $data['serial_number'],
                'type' => $data['type'],
                'capacity' => $data['capacity'],
                'client_name' => $clientName,
                'filling_date' => $data['filling_date'],
                'expiry_date' => $data['expiry_date']
            ]);

            // Update record with actual file paths
            $ext = $this->extModel->findById($id);
            $ext['qr_code_path'] = $qrPath;
            $ext['label_pdf_path'] = $pdfPath;
            $this->extModel->update($id, $ext);

            $this->jsonResponse([
                "message" => "Extinguisher created successfully",
                "id" => $id,
                "serial_number" => $data['serial_number'],
                "qr_path" => $qrPath,
                "pdf_path" => $pdfPath
            ], 201);
        }
        $this->jsonResponse(["message" => "Failed to create extinguisher"], 500);
    }

    public function update($id)
    {
        AuthMiddleware::hasRole(['Super Admin', 'Admin']);
        $data = $this->getJsonInput();

        $ext = $this->extModel->findById($id);
        if (!$ext) {
            $this->jsonResponse(["message" => "Extinguisher not found"], 404);
        }

        if ($this->extModel->update($id, array_merge($ext, $data))) {
            $this->jsonResponse(["message" => "Extinguisher updated successfully"]);
        }
        $this->jsonResponse(["message" => "Update failed"], 500);
    }

    public function destroy($id)
    {
        AuthMiddleware::hasRole(['Super Admin']);

        $ext = $this->extModel->findById($id);
        if (!$ext) {
            $this->jsonResponse(["message" => "Extinguisher not found"], 404);
        }

        // Cleanup files
        if (!empty($ext['qr_code_path'])) {
            $file = __DIR__ . '/..' . $ext['qr_code_path'];
            if (file_exists($file))
                unlink($file);
        }
        if (!empty($ext['label_pdf_path'])) {
            $file = __DIR__ . '/..' . $ext['label_pdf_path'];
            if (file_exists($file))
                unlink($file);
        }

        if ($this->extModel->delete($id)) {
            $this->jsonResponse(["message" => "Extinguisher deleted"]);
        }
        $this->jsonResponse(["message" => "Deletion failed"], 500);
    }

    public function show($id)
    {
        AuthMiddleware::check();
        $ext = $this->extModel->findById($id);
        if ($ext) {
            $this->jsonResponse($ext);
        }
        $this->jsonResponse(["message" => "Extinguisher not found"], 404);
    }
}
