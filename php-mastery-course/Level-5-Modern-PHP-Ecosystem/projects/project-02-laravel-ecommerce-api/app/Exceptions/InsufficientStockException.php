<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class InsufficientStockException extends Exception
{
    public function __construct(string $productName = '')
    {
        $message = $productName
            ? "Insufficient stock for product: {$productName}"
            : 'Insufficient stock for one or more products.';

        parent::__construct($message, 422);
    }
}
