<?php

declare(strict_types = 1);

namespace App\View;

use App\Domain\Cart;
use App\Repository\ProductRepository;

readonly class CartView
{
    /**
     * @param ProductRepository $productRepository
     */
    public function __construct(
        private ProductRepository $productRepository
    ) {
    }

    /**
     * @param Cart $cart
     * @return array
     * @throws \Doctrine\DBAL\Exception
     */
    public function toArray(Cart $cart): array
    {
        $productUuids = array_map(fn($item) => $item->getProductUuid(), $cart->getItems());
        $products = $this->productRepository->getByUuids($productUuids);
        $productMap = [];

        foreach ($products as $product) {
            $productMap[$product->getUuid()] = $product;
        }

        $data = [
            'uuid' => $cart->getUuid(),
            'customer' => [
                'id' => $cart->getCustomer()->getId(),
                'name' => implode(' ', [
                    $cart->getCustomer()->getLastName(),
                    $cart->getCustomer()->getFirstName(),
                    $cart->getCustomer()->getMiddleName(),
                ]),
                'email' => $cart->getCustomer()->getEmail(),
            ],
            'payment_method' => $cart->getPaymentMethod(),
        ];

        $total = 0;
        $data['items'] = [];
        foreach ($cart->getItems() as $item) {
            $product = $productMap[$item->getProductUuid()] ?? [];
            if (empty($product)) {
                continue;
            }

            $itemTotal = $item->getPrice() * $item->getQuantity();
            $total += $itemTotal;

            $data['items'][] = [
                'uuid' => $item->getUuid(),
                'price' => $item->getPrice(),
                'total' => $itemTotal,
                'quantity' => $item->getQuantity(),
                'product' => [
                    'id' => $product->getId(),
                    'uuid' => $product->getUuid(),
                    'name' => $product->getName(),
                    'thumbnail' => $product->getThumbnail(),
                    'price' => $product->getPrice(),
                    'is_active' => $product->isActive(),
                ],
            ];
        }

        $data['total'] = $total;

        return $data;
    }
}
