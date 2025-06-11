<?php

namespace App\Services\PaymentGateway\DTO;

/**
 * Immutable Deposit Data Transfer Object
 */
readonly class DepositDTO
{
    public function __construct(
        public ?int $id,
        public ?int $userId,
        public float $amount,
        public string $currency,
        public string $status,
        public ?string $paymentMethod,
        public ?string $transactionId,
        public ?\DateTimeImmutable $processedAt,
        public ?\DateTimeImmutable $createdAt
    ) {}

    public function isProcessed(): bool
    {
        return $this->status === 'completed';
    }

    public function getFormattedAmount(): string
    {
        return number_format($this->amount, 2).' '.$this->currency;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->userId,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'status' => $this->status,
            'payment_method' => $this->paymentMethod,
            'transaction_id' => $this->transactionId,
            'processed_at' => $this->processedAt?->format('Y-m-d H:i:s'),
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
        ];
    }
}
