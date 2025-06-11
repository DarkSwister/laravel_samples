<?php

namespace App\Services\PaymentGateway;

use App\Services\PaymentGateway\Contracts\PaymentGatewayInterface;
use App\Services\PaymentGateway\DTO\DepositDTO;
use App\Services\PaymentGateway\DTO\PaginatedResponseDTO;
use App\Services\PaymentGateway\DTO\UserDTO;
use Illuminate\Support\LazyCollection;

/**
 * Mock Implementation for Testing
 * Liskov Substitution Principle: Can replace ApiPaymentGateway
 */
class MockPaymentGateway implements PaymentGatewayInterface
{
    public function syncUsers(array $filters = []): PaginatedResponseDTO
    {
        $users = LazyCollection::make([
            new UserDTO(
                id: 1,
                email: 'test@example.com',
                name: 'Test User',
                status: 'active',
                createdAt: new \DateTimeImmutable,
                updatedAt: new \DateTimeImmutable
            ),
        ]);

        return new PaginatedResponseDTO(
            data: $users,
            meta: ['total' => 1, 'current_page' => 1, 'last_page' => 1]
        );
    }

    public function syncDeposits(array $filters = []): PaginatedResponseDTO
    {
        $deposits = LazyCollection::make([
            new DepositDTO(
                id: 1,
                userId: 1,
                amount: 100.00,
                currency: 'USD',
                status: 'completed',
                paymentMethod: 'credit_card',
                transactionId: 'txn_123',
                processedAt: new \DateTimeImmutable,
                createdAt: new \DateTimeImmutable
            ),
        ]);

        return new PaginatedResponseDTO(
            data: $deposits,
            meta: ['total' => 1, 'current_page' => 1, 'last_page' => 1]
        );
    }

    public function syncWithdrawals(array $filters = []): PaginatedResponseDTO
    {
        return new PaginatedResponseDTO(
            data: LazyCollection::empty(),
            meta: ['total' => 0, 'current_page' => 1, 'last_page' => 1]
        );
    }

    public function syncBonuses(array $filters = []): PaginatedResponseDTO
    {
        return new PaginatedResponseDTO(
            data: LazyCollection::empty(),
            meta: ['total' => 0, 'current_page' => 1, 'last_page' => 1]
        );
    }

    public function syncTransactions(array $filters = []): PaginatedResponseDTO
    {
        return new PaginatedResponseDTO(
            data: LazyCollection::empty(),
            meta: ['total' => 0, 'current_page' => 1, 'last_page' => 1]
        );
    }
}
