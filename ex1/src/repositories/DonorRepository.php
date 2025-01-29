<?php
namespace App\Repository;

use App\Config\Database;
use App\Model\Donor;
use PDO;

class DonorRepository
{
    private PDO $connection;

    public function __construct(Database $database)
    {
        $this->connection = $database->getConnection();
    }

    public function beginTransaction(): void
    {
        $this->connection->beginTransaction();
    }

    public function commit(): void
    {
        $this->connection->commit();
    }

    public function rollback(): void
    {
        $this->connection->rollBack();
    }

    public function create(Donor $donor): bool
    {
        $sql = "INSERT INTO donors (name, email, cpf, phone, birth_date, registration_date,
                donation_interval, donation_value, payment_method, account_number,
                card_brand, card_first6, card_last4, address)
                VALUES (:name, :email, :cpf, :phone, :birth_date, :registration_date,
                :donation_interval, :donation_value, :payment_method, :account_number,
                :card_brand, :card_first6, :card_last4, :address)";

        $stmt = $this->connection->prepare($sql);

        return $stmt->execute([
            "name" => $donor->getName(),
            "email" => $donor->getEmail(),
            "cpf" => $donor->getCpf(),
            "phone" => $donor->getPhone(),
            "birth_date" => $donor->getBirthDate(),
            "registration_date" => $donor->getRegistrationDate(),
            "donation_interval" => $donor->getDonationInterval(),
            "donation_value" => $donor->getDonationValue(),
            "payment_method" => $donor->getPaymentMethod(),
            "account_number" => $donor->getAccountNumber(),
            "card_brand" => $donor->getCardBrand(),
            "card_first6" => $donor->getCardFirst6(),
            "card_last4" => $donor->getCardLast4(),
            "address" => $donor->getAddress(),
        ]);
    }

    public function read(int $id): ?Donor
    {
        $sql = "SELECT * FROM donors WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(["id" => $id]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            return null;
        }

        return new Donor(
            $result["id"],
            $result["name"],
            $result["email"],
            $result["cpf"],
            $result["phone"],
            $result["birth_date"],
            $result["registration_date"],
            $result["donation_interval"],
            (float) $result["donation_value"],
            $result["payment_method"],
            $result["account_number"],
            $result["card_brand"],
            $result["card_first6"],
            $result["card_last4"],
            $result["address"]
        );
    }

    public function update(Donor $donor): bool
    {
        $sql = "UPDATE donors SET
                name = :name,
                email = :email,
                cpf = :cpf,
                phone = :phone,
                birth_date = :birth_date,
                donation_interval = :donation_interval,
                donation_value = :donation_value,
                payment_method = :payment_method,
                account_number = :account_number,
                card_brand = :card_brand,
                card_first6 = :card_first6,
                card_last4 = :card_last4,
                address = :address
                WHERE id = :id";

        $stmt = $this->connection->prepare($sql);

        return $stmt->execute([
            "id" => $donor->getId(),
            "name" => $donor->getName(),
            "email" => $donor->getEmail(),
            "cpf" => $donor->getCpf(),
            "phone" => $donor->getPhone(),
            "birth_date" => $donor->getBirthDate(),
            "donation_interval" => $donor->getDonationInterval(),
            "donation_value" => $donor->getDonationValue(),
            "payment_method" => $donor->getPaymentMethod(),
            "account_number" => $donor->getAccountNumber(),
            "card_brand" => $donor->getCardBrand(),
            "card_first6" => $donor->getCardFirst6(),
            "card_last4" => $donor->getCardLast4(),
            "address" => $donor->getAddress(),
        ]);
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM donors WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        return $stmt->execute(["id" => $id]);
    }

    public function findAll(): array
    {
        $sql = "SELECT * FROM donors";
        $stmt = $this->connection->query($sql);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $donors = [];
        foreach ($results as $result) {
            $donors[] = new Donor(
                $result["id"],
                $result["name"],
                $result["email"],
                $result["cpf"],
                $result["phone"],
                $result["birth_date"],
                $result["registration_date"],
                $result["donation_interval"],
                (float) $result["donation_value"],
                $result["payment_method"],
                $result["account_number"],
                $result["card_brand"],
                $result["card_first6"],
                $result["card_last4"],
                $result["address"]
            );
        }

        return $donors;
    }

    public function count(array $filters = []): int
    {
        $sql = "SELECT COUNT(*) FROM donors WHERE 1=1";
        $params = [];

        if (!empty($filters["search"])) {
            $sql .=
                " AND (name LIKE :search OR email LIKE :search OR cpf LIKE :search)";
            $params["search"] = "%{$filters["search"]}%";
        }

        $stmt = $this->connection->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }
}
