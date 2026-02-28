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
            $this->jsonResponse([
                "message" => "Extinguisher created successfully",
                "id" => $id,
                "serial_number" => $data['serial_number'],
                "qr_path" => $data['qr_code_path']
            ], 201);
        }
        $this->jsonResponse(["message" => "Failed to create extinguisher"], 500);
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
