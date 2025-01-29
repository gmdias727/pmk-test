<?php
namespace App\Core;

use App\Controllers\DonorController;
use App\Controllers\PaymentDetailController;
use App\Controllers\AddressController;

require_once __DIR__ . "/config/Database.php";
require_once __DIR__ . "/models/Donor.php";
require_once __DIR__ . "/models/Address.php";
require_once __DIR__ . "/models/PaymentDetail.php";
require_once __DIR__ . "/repositories/DonorRepository.php";
require_once __DIR__ . "/repositories/AddressRepository.php";
require_once __DIR__ . "/repositories/PaymentDetailRepository.php";
require_once __DIR__ . "/Controllers/BaseController.php";
require_once __DIR__ . "/Controllers/DonorController.php";
require_once __DIR__ . "/Controllers/PaymentDetailController.php";
require_once __DIR__ . "/Controllers/AddressController.php";
require_once __DIR__ . "/routes/routes.php";

class App
{
    private Router $router;

    public function __construct()
    {
        $this->router = new Router();
        $this->setupRoutes();
    }

    private function setupRoutes(): void
    {
        $this->router->addRoute("GET", "/", function () {
            header("Content-Type: application/json");
            return json_encode([
                "status" => "success",
                "message" => "API is running",
            ]);
        });

        $this->router->addRoute("GET", "/donors", function () {
            $controller = new DonorController();
            return $controller->index();
        });

        $this->router->addRoute("POST", "/donors", function () {
            $controller = new DonorController();
            return $controller->create();
        });

        $this->router->addRoute("PUT", "/donors/{id}", function ($id) {
            $controller = new DonorController();
            $_GET["id"] = $id;
            return $controller->update();
        });

        $this->router->addRoute("DELETE", "/donors/{id}", function ($id) {
            $controller = new DonorController();
            $_GET["id"] = $id;
            return $controller->delete();
        });

        $this->router->addRoute("POST", "/payment-details", function () {
            $controller = new PaymentDetailController();
            return $controller->create();
        });

        $this->router->addRoute("PUT", "/payment-details/{id}", function ($id) {
            $controller = new PaymentDetailController();
            $_GET["id"] = $id;
            return $controller->update();
        });

        $this->router->addRoute("DELETE", "/payment-details/{id}", function (
            $id
        ) {
            $controller = new PaymentDetailController();
            $_GET["id"] = $id;
            return $controller->delete();
        });

        $this->router->addRoute("POST", "/addresses", function () {
            $controller = new AddressController();
            return $controller->create();
        });

        $this->router->addRoute("PUT", "/addresses/{id}", function ($id) {
            $controller = new AddressController();
            $_GET["id"] = $id;
            return $controller->update();
        });

        $this->router->addRoute("DELETE", "/addresses/{id}", function ($id) {
            $controller = new AddressController();
            $_GET["id"] = $id;
            return $controller->delete();
        });
    }

    public function run(): void
    {
        $method = $_SERVER["REQUEST_METHOD"];
        $uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

        $uri = rtrim($uri, "/");

        if (empty($uri)) {
            $uri = "/";
        }

        echo $this->router->dispatch($method, $uri);
    }
}
