<?php

namespace App\Services\PaymentGateway\Contracts;

use App\Services\PaymentGateway\DTO\PaginatedResponseDTO;

/**
 * Interface Segregation Principle - Laravel Service Contract
 */
interface PaymentGatewayInterface
{
    public function syncUsers(array $filters = []): PaginatedResponseDTO;

    public function syncDeposits(array $filters = []): PaginatedResponseDTO;

    public function syncWithdrawals(array $filters = []): PaginatedResponseDTO;

    public function syncBonuses(array $filters = []): PaginatedResponseDTO;

    public function syncTransactions(array $filters = []): PaginatedResponseDTO;
}
