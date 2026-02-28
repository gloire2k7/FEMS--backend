<?php

$router = new Router();

// Test Route
$router->get('/api/test', function () {
    echo json_encode(["message" => "FEMS API is running successfully."]);
});

// Auth Routes (Public)
$router->post('/api/login', ['AuthController', 'login']);

// Users
$router->get('/api/users', ['UserController', 'index']);
$router->post('/api/users', ['UserController', 'store']); // Public for Company User, Auth for Admin
$router->get('/api/users/{id}', ['UserController', 'show']);
$router->put('/api/users/{id}', ['UserController', 'update']);
$router->delete('/api/users/{id}', ['UserController', 'destroy']);
$router->post('/api/users/change-password', ['UserController', 'changePassword']);

// Clients
$router->get('/api/clients', ['ClientController', 'index']);
$router->post('/api/clients', ['ClientController', 'store']);
$router->get('/api/clients/{id}', ['ClientController', 'show']);
$router->put('/api/clients/{id}', ['ClientController', 'update']);
$router->delete('/api/clients/{id}', ['ClientController', 'destroy']);

// Extinguishers
$router->get('/api/extinguishers', ['ExtinguisherController', 'index']);
$router->post('/api/extinguishers', ['ExtinguisherController', 'store']);
$router->get('/api/extinguishers/{id}', ['ExtinguisherController', 'show']);
$router->put('/api/extinguishers/{id}', ['ExtinguisherController', 'update']);
$router->delete('/api/extinguishers/{id}', ['ExtinguisherController', 'destroy']);

// Inspections
$router->get('/api/inspections', ['InspectionController', 'index']);
$router->post('/api/inspections', ['InspectionController', 'store']);

$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];
$router->dispatch($method, $uri);
