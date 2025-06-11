<?php

namespace App\Services\PaymentGateway\Pipes;

use Illuminate\Support\Carbon;
use Psr\Log\LoggerInterface;

/**
 * Laravel Pipeline Pattern - SOLID Single Responsibility
 * Each pipe has one responsibility
 */
class FormatDateFilters
{
    private const DATE_FIELDS = ['updated_from', 'updated_to', 'created_from', 'created_to'];

    public function __construct(
        private readonly LoggerInterface $logger
    ) {}

    /**
     * Laravel Pipeline handle method
     * Single Responsibility: Format date filters only
     */
    public function handle(array $filters, \Closure $next): mixed
    {
        foreach (self::DATE_FIELDS as $field) {
            if (isset($filters[$field])) {
                $filters[$field] = $this->formatDate($filters[$field], $field);
            }
        }

        return $next(array_filter($filters));
    }

    private function formatDate(string $date, string $field): ?string
    {
        try {
            return Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            $this->logger->warning('Invalid date in payment gateway filter', [
                'field' => $field,
                'value' => $date,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }
}
