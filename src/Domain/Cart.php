<?php

declare(strict_types = 1);

namespace App\Domain;

use Ramsey\Uuid\Uuid;

final class Cart
{
    /**
     * @param string $uuid
     * @param Customer $customer
     * @param string $paymentMethod
     * @param array $items
     */
    public function __construct(
        private readonly string $uuid,
        private readonly Customer $customer,
        private readonly string $paymentMethod,
        private array $items,
    ) {
    }

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * @return Customer
     */
    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    /**
     * @return string
     */
    public function getPaymentMethod(): string
    {
        return $this->paymentMethod;
    }

    /**
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @param CartItem $item
     * @return void
     */
    public function addItem(CartItem $item): void
    {
        $this->items[] = $item;
    }

    /**
     * @return self
     */
    public static function createDefault(): self
    {
        return new self(
            Uuid::uuid4()->toString(),
            Customer::anonymous(),
            'not_selected',
            []
        );
    }
}
