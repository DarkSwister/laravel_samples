<?php

namespace App\Services\PaymentGateway\Mappers;

use App\Services\PaymentGateway\DTO\DepositDTO;
use Illuminate\Support\LazyCollection;

/**
 * Deposit Mapper extending AbstractMapper - DRY Principle Applied
 */
class DepositMapper extends AbstractMapper
{
    private const array VALID_STATUSES = ['pending', 'completed', 'failed', 'cancelled'];

    public function toDTOCollection(LazyCollection $data): LazyCollection
    {
        return $data->map(fn (array $item) => $this->toDTO($item));
    }

    public function toDTO(array $data): DepositDTO
    {
        return new DepositDTO(
            id: $data['id'] ?? null,
            userId: $data['user_id'] ?? null,
            amount: $this->parseAmount($data['amount'] ?? 0),
            currency: $this->parseCurrency($data['currency'] ?? null),
            status: $this->parseStatus($data['status'] ?? null, self::VALID_STATUSES),
            paymentMethod: $data['payment_method'] ?? null,
            transactionId: $data['transaction_id'] ?? null,
            processedAt: $this->parseDate($data['processed_at'] ?? null),
            createdAt: $this->parseDate($data['created_at'] ?? null)
        );
    }
}
