<?php

namespace App\Services\PaymentGateway\DTO;

use Illuminate\Support\LazyCollection;

/**
 * Data Transfer Object - SOLID Single Responsibility
 * Immutable data structure for API responses
 */
readonly class PaginatedResponseDTO
{
    public function __construct(
        public LazyCollection $data,
        public ?array $meta = null
    ) {}

    /**
     * Get pagination information
     */
    public function getPagination(): array
    {
        return $this->meta ?? [];
    }

    /**
     * Check if there are more pages
     */
    public function hasMorePages(): bool
    {
        if (! $this->meta) {
            return false;
        }

        $currentPage = $this->meta['current_page'] ?? 1;
        $lastPage = $this->meta['last_page'] ?? 1;

        return $currentPage < $lastPage;
    }

    /**
     * Get total count
     */
    public function getTotal(): int
    {
        return $this->meta['total'] ?? $this->data->count();
    }

    /**
     * Convert to array for API responses
     */
    public function toArray(): array
    {
        return [
            'data' => $this->data->toArray(),
            'meta' => $this->meta,
        ];
    }
}
