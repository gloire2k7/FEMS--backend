<?php

class ReportController extends Controller
{
    public function index()
    {
        AuthMiddleware::hasRole(['Super Admin', 'Admin']);
        $this->jsonResponse(["message" => "Reports endpoint is ready for implementation with PhpSpreadsheet and TCPDF."]);
    }
}
