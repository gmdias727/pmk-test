<?php
namespace App\Model;

class Address
{
    private ?int $id;
    private int $donorId;
    private string $street;
    private int $number;
    private ?string $complement;
    private string $neighborhood;
    private string $city;
    private string $state;
    private string $zipCode;
    private string $createdAt;
    private string $updatedAt;

    public function __construct(
        ?int $id,
        int $donorId,
        string $street,
        int $number,
        ?string $complement,
        string $neighborhood,
        string $city,
        string $state,
        string $zipCode,
        string $createdAt = "",
        string $updatedAt = ""
    ) {
        $this->id = $id;
        $this->donorId = $donorId;
        $this->street = $street;
        $this->number = $number;
        $this->complement = $complement;
        $this->neighborhood = $neighborhood;
        $this->city = $city;
        $this->state = $state;
        $this->zipCode = $zipCode;
        $this->createdAt = $createdAt ?: date("Y-m-d H:i:s");
        $this->updatedAt = $updatedAt ?: date("Y-m-d H:i:s");
    }

    // Getters
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getDonorId(): int
    {
        return $this->donorId;
    }
    public function getStreet(): string
    {
        return $this->street;
    }
    public function getNumber(): int
    {
        return $this->number;
    }
    public function getComplement(): ?string
    {
        return $this->complement;
    }
    public function getNeighborhood(): string
    {
        return $this->neighborhood;
    }
    public function getCity(): string
    {
        return $this->city;
    }
    public function getState(): string
    {
        return $this->state;
    }
    public function getZipCode(): string
    {
        return $this->zipCode;
    }
    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }
    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }
}
