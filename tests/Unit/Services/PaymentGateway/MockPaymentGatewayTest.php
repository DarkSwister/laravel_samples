<?php

namespace Tests\Unit\Services\PaymentGateway;

use App\Services\PaymentGateway\DTO\DepositDTO;
use App\Services\PaymentGateway\DTO\UserDTO;
use App\Services\PaymentGateway\MockPaymentGateway;
use PHPUnit\Framework\TestCase;

class MockPaymentGatewayTest extends TestCase
{
    private MockPaymentGateway $gateway;

    protected function setUp(): void
    {
        $this->gateway = new MockPaymentGateway;
    }

    public function test_sync_users_returns_paginated_response_with_mock_data(): void
    {
        $result = $this->gateway->syncUsers();

        $this->assertEquals(['total' => 1, 'current_page' => 1, 'last_page' => 1], $result->meta);

        $users = $result->data->toArray();
        $this->assertCount(1, $users);

        $user = $users[0];
        $this->assertInstanceOf(UserDTO::class, $user);
        $this->assertEquals(1, $user->id);
        $this->assertEquals('test@example.com', $user->email);
        $this->assertEquals('Test User', $user->name);
        $this->assertEquals('active', $user->status);
    }

    public function test_sync_users_with_filters_still_returns_same_data(): void
    {
        $filters = ['status' => 'active', 'email' => 'test@example.com'];

        $result = $this->gateway->syncUsers($filters);

        $this->assertEquals(['total' => 1, 'current_page' => 1, 'last_page' => 1], $result->meta);

        $users = $result->data->toArray();
        $this->assertCount(1, $users);
    }

    public function test_sync_deposits_returns_paginated_response_with_mock_data(): void
    {
        $result = $this->gateway->syncDeposits();

        $this->assertEquals(['total' => 1, 'current_page' => 1, 'last_page' => 1], $result->meta);

        $deposits = $result->data->toArray();
        $this->assertCount(1, $deposits);

        $deposit = $deposits[0];
        $this->assertInstanceOf(DepositDTO::class, $deposit);
        $this->assertEquals(1, $deposit->id);
        $this->assertEquals(1, $deposit->userId);
        $this->assertEquals(100.00, $deposit->amount);
        $this->assertEquals('USD', $deposit->currency);
        $this->assertEquals('completed', $deposit->status);
        $this->assertEquals('credit_card', $deposit->paymentMethod);
        $this->assertEquals('txn_123', $deposit->transactionId);
    }

    public function test_sync_withdrawals_returns_empty_paginated_response(): void
    {
        $result = $this->gateway->syncWithdrawals();

        $this->assertEquals(['total' => 0, 'current_page' => 1, 'last_page' => 1], $result->meta);
        $this->assertCount(0, $result->data->toArray());
    }

    public function test_sync_bonuses_returns_empty_paginated_response(): void
    {
        $result = $this->gateway->syncBonuses();

        $this->assertEquals(['total' => 0, 'current_page' => 1, 'last_page' => 1], $result->meta);
        $this->assertCount(0, $result->data->toArray());
    }

    public function test_sync_transactions_returns_empty_paginated_response(): void
    {
        $result = $this->gateway->syncTransactions();

        $this->assertEquals(['total' => 0, 'current_page' => 1, 'last_page' => 1], $result->meta);
        $this->assertCount(0, $result->data->toArray());
    }
}
