<?php

declare(strict_types = 1);

namespace App\Repository;

use App\Exception\CartException;
use App\Infrastructure\ConnectorException;
use Psr\Log\LoggerInterface;
use App\Domain\Cart;
use App\Infrastructure\ConnectorFacade;

readonly class CartManager
{
    /**
     * @param ConnectorFacade $connectorFacade
     * @param LoggerInterface $logger
     */
    public function __construct(
        private ConnectorFacade $connectorFacade,
        private LoggerInterface $logger
    ) {
    }

    /**
     * @return Cart
     */
    public function createCart(): Cart
    {
        $cart = Cart::createDefault();
        $this->saveCart($cart);
        $this->logger->info('New cart created', ['uuid' => $cart->getUuid()]);

        return $cart;
    }

    /**
     * @param Cart $cart
     * @return void
     */
    public function saveCart(Cart $cart): void
    {
        try {
            $this->connectorFacade->getConnector()->set($cart->getUuid(), $cart);
        } catch (ConnectorException $exception) {
            $this->logger->error('Error saving cart: ' . $exception->getMessage(), [
                'uuid' => $cart->getUuid(),
                'error' => $exception->getMessage(),
                'exception' => $exception,
            ]);
            throw new CartException('Failed to save cart');
        }
    }

    /**
     * @param string $cartUuid
     * @return Cart|null
     */
    public function getCart(string $cartUuid): ?Cart
    {
        try {
            return $this->connectorFacade->getConnector()->get($cartUuid);
        } catch (ConnectorException $exception) {
            $this->logger->error('Error retrieving cart: ' . $exception->getMessage(), [
                'uuid' => $cartUuid,
                'error' => $exception->getMessage(),
                'exception' => $exception,
            ]);
            return null;
        }
    }
}
