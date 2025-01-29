<?php

namespace App\Repository;

use App\Config\Database;
use App\Model\Address;
use PDO;

class AddressRepository
{
    private PDO $connection;

    public function __construct(Database $database)
    {
        $this->connection = $database->getConnection();
    }

    public function create(Address $address): bool
    {
        $sql = "INSERT INTO addresses (donor_id, street, number, complement, neighborhood, city, state, zip_code)
                VALUES (:donor_id, :street, :number, :complement, :neighborhood, :city, :state, :zip_code)";

        $stmt = $this->connection->prepare($sql);

        return $stmt->execute([
            "donor_id" => $address->getDonorId(),
            "street" => $address->getStreet(),
            "number" => $address->getNumber(),
            "complement" => $address->getComplement(),
            "neighborhood" => $address->getNeighborhood(),
            "city" => $address->getCity(),
            "state" => $address->getState(),
            "zip_code" => $address->getZipCode(),
        ]);
    }

    public function read(int $id): ?Address
    {
        $sql = "SELECT * FROM addresses WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(["id" => $id]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            return null;
        }

        return new Address(
            $result["id"],
            $result["donor_id"],
            $result["street"],
            $result["number"],
            $result["complement"],
            $result["neighborhood"],
            $result["city"],
            $result["state"],
            $result["zip_code"],
            $result["created_at"],
            $result["updated_at"]
        );
    }

    public function update(Address $address): bool
    {
        $sql = "UPDATE addresses
                SET street = :street,
                    number = :number,
                    complement = :complement,
                    neighborhood = :neighborhood,
                    city = :city,
                    state = :state,
                    zip_code = :zip_code
                WHERE id = :id";

        $stmt = $this->connection->prepare($sql);

        return $stmt->execute([
            "id" => $address->getId(),
            "street" => $address->getStreet(),
            "number" => $address->getNumber(),
            "complement" => $address->getComplement(),
            "neighborhood" => $address->getNeighborhood(),
            "city" => $address->getCity(),
            "state" => $address->getState(),
            "zip_code" => $address->getZipCode(),
        ]);
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM addresses WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        return $stmt->execute(["id" => $id]);
    }

    public function deleteByDonorId(int $donorId): bool
        {
            $sql = "DELETE FROM addresses WHERE donor_id = :donor_id";
            $stmt = $this->connection->prepare($sql);
            return $stmt->execute(['donor_id' => $donorId]);
        }

    public function findByDonorId(int $donorId): array
    {
        $sql = "SELECT * FROM addresses WHERE donor_id = :donor_id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(["donor_id" => $donorId]);

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $addresses = [];

        foreach ($results as $result) {
            $addresses[] = new Address(
                $result["id"],
                $result["donor_id"],
                $result["street"],
                $result["number"],
                $result["complement"],
                $result["neighborhood"],
                $result["city"],
                $result["state"],
                $result["zip_code"],
                $result["created_at"],
                $result["updated_at"]
            );
        }

        return $addresses;
    }
}
