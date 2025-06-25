<?php

namespace App\Services\PaymentGateway\DTO;

use DateTimeImmutable;

/**
 * Immutable User Data Transfer Object
 * Single Responsibility: Hold user data structure
 */
readonly class UserDTO
{
    public function __construct(
        public ?int $id,
        public ?string $email,
        public ?string $name,
        public string $status,
        public ?DateTimeImmutable $createdAt,
        public ?DateTimeImmutable $updatedAt,
        public array $metadata = []
    ) {}

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'name' => $this->name,
            'status' => $this->status,
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
            'metadata' => $this->metadata,
        ];
    }
}
