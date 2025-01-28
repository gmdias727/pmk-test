<?php

require_once __DIR__ . '/database/db.php';
require_once __DIR__ . '/services/DonationsService.php';
require_once __DIR__ . '/controllers/DonationsController.php';

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$controller = new DonationsController();

$result = $controller->index();
header('Content-Type: application/json');
echo json_encode($result);

try {
    switch ("$method $path") {
        case 'GET /donations':
            $result = $controller->index();
            break;

        case 'GET /donations/{id}':
            $id = // extract ID from path
            $result = $controller->show($id);
            break;

        case 'POST /donations':
            $data = json_decode(file_get_contents('php://input'), true);
            $result = $controller->store($data);
            break;

        default:
            http_response_code(404);
            $result = ['error' => 'Not Found'];
    }

    header('Content-Type: application/json');
    echo json_encode($result);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
