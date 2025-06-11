<?php

namespace App\Services\PaymentGateway;

use App\Services\PaymentGateway\Contracts\PaymentGatewayInterface;
use App\Services\PaymentGateway\DTO\PaginatedResponseDTO;
use Illuminate\Support\Manager;

/**
 * Laravel Manager Pattern - SOLID Laravel Way
 *
 * Demonstrates:
 * - Laravel's Manager pattern for multiple drivers
 * - Service Container integration
 * - Configuration-based driver selection
 * - Extensible architecture
 */
class PaymentGatewayManager extends Manager implements PaymentGatewayInterface
{
    /**
     * Get the default driver name - Laravel Manager pattern
     */
    public function getDefaultDriver(): string
    {
        return $this->config->get('payment-gateway.default', 'api');
    }

    /**
     * Create API driver instance - Laravel naming convention
     */
    public function createApiDriver(): PaymentGatewayInterface
    {
        $config = $this->config->get('payment-gateway.drivers.api');

        return $this->container->make(ApiPaymentGateway::class, [
            'config' => $config,
        ]);
    }

    /**
     * Create mock driver for testing - Laravel testing patterns
     */
    public function createMockDriver(): PaymentGatewayInterface
    {
        return $this->container->make(MockPaymentGateway::class);
    }

    // Delegate interface methods to current driver
    public function syncUsers(array $filters = []): PaginatedResponseDTO
    {
        return $this->driver()->syncUsers($filters);
    }

    public function syncDeposits(array $filters = []): PaginatedResponseDTO
    {
        return $this->driver()->syncDeposits($filters);
    }

    public function syncWithdrawals(array $filters = []): PaginatedResponseDTO
    {
        return $this->driver()->syncWithdrawals($filters);
    }

    public function syncBonuses(array $filters = []): PaginatedResponseDTO
    {
        return $this->driver()->syncBonuses($filters);
    }

    public function syncTransactions(array $filters = []): PaginatedResponseDTO
    {
        return $this->driver()->syncTransactions($filters);
    }
}
