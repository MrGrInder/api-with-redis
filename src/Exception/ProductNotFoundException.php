<?php

declare(strict_types=1);

namespace App\Exception;

use Throwable;

class ProductNotFoundException extends \RuntimeException
{
    public function __construct(
        private string $productUuid,
        string $message,
        int $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function getProductUuid(): string
    {
        return $this->productUuid;
    }
}
