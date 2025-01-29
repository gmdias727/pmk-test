<?php
namespace App\Repository;

use App\Config\Database;
use App\Model\PaymentDetail;
use PDO;

class PaymentDetailRepository
{
    private PDO $connection;

    public function __construct(Database $database)
    {
        $this->connection = $database->getConnection();
    }

    public function create(PaymentDetail $paymentDetail): bool
    {
        $sql = "INSERT INTO payment_details (donor_id, payment_method, account_info, card_info)
                VALUES (:donor_id, :payment_method, :account_info, :card_info)";

        $stmt = $this->connection->prepare($sql);

        return $stmt->execute([
            "donor_id" => $paymentDetail->getDonorId(),
            "payment_method" => $paymentDetail->getPaymentMethod(),
            "account_info" => $paymentDetail->getAccountInfo()
                ? json_encode($paymentDetail->getAccountInfo())
                : null,
            "card_info" => $paymentDetail->getCardInfo()
                ? json_encode($paymentDetail->getCardInfo())
                : null,
        ]);
    }

    public function read(int $id): ?PaymentDetail
    {
        $sql = "SELECT * FROM payment_details WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(["id" => $id]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            return null;
        }

        return new PaymentDetail(
            $result["id"],
            $result["donor_id"],
            $result["payment_method"],
            $result["account_info"]
                ? json_decode($result["account_info"], true)
                : null,
            $result["card_info"]
                ? json_decode($result["card_info"], true)
                : null,
            $result["created_at"],
            $result["updated_at"]
        );
    }

    public function update(PaymentDetail $paymentDetail): bool
    {
        $sql = "UPDATE payment_details
                SET payment_method = :payment_method,
                    account_info = :account_info,
                    card_info = :card_info
                WHERE id = :id";

        $stmt = $this->connection->prepare($sql);

        return $stmt->execute([
            "id" => $paymentDetail->getId(),
            "payment_method" => $paymentDetail->getPaymentMethod(),
            "account_info" => $paymentDetail->getAccountInfo()
                ? json_encode($paymentDetail->getAccountInfo())
                : null,
            "card_info" => $paymentDetail->getCardInfo()
                ? json_encode($paymentDetail->getCardInfo())
                : null,
        ]);
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM payment_details WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        return $stmt->execute(["id" => $id]);
    }

    public function deleteByDonorId(int $donorId): bool
    {
        $sql = "DELETE FROM payment_details WHERE donor_id = :donor_id";
        $stmt = $this->connection->prepare($sql);
        return $stmt->execute(["donor_id" => $donorId]);
    }

    public function findByDonorId(int $donorId): array
    {
        $sql = "SELECT * FROM payment_details WHERE donor_id = :donor_id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(["donor_id" => $donorId]);

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $paymentDetails = [];

        foreach ($results as $result) {
            $paymentDetails[] = new PaymentDetail(
                $result["id"],
                $result["donor_id"],
                $result["payment_method"],
                $result["account_info"]
                    ? json_decode($result["account_info"], true)
                    : null,
                $result["card_info"]
                    ? json_decode($result["card_info"], true)
                    : null,
                $result["created_at"],
                $result["updated_at"]
            );
        }

        return $paymentDetails;
    }
}
