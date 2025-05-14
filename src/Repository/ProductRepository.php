<?php

declare(strict_types = 1);

namespace App\Repository;

use App\Exception\ProductNotFoundException;
use App\Repository\Entity\Product;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;

class ProductRepository
{
    private Connection $connection;

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param string $uuid
     * @return Product
     * @throws \Doctrine\DBAL\Exception
     * @throws ProductNotFoundException
     */
    public function getByUuid(string $uuid): Product
    {
        $product = $this->connection->fetchAssociative(
            "SELECT * FROM products WHERE is_active = 1 AND uuid = :uuid",
            ['uuid' => $uuid]
        );

        if (empty($product)) {
            throw new ProductNotFoundException(
                $uuid,
                sprintf('Product %s not found or inactive', $uuid)
            );
        }

        return $this->make($product);
    }

    /**
     * @param string $category
     * @return array
     * @throws \Doctrine\DBAL\Exception
     * @throws ProductNotFoundException
     */
    public function getByCategory(string $category): array
    {
        $products = $this->connection->fetchAllAssociative(
            "SELECT * FROM products WHERE is_active = 1 AND category = :category",
            ['category' => $category]
        );

        return array_map(
            fn (array $product) => $this->make($product),
            $products
        );
    }

    /**
     * @param array $uuids
     * @return array
     * @throws \Doctrine\DBAL\Exception
     * @throws ProductNotFoundException
     */
    public function getByUuids(array $uuids): array
    {
        if (empty($uuids)) {
            return [];
        }

        $products = $this->connection->fetchAllAssociative(
            "SELECT * FROM products WHERE uuid IN (:uuids)",
            ['uuids' => $uuids],
            ['uuids' => ArrayParameterType::STRING]
        );

        return array_map([$this, 'make'], $products);
    }

    /**
     * @param array $row
     * @return Product
     */
    public function make(array $row): Product
    {
        return new Product(
            $row['id'],
            $row['uuid'],
            $row['is_active'],
            $row['category'],
            $row['name'],
            $row['description'],
            $row['thumbnail'],
            $row['price'],
        );
    }
}
