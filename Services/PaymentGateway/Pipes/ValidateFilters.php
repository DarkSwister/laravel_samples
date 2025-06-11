<?php

namespace App\Services\PaymentGateway\Pipes;

use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Validation\ValidationException;

/**
 * Laravel Validation + Pipeline Pattern
 * Single Responsibility: Validate filters only
 */
class ValidateFilters
{
    public function __construct(
        private readonly ValidationFactory $validator
    ) {}

    public function handle(array $filters, \Closure $next): mixed
    {
        $validator = $this->validator->make($filters, [
            'page' => 'integer|min:1',
            'per_page' => 'integer|min:1|max:1000',
            'updated_from' => 'date',
            'updated_to' => 'date|after_or_equal:updated_from',
            'created_from' => 'date',
            'created_to' => 'date|after_or_equal:created_from',
            'status' => 'string|in:active,inactive,pending',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $next($validator->validated());
    }
}
