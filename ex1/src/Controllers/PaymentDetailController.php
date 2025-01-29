<?php
namespace App\Controllers;

use App\Config\Database;
use App\Model\PaymentDetail;
use App\Repository\PaymentDetailRepository;
use Exception;

class PaymentDetailController extends BaseController {
    private PaymentDetailRepository $repository;
    private array $validPaymentMethods = ['Débito', 'Crédito'];

    public function __construct() {
        $database = new Database();
        $this->repository = new PaymentDetailRepository($database);
    }

    public function create(): void {
        try {
            if ($_SERVER["REQUEST_METHOD"] !== "POST") {
                throw new Exception("Method not allowed");
            }

            $input = $this->getJsonInput();
            $this->validatePaymentDetailInput($input);

            $paymentDetail = $this->createPaymentDetailFromInput($input);

            if ($this->repository->create($paymentDetail)) {
                $this->response["success"] = true;
                $this->response["message"] = "Payment detail created successfully.";
                $this->response["data"] = [
                    "id" => $paymentDetail->getId(),
                    "donor_id" => $paymentDetail->getDonorId(),
                    "payment_method" => $paymentDetail->getPaymentMethod()
                ];
                $this->jsonResponse($this->response);
            } else {
                throw new Exception("Error creating payment detail.");
            }
        } catch (Exception $e) {
            $this->response["message"] = $e->getMessage();
            $this->jsonResponse($this->response, 400);
        }
    }

    public function update(): void {
        try {
            if ($_SERVER["REQUEST_METHOD"] !== "PUT") {
                throw new Exception("Method not allowed");
            }

            $id = $_GET["id"] ?? null;
            if (!$id) {
                throw new Exception("Payment detail ID is required");
            }

            $input = $this->getJsonInput();
            $existing = $this->repository->read((int)$id);

            if (!$existing) {
                throw new Exception("Payment detail not found");
            }

            $updated = $this->updatePaymentDetailFromInput($existing, $input);

            if ($this->repository->update($updated)) {
                $this->response["success"] = true;
                $this->response["message"] = "Payment detail updated successfully";
                $this->jsonResponse($this->response);
            } else {
                throw new Exception("Error updating payment detail");
            }
        } catch (Exception $e) {
            $this->response["message"] = $e->getMessage();
            $this->jsonResponse($this->response, 400);
        }
    }

    public function delete(): void {
        try {
            if ($_SERVER["REQUEST_METHOD"] !== "DELETE") {
                throw new Exception("Method not allowed");
            }

            $id = $_GET["id"] ?? null;
            if (!$id) {
                throw new Exception("Payment detail ID is required");
            }

            if ($this->repository->delete((int)$id)) {
                $this->response["success"] = true;
                $this->response["message"] = "Payment detail deleted successfully";
                $this->jsonResponse($this->response);
            } else {
                throw new Exception("Error deleting payment detail");
            }
        } catch (Exception $e) {
            $this->response["message"] = $e->getMessage();
            $this->jsonResponse($this->response, 400);
        }
    }

    private function validatePaymentDetailInput(array $input): void {
        $requiredFields = ["donor_id", "payment_method"];
        foreach ($requiredFields as $field) {
            if (!isset($input[$field])) {
                throw new Exception("Field '$field' is required");
            }
        }

        if (!in_array($input["payment_method"], $this->validPaymentMethods)) {
            throw new Exception("Invalid payment method");
        }

        if ($input["payment_method"] === "Débito" && empty($input["account_info"])) {
            throw new Exception("Account info is required for debit payments");
        }

        if ($input["payment_method"] === "Crédito" && empty($input["card_info"])) {
            throw new Exception("Card info is required for credit payments");
        }
    }

    private function createPaymentDetailFromInput(array $input): PaymentDetail {
        return new PaymentDetail(
            null,
            (int)$input["donor_id"],
            $input["payment_method"],
            $input["payment_method"] === "Débito" ? $input["account_info"] : null,
            $input["payment_method"] === "Crédito" ? $input["card_info"] : null
        );
    }

    private function updatePaymentDetailFromInput(PaymentDetail $existing, array $input): PaymentDetail {
        return new PaymentDetail(
            $existing->getId(),
            $input["donor_id"] ?? $existing->getDonorId(),
            $input["payment_method"] ?? $existing->getPaymentMethod(),
            isset($input["account_info"]) ? $input["account_info"] : $existing->getAccountInfo(),
            isset($input["card_info"]) ? $input["card_info"] : $existing->getCardInfo()
        );
    }
}
