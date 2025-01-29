<?php
namespace App\Controllers;

use App\Config\Database;
use App\Model\Donor;
use App\Repository\AddressRepository;
use App\Repository\DonorRepository;
use App\Repository\PaymentDetailRepository;
use Exception;

class DonorController extends BaseController
{
    private DonorRepository $repository;
    private array $validIntervals = [
        "Unico",
        "Bimestral",
        "Semestral",
        "Anual",
    ];

    public function __construct()
    {
        $database = new Database();
        $this->repository = new DonorRepository($database);
    }

    public function index(): void
    {
        try {
            if ($_SERVER["REQUEST_METHOD"] !== "GET") {
                throw new Exception("Method not allowed");
            }

            $filters = [
                "search" => $_GET["search"] ?? null,
                "page" => isset($_GET["page"])
                    ? max(1, (int) $_GET["page"])
                    : 1,
                "limit" => isset($_GET["limit"])
                    ? min(100, max(1, (int) $_GET["limit"]))
                    : 10,
            ];

            $donors = $this->repository->findAll($filters);
            $total = $this->repository->count($filters);

            $totalPages = ceil($total / $filters["limit"]);

            if ($donors) {
                $donorsData = array_map(function ($donor) {
                    return [
                        "id" => $donor->getId(),
                        "name" => $donor->getName(),
                        "email" => $donor->getEmail(),
                        "cpf" => $donor->getCpf(),
                        "phone" => $donor->getPhone(),
                        "birth_date" => $donor->getBirthDate(),
                        "registration_date" => $donor->getRegistrationDate(),
                        "donation_interval" => $donor->getDonationInterval(),
                        "donation_value" => $donor->getDonationValue(),
                        "payment_method" => $donor->getPaymentMethod(),
                        "address" => $donor->getAddress(),
                    ];
                }, $donors);

                $this->response["success"] = true;
                $this->response["message"] = "Donors retrieved successfully";
                $this->response["data"] = [
                    "donors" => $donorsData,
                    "pagination" => [
                        "current_page" => $filters["page"],
                        "per_page" => $filters["limit"],
                        "total_items" => $total,
                        "total_pages" => $totalPages,
                    ],
                ];
            } else {
                $this->response["success"] = true;
                $this->response["message"] = "No donors found";
                $this->response["data"] = [
                    "donors" => [],
                    "pagination" => [
                        "current_page" => 1,
                        "per_page" => $filters["limit"],
                        "total_items" => 0,
                        "total_pages" => 0,
                    ],
                ];
            }

            $this->jsonResponse($this->response);
        } catch (Exception $e) {
            $this->response["message"] = $e->getMessage();
            $this->jsonResponse($this->response, 400);
        }
    }

    public function create(): void
    {
        try {
            if ($_SERVER["REQUEST_METHOD"] !== "POST") {
                throw new Exception("Method not allowed");
            }

            $input = $this->getJsonInput();
            $this->validateDonorInput($input);

            $donor = $this->createDonorFromInput($input);

            if ($this->repository->create($donor)) {
                $this->response["success"] = true;
                $this->response["message"] = "Donor created successfully.";
                $this->response["data"] = [
                    "name" => $donor->getName(),
                    "email" => $donor->getEmail(),
                    "registration_date" => $donor->getRegistrationDate(),
                ];
                $this->jsonResponse($this->response);
            } else {
                throw new Exception("Error creating donor.");
            }
        } catch (Exception $e) {
            $this->response["message"] = $e->getMessage();
            $this->jsonResponse($this->response, 400);
        }
    }

