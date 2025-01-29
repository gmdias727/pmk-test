<?php
namespace App\Controllers;

use App\Config\Database;
use App\Model\Address;
use App\Repository\AddressRepository;
use Exception;

class AddressController extends BaseController
{
    private AddressRepository $repository;

    public function __construct()
    {
        $database = new Database();
        $this->repository = new AddressRepository($database);
    }

    public function create(): void
    {
        try {
            if ($_SERVER["REQUEST_METHOD"] !== "POST") {
                throw new Exception("Method not allowed");
            }

            $input = $this->getJsonInput();

            $requiredFields = [
                "donor_id",
                "street",
                "number",
                "neighborhood",
                "city",
                "state",
                "zip_code"
            ];

            foreach ($requiredFields as $field) {
                if (!isset($input[$field]) || empty($input[$field])) {
                    throw new Exception("Field '{$field}' is required");
                }
            }

            $address = new Address(
                null,
                $input["donor_id"],
                $input["street"],
                $input["number"],
                $input["complement"] ?? "",
                $input["neighborhood"],
                $input["city"],
                $input["state"],
                $input["zip_code"]
            );

            if ($this->repository->create($address)) {
                $this->response["success"] = true;
                $this->response["message"] = "Address created successfully";
                $this->response["data"] = [
                    "donor_id" => $address->getDonorId(),
                    "street" => $address->getStreet(),
                    "city" => $address->getCity()
                ];
                $this->jsonResponse($this->response, 201);
            } else {
                throw new Exception("Error creating address");
            }
        } catch (Exception $e) {
            $this->response["message"] = $e->getMessage();
            $this->jsonResponse($this->response, 400);
        }
    }

    public function update(): void
    {
        try {
            if ($_SERVER["REQUEST_METHOD"] !== "PUT") {
                throw new Exception("Method not allowed");
            }

            $id = $_GET["id"] ?? null;
            if (!$id) {
                throw new Exception("Address ID is required");
            }

            $input = $this->getJsonInput();
            $existingAddress = $this->repository->read((int)$id);

            if (!$existingAddress) {
                throw new Exception("Address not found");
            }

            $updatedAddress = new Address(
                (int)$id,
                $input["donor_id"] ?? $existingAddress->getDonorId(),
                $input["street"] ?? $existingAddress->getStreet(),
                $input["number"] ?? $existingAddress->getNumber(),
                $input["complement"] ?? $existingAddress->getComplement(),
                $input["neighborhood"] ?? $existingAddress->getNeighborhood(),
                $input["city"] ?? $existingAddress->getCity(),
                $input["state"] ?? $existingAddress->getState(),
                $input["zip_code"] ?? $existingAddress->getZipCode()
            );

            if ($this->repository->update($updatedAddress)) {
                $this->response["success"] = true;
                $this->response["message"] = "Address updated successfully";
                $this->jsonResponse($this->response);
            } else {
                throw new Exception("Error updating address");
            }
        } catch (Exception $e) {
            $this->response["message"] = $e->getMessage();
            $this->jsonResponse($this->response, 400);
        }
    }

    public function delete(): void
    {
        try {
            if ($_SERVER["REQUEST_METHOD"] !== "DELETE") {
                throw new Exception("Method not allowed");
            }

            $id = $_GET["id"] ?? null;
            if (!$id) {
                throw new Exception("Address ID is required");
            }

            if ($this->repository->delete((int)$id)) {
                $this->response["success"] = true;
                $this->response["message"] = "Address deleted successfully";
                $this->jsonResponse($this->response);
            } else {
                throw new Exception("Error deleting address");
            }
        } catch (Exception $e) {
            $this->response["message"] = $e->getMessage();
            $this->jsonResponse($this->response, 400);
        }
    }

    protected function getJsonInput(): array
    {
        $input = json_decode(file_get_contents("php://input"), true);
        if ($input === null) {
            throw new Exception("Invalid JSON data provided");
        }
        return $input;
    }
}
