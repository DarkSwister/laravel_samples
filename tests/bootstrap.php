<?php

require_once __DIR__.'/../vendor/autoload.php';

// Set up any global test configuration here
date_default_timezone_set('UTC');

// Mock Laravel's app() helper for testing
if (! function_exists('app')) {
    function app($abstract = null, array $parameters = [])
    {
        static $container = [];

        if (is_null($abstract)) {
            return $container;
        }

        if (isset($container[$abstract])) {
            return $container[$abstract];
        }

        // Simple factory for mappers
        if (str_contains($abstract, 'mapper')) {
            return new class
            {
                public function toDTOCollection($data)
                {
                    return $data->map(fn ($item) => (object) $item);
                }

                public function toPlayerCollection($data)
                {
                    return $data->map(fn ($item) => (object) $item);
                }

                public function toBetCollection($data)
                {
                    return $data->map(fn ($item) => (object) $item);
                }
            };
        }

        return null;
    }
}

// Mock Laravel's config() helper
if (! function_exists('config')) {
    function config($key = null, $default = null)
    {
        $config = [
            'app.name' => 'SOLID-Samples',
            'payment-gateway.filter_pipes.default' => [
                'ValidateFilters',
                'FormatDateFilters',
                'CacheFilters',
            ],
        ];

        if (is_null($key)) {
            return $config;
        }

        return $config[$key] ?? $default;
    }
}
