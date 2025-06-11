<?php

namespace App\Services\PaymentGateway\Pipes;

use Illuminate\Contracts\Cache\Repository as CacheRepository;

/**
 * Laravel Pipeline Pattern - Cache Filter Processing
 * Single Responsibility: Handle filter caching logic
 */
readonly class CacheFilters
{
    public function __construct(
        private CacheRepository $cache
    ) {}

    /**
     * Cache processed filters to avoid reprocessing
     * Demonstrates: Performance optimization, Laravel caching
     */
    public function handle(array $filters, \Closure $next): mixed
    {
        $cacheKey = $this->generateCacheKey($filters);

        // Store original filters for potential debugging
        $this->cache->put(
            "payment_gateway:filters:{$cacheKey}",
            $filters,
            config('payment-gateway.cache.ttl', 300)
        );

        return $next($filters);
    }

    private function generateCacheKey(array $filters): string
    {
        return md5(serialize($filters));
    }
}
