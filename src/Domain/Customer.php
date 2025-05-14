<?php

declare(strict_types = 1);

namespace App\Domain;

final readonly class Customer
{
    /**
     * @param int $id
     * @param string $firstName
     * @param string $lastName
     * @param string $middleName
     * @param string $email
     */
    public function __construct(
        private int $id,
        private string $firstName,
        private string $lastName,
        private string $middleName,
        private string $email,
    ) {
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @return string
     */
    public function getMiddleName(): string
    {
        return $this->middleName;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return self
     */
    public static function anonymous(): self
    {
        return new self(
            0,
            'Anonymous',
            'User',
            '',
            'guest@example.com'
        );
    }
}
