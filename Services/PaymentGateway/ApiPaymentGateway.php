<?php

namespace App\Services\PaymentGateway;

use App\Services\PaymentGateway\Contracts\PaymentGatewayInterface;
use App\Services\PaymentGateway\DTO\PaginatedResponseDTO;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Events\Dispatcher as EventDispatcher;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Pipeline;
use Psr\Log\LoggerInterface;

/**
 * SOLID Laravel Implementation
 */
class ApiPaymentGateway implements PaymentGatewayInterface
{
    private PendingRequest $httpClient;

    public function __construct(
        private readonly array $config,
        private readonly CacheRepository $cache,
        private readonly EventDispatcher $events,
        private readonly LoggerInterface $logger
    ) {
        $this->buildHttpClient();
        $this->logger->info('Payment Gateway initialized', [
            'driver' => 'api',
            'base_url' => $this->config['base_url'] ?? 'not_configured',
        ]);
    }

    /**
     * Single Responsibility: HTTP client configuration
     * Uses Laravel's HTTP client factory
     */
    private function buildHttpClient(): void
    {
        $this->httpClient = Http::baseUrl($this->config['base_url'])
            ->withHeaders([
                'Authorization' => 'Bearer '.$this->config['api_key'],
                'Accept' => 'application/json',
                'User-Agent' => config('app.name', 'Laravel').'/API-Client',
            ])
            ->timeout($this->config['timeout'] ?? 30)
            ->retry(3, 1000);
    }

    /**
     * Laravel Pipeline Pattern + Event System
     * Open/Closed: Extensible via pipeline pipes and events
     */
    public function syncUsers(array $filters = []): PaginatedResponseDTO
    {
        return Pipeline::send($filters)
            ->through($this->getFilterPipes())
            ->then(fn ($processedFilters) => $this->performSync(
                endpoint: '/api/v1/users',
                filters: $processedFilters,
                mapper: 'user',
                event: 'users.synced'
            ));
    }

    public function syncDeposits(array $filters = []): PaginatedResponseDTO
    {
        return Pipeline::send($filters)
            ->through($this->getFilterPipes())
            ->then(fn ($processedFilters) => $this->performSync(
                endpoint: '/api/v1/transactions/deposits',
                filters: $processedFilters,
                mapper: 'deposit',
                event: 'deposits.synced'
            ));
    }

    public function syncWithdrawals(array $filters = []): PaginatedResponseDTO
    {
        return Pipeline::send($filters)
            ->through($this->getFilterPipes())
            ->then(fn ($processedFilters) => $this->performSync(
                endpoint: '/api/v1/transactions/withdrawals',
                filters: $processedFilters,
                mapper: 'withdrawal',
                event: 'withdrawals.synced'
            ));
    }

    public function syncBonuses(array $filters = []): PaginatedResponseDTO
    {
        return Pipeline::send($filters)
            ->through($this->getFilterPipes())
            ->then(fn ($processedFilters) => $this->performSync(
                endpoint: '/api/v1/bonuses',
                filters: $processedFilters,
                mapper: 'bonus',
                event: 'bonuses.synced'
            ));
    }

    public function syncTransactions(array $filters = []): PaginatedResponseDTO
    {
        return Pipeline::send($filters)
            ->through($this->getFilterPipes())
            ->then(fn ($processedFilters) => $this->performSync(
                endpoint: '/api/v1/transactions',
                filters: $processedFilters,
                mapper: 'transaction',
                event: 'transactions.synced'
            ));
    }

    /**
     * DRY Principle + Laravel Service Container
     * Single Responsibility: Orchestrates sync operation
     */
    private function performSync(string $endpoint, array $filters, string $mapper, string $event): PaginatedResponseDTO
    {
        $cacheKey = $this->getCacheKey($endpoint, $filters);

        return $this->cache->remember($cacheKey, 300, function () use ($endpoint, $filters, $mapper, $event) {
            $response = $this->makeRequest('GET', $endpoint, $filters);

            $mapperInstance = app("payment-gateway.mappers.{$mapper}");
            $data = $mapperInstance->toDTOCollection($response->collect('data')->lazy());

            $result = new PaginatedResponseDTO(
                data: $data,
                meta: $response->json('meta')
            );

            // Laravel Event System - Open/Closed Principle
            $this->events->dispatch("payment-gateway.{$event}", [$result, $filters]);

            return $result;
        });
    }

    private function makeRequest(string $method, string $endpoint, array $data = []): Response
    {
        return $this->httpClient->throw()->{$method}($endpoint, $data);
    }

    /**
     * Laravel Configuration Pattern
     * Open/Closed: Filter pipes configurable
     */
    private function getFilterPipes(string $syncType = 'default'): array
    {
        return config("payment-gateway.filter_pipes.{$syncType}", [
            \App\Services\PaymentGateway\Pipes\ValidateFilters::class,
            \App\Services\PaymentGateway\Pipes\FormatDateFilters::class,
            \App\Services\PaymentGateway\Pipes\CacheFilters::class,
        ]);
    }

    /**
     * Laravel Cache Key Generation
     */
    private function getCacheKey(string $endpoint, array $filters): string
    {
        return 'payment_gateway:'.md5($endpoint.serialize($filters));
    }
}
