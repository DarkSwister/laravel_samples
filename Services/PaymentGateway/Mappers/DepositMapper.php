<?php

namespace App\Services\PaymentGateway\Mappers;

use App\Services\PaymentGateway\DTO\DepositDTO;
use Illuminate\Support\LazyCollection;

/**
 * Deposit Mapper extending AbstractMapper - DRY Principle Applied
 */
class DepositMapper extends AbstractMapper
{
    private const VALID_STATUSES = ['pending', 'completed', 'failed', 'cancelled'];

    public function toDTOCollection(LazyCollection $data): LazyCollection
    {
        return $data->map(fn (array $item) => $this->toDTO($item));
    }

    public function toDTO(array $data): DepositDTO
    {
        return new DepositDTO(
            id: $data['id'] ?? null,
            userId: $data['user_id'] ?? null,
            amount: $this->parseAmount($data['amount'] ?? 0), // ✅ DRY
            currency: $this->parseCurrency($data['currency'] ?? null), // ✅ DRY
            status: $this->parseStatus($data['status'] ?? null, self::VALID_STATUSES), // ✅ DRY
            paymentMethod: $data['payment_method'] ?? null,
            transactionId: $data['transaction_id'] ?? null,
            processedAt: $this->parseDate($data['processed_at'] ?? null), // ✅ DRY
            createdAt: $this->parseDate($data['created_at'] ?? null) // ✅ DRY
        );
    }
}
