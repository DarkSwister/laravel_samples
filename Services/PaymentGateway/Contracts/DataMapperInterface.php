<?php

namespace App\Services\PaymentGateway\Contracts;

use Illuminate\Support\LazyCollection;

/**
 * Interface Segregation Principle - Focused on data mapping only
 */
interface DataMapperInterface
{
    public function toDTOCollection(LazyCollection $data): LazyCollection;
}
