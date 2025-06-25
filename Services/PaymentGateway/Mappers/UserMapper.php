<?php

namespace App\Services\PaymentGateway\Mappers;

use App\Services\PaymentGateway\DTO\UserDTO;
use Illuminate\Support\LazyCollection;

/**
 * User Mapper extending AbstractMapper - DRY Principle Applied
 * Single Responsibility: Transform user data only
 */
class UserMapper extends AbstractMapper
{
    private const VALID_STATUSES = ['active', 'inactive', 'pending', 'suspended'];

    public function toDTOCollection(LazyCollection $data): LazyCollection
    {
        return $data->map(fn (array $item) => $this->toDTO($item));
    }

    /**
     * Transform single user array to UserDTO
     * Uses inherited utility methods - DRY principle
     */
    public function toDTO(array $data): UserDTO
    {
        return new UserDTO(
            id: $data['id'] ?? null,
            email: $data['email'] ?? null,
            name: $data['name'] ?? $data['full_name'] ?? null,
            status: $this->parseStatus($data['status'] ?? null, self::VALID_STATUSES),
            createdAt: $this->parseDate($data['created_at'] ?? null),
            updatedAt: $this->parseDate($data['updated_at'] ?? null),
            metadata: $data['metadata'] ?? []
        );
    }
}
