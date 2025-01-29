<?php

namespace App\Controllers;

class ApiController
{
    private string $method;
    private string $path;
    private array $response;

    public function __construct()
    {
        $this->method = $_SERVER["REQUEST_METHOD"];
        $this->path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
        $this->setHeaders();
    }

    private function setHeaders(): void
    {
        header("Content-Type: application/json");
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
        header("Access-Control-Allow-Headers: Content-Type");
    }

    public function handleRequest(): string
    {
        $this->response = [
            "available_endpoints" => [
                "/donors" => [
                    "POST" => "Create a new donor",
                    "GET" => "Read donor information",
                    "PUT" => "Update donor information",
                    "DELETE" => "Delete a donor",
                ],
                "/donors/{id}" => [
                    "GET" => "Get specific donor",
                    "PUT" => "Update specific donor",
                    "DELETE" => "Delete specific donor",
                ],
            ],
            "method" => $this->method,
            "path" => $this->path,
            "message" => "Welcome to Donors API",
        ];

        return json_encode($this->response);
    }

    public function createDonor(): string
    {
        return json_encode(["message" => "Donor created"]);
    }

    public function readDonor(int $id): string
    {
        return json_encode(["message" => "Donor details"]);
    }

    public function updateDonor(int $id): string
    {
        return json_encode(["message" => "Donor updated"]);
    }

    public function deleteDonor(int $id): string
    {
        return json_encode(["message" => "Donor deleted"]);
    }
}
