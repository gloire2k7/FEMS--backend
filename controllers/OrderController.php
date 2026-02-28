<?php

class OrderController extends Controller
{
    private $orderModel;
    private $extModel;

    public function __construct()
    {
        $this->orderModel = new Order();
        $this->extModel = new Extinguisher();
    }

    public function index()
    {
        AuthMiddleware::check();

        if ($_SESSION['role_name'] === 'Super Admin' || $_SESSION['role_name'] === 'Admin') {
            $orders = $this->orderModel->findAll();
        }
        else {
            // Client only sees their own orders
            $orders = $this->orderModel->findByClientId($_SESSION['company_id']);
        }

        $this->jsonResponse($orders);
    }

    public function store()
    {
        AuthMiddleware::hasRole(['Company User']);
        $data = $this->getJsonInput();

        if (!$this->validateRequiredParams(['type', 'capacity', 'quantity'], $data)) {
            $this->jsonResponse(["message" => "Type, capacity and quantity are required"], 400);
        }

        $data['client_id'] = $_SESSION['company_id'];
        $data['status'] = 'pending';

        $id = $this->orderModel->create($data);
        if ($id) {
            $this->jsonResponse(["message" => "Order placed successfully", "id" => $id], 201);
        }
        $this->jsonResponse(["message" => "Failed to place order"], 500);
    }

    public function grant($id)
    {
        AuthMiddleware::hasRole(['Super Admin', 'Admin']);

        $order = $this->orderModel->findById($id);
        if (!$order) {
            $this->jsonResponse(["message" => "Order not found"], 404);
        }

        if ($order['status'] !== 'pending') {
            $this->jsonResponse(["message" => "Order is already " . $order['status']], 400);
        }

        // 1. Find available stock
        $available = $this->extModel->findAvailableInStock($order['type'], $order['capacity'], $order['quantity']);

        if (count($available) < $order['quantity']) {
            $this->jsonResponse([
                "message" => "Insufficient stock. Only " . count($available) . " available.",
                "available_count" => count($available)
            ], 400);
        }

        // 2. Allocate and Generate Labels
        require_once __DIR__ . '/../helpers/pdf_helper.php';

        $clientModel = new Client();
        $client = $clientModel->findById($order['client_id']);
        $clientName = $client ? $client['company_name'] : 'Unknown';

        foreach ($available as $ext) {
            // Assign to client and order
            $this->extModel->allocateToOrder($ext['id'], $order['client_id'], $order['id']);

            // Generate PDF Label now that we have a client
            $pdfPath = PDFHelper::generateLabel($ext['id'], [
                'serial_number' => $ext['serial_number'],
                'type' => $ext['type'],
                'capacity' => $ext['capacity'],
                'client_name' => $clientName,
                'filling_date' => $ext['filling_date'],
                'expiry_date' => $ext['expiry_date']
            ]);

            // Update ext with label path
            $updatedExt = $this->extModel->findById($ext['id']);
            $updatedExt['label_pdf_path'] = $pdfPath;
            $this->extModel->update($ext['id'], $updatedExt);
        }

        // 3. Update Order Status
        if ($this->orderModel->updateStatus($id, 'granted')) {
            $this->jsonResponse(["message" => "Order granted and stock allocated"]);
        }
        $this->jsonResponse(["message" => "Failed to update order status"], 500);
    }

    public function confirm($id)
    {
        AuthMiddleware::hasRole(['Company User']);

        $order = $this->orderModel->findById($id);
        if (!$order) {
            $this->jsonResponse(["message" => "Order not found"], 404);
        }

        if ($order['client_id'] != $_SESSION['company_id']) {
            $this->jsonResponse(["message" => "Unauthorized"], 403);
        }

        if ($order['status'] !== 'granted') {
            $this->jsonResponse(["message" => "Order must be 'granted' before it can be confirmed as delivered"], 400);
        }

        if ($this->orderModel->updateStatus($id, 'delivered')) {
            $this->jsonResponse(["message" => "Order marked as delivered"]);
        }
        $this->jsonResponse(["message" => "Failed to update order status"], 500);
    }

    public function show($id)
    {
        AuthMiddleware::check();
        $order = $this->orderModel->findById($id);
        if ($order) {
            $this->jsonResponse($order);
        }
        $this->jsonResponse(["message" => "Order not found"], 404);
    }
}
