<?php

namespace App\Providers;

use App\Services\PaymentGateway\Contracts\PaymentGatewayInterface;
use App\Services\PaymentGateway\Mappers\DepositMapper;
use App\Services\PaymentGateway\Mappers\UserMapper;
use App\Services\PaymentGateway\PaymentGatewayManager;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

/**
 * Laravel Service Provider Pattern
 * Dependency Inversion: Binds abstractions to implementations
 */
class PaymentGatewayServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Laravel Manager Pattern Registration
        $this->app->singleton(PaymentGatewayInterface::class, function (Application $app) {
            return new PaymentGatewayManager($app);
        });

        // Bind mappers with Laravel naming convention
        $this->app->bind('payment-gateway.mappers.user', UserMapper::class);
        $this->app->bind('payment-gateway.mappers.deposit', DepositMapper::class);
        $this->app->bind('payment-gateway.mappers.withdrawal', WithdrawalMapper::class);
        $this->app->bind('payment-gateway.mappers.bonus', BonusMapper::class);
        $this->app->bind('payment-gateway.mappers.transaction', TransactionMapper::class);
    }

    public function boot(): void
    {
        // Laravel Configuration Publishing
        $this->publishes([
            __DIR__.'/../../config/payment-gateway.php' => config_path('payment-gateway.php'),
        ], 'payment-gateway-config');
    }
}
