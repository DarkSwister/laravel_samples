<?php

namespace App\Services\PaymentGateway\Mappers;

use App\Services\PaymentGateway\Contracts\DataMapperInterface;
use DateTimeImmutable;
use Illuminate\Support\Carbon;

/**
 * Abstract Base Mapper - DRY Principle
 * Single Responsibility: Provide common mapping utilities
 */
abstract class AbstractMapper implements DataMapperInterface
{
    /**
     * Parse date string to DateTimeImmutable
     * Single source of truth for date parsing logic
     */
    protected function parseDate(?string $date): ?DateTimeImmutable
    {
        if (! $date) {
            return null;
        }

        try {
            // Using Laravel's Carbon for better date handling
            return Carbon::parse($date)->toDateTimeImmutable();
        } catch (\Exception) {
            return null;
        }
    }

    /**
     * Parse amount with validation
     * Common utility for financial data
     */
    protected function parseAmount(mixed $amount): float
    {
        return (float) ($amount ?? 0);
    }

    /**
     * Parse currency with default fallback
     */
    protected function parseCurrency(?string $currency): string
    {
        return strtoupper($currency ?? config('payment-gateway.default_currency', 'USD'));
    }

    /**
     * Parse status with validation
     */
    protected function parseStatus(?string $status, array $validStatuses = []): string
    {
        if (! $status) {
            return 'unknown';
        }

        if (! empty($validStatuses) && ! in_array($status, $validStatuses, true)) {
            return 'unknown';
        }

        return $status;
    }
}