    public function update(): void
    {
        try {

            if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
                $this->jsonResponse([], 200);
                exit();
            }

            if ($_SERVER["REQUEST_METHOD"] !== "PUT") {
                throw new Exception("Method not allowed. Use PUT.");
            }

            $donorId = $_GET["id"] ?? null;
            if (!$donorId) {
                throw new Exception("Donor ID is required");
            }

            $input = $this->getJsonInput();

            $existingDonor = $this->repository->read((int) $donorId);
            if (!$existingDonor) {
                throw new Exception("Donor not found");
            }

            $updatedDonor = new Donor(
                $donorId,
                $input["name"] ?? $existingDonor->getName(),
                $input["email"] ?? $existingDonor->getEmail(),
                $input["cpf"] ?? $existingDonor->getCpf(),
                $input["phone"] ?? $existingDonor->getPhone(),
                $input["birth_date"] ?? $existingDonor->getBirthDate(),
                $existingDonor->getRegistrationDate(),
                $input["donation_interval"] ??
                    $existingDonor->getDonationInterval(),
                isset($input["donation_value"])
                    ? (float) $input["donation_value"]
                    : $existingDonor->getDonationValue(),
                $input["payment_method"] ?? $existingDonor->getPaymentMethod(),
                $input["account_number"] ?? $existingDonor->getAccountNumber(),
                $input["card_brand"] ?? $existingDonor->getCardBrand(),
                $input["card_first6"] ?? $existingDonor->getCardFirst6(),
                $input["card_last4"] ?? $existingDonor->getCardLast4(),
                $input["address"] ?? $existingDonor->getAddress()
            );

            if (
                $updatedDonor->getPaymentMethod() === "Débito" &&
                empty($updatedDonor->getAccountNumber())
            ) {
                throw new Exception(
                    "Account number is required for debit payment"
                );
            }

            if (
                $updatedDonor->getPaymentMethod() === "Crédito" &&
                (empty($updatedDonor->getCardBrand()) ||
                    empty($updatedDonor->getCardFirst6()) ||
                    empty($updatedDonor->getCardLast4()))
            ) {
                throw new Exception(
                    "Card information is required for credit payment"
                );
            }

            if ($this->repository->update($updatedDonor)) {
                $this->response["success"] = true;
                $this->response["message"] = "Donor updated successfully";
                $this->response["data"] = [
                    "id" => $updatedDonor->getId(),
                    "name" => $updatedDonor->getName(),
                    "email" => $updatedDonor->getEmail(),
                    "updated_at" => date("Y-m-d H:i:s"),
                ];
                $this->jsonResponse($this->response);
            } else {
                throw new Exception("Error updating donor");
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
                throw new Exception("Method not allowed. Use DELETE.");
            }

            $donorId = $_GET["id"] ?? null;
            if (!$donorId) {
                throw new Exception("Donor ID is required");
            }

            $this->repository->beginTransaction();

            try {

                $addressRepository = new AddressRepository(new Database());
                $addressRepository->deleteByDonorId((int) $donorId);

                $paymentDetailRepository = new PaymentDetailRepository(new Database());
                $paymentDetailRepository->deleteByDonorId((int) $donorId);


                if ($this->repository->delete((int) $donorId)) {
                    $this->repository->commit();
                    $this->response["success"] = true;
                    $this->response["message"] = "Donor and related records deleted successfully";
                    $this->response["data"] = [
                        "id" => $donorId,
                        "deleted_at" => date("Y-m-d H:i:s"),
                    ];
                    $this->jsonResponse($this->response);
                } else {
                    throw new Exception("Error deleting donor");
                }
            } catch (Exception $e) {
                $this->repository->rollback();
                throw $e;
            }
        } catch (Exception $e) {
            $this->response["success"] = false;
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

    private function validateDonorInput(array $input): void
    {
        if (!in_array($input["donation_interval"], $this->validIntervals)) {
            throw new Exception(
                "Invalid donation interval. Must be one of: " .
                    implode(", ", $this->validIntervals)
            );
        }

        $this->validateRequiredFields($input, [
            "name",
            "email",
            "cpf",
            "phone",
            "birth_date",
            "donation_interval",
            "donation_value",
            "payment_method",
            "address",
        ]);

        $this->validatePaymentFields($input);
    }

    private function validatePaymentFields(array $input): void
    {
        if (
            $input["payment_method"] === "Debito" &&
            !isset($input["account_number"])
        ) {
            throw new Exception("Account number is required for debit payment");
        }

        if (
            $input["payment_method"] === "Credito" &&
            (!isset($input["card_brand"]) || !isset($input["card_number"]))
        ) {
            throw new Exception(
                "Card brand and number are required for credit payment"
            );
        }
    }

    private function createDonorFromInput(array $input): Donor
    {
        $paymentMethod = $input["payment_method"];
        $accountNumber = null;
        $cardBrand = null;
        $cardFirst6 = null;
        $cardLast4 = null;

        if ($paymentMethod === "Debito") {
            $accountNumber = trim($input["account_number"]);
        }

        if ($paymentMethod === "Credito") {
            $cardBrand = trim($input["card_brand"]);
            $cardNumber = preg_replace("/\D/", "", $input["card_number"]);
            $cardFirst6 = substr($cardNumber, 0, 6);
            $cardLast4 = substr($cardNumber, -4);
        }

        return new Donor(
            null,
            trim($input["name"]),
            trim($input["email"]),
            trim($input["cpf"]),
            trim($input["phone"]),
            $input["birth_date"],
            date("Y-m-d"),
            $input["donation_interval"],
            (float) $input["donation_value"],
            $paymentMethod,
            $accountNumber,
            $cardBrand,
            $cardFirst6,
            $cardLast4,
            trim($input["address"])
        );
    }
}
