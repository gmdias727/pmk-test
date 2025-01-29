<?php
namespace App\Controllers;

abstract class BaseController
{
    protected array $response = [
        "success" => false,
        "message" => "",
        "data" => null,
    ];

    protected function jsonResponse(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header("Content-Type: application/json");
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type");

        echo json_encode($data);
    }

    protected function validateRequiredFields(
        array $input,
        array $requiredFields
    ): void {
        foreach ($requiredFields as $field) {
            if (!isset($input[$field]) || empty($input[$field])) {
                throw new \Exception("Field '$field' is required");
            }
        }
    }

    protected function getJsonInput(): array
    {
        $input = json_decode(file_get_contents("php://input"), true);
        if ($input === null) {
            throw new \Exception("Invalid JSON data provided");
        }
        return $input;
    }
}
