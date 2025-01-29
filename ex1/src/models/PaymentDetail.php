<?php
namespace App\Model;

class PaymentDetail
{
    private ?int $id;
    private int $donorId;
    private string $paymentMethod;
    private ?array $accountInfo;
    private ?array $cardInfo;
    private string $createdAt;
    private string $updatedAt;

    public function __construct(
        ?int $id,
        int $donorId,
        string $paymentMethod,
        ?array $accountInfo,
        ?array $cardInfo,
        string $createdAt = "",
        string $updatedAt = ""
    ) {
        $this->id = $id;
        $this->donorId = $donorId;
        $this->paymentMethod = $paymentMethod;
        $this->accountInfo = $accountInfo;
        $this->cardInfo = $cardInfo;
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
    public function getPaymentMethod(): string
    {
        return $this->paymentMethod;
    }
    public function getAccountInfo(): ?array
    {
        return $this->accountInfo;
    }
    public function getCardInfo(): ?array
    {
        return $this->cardInfo;
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
