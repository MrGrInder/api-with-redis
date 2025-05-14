<?php

declare(strict_types = 1);

namespace App\Controller;

use App\Domain\CartItem;
use App\Exception\CartException;
use App\Exception\ProductNotFoundException;
use App\Repository\CartManager;
use App\Repository\ProductRepository;
use App\View\CartView;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;

readonly class AddToCartController
{
    /**
     * @param ProductRepository $productRepository
     * @param CartView $cartView
     * @param CartManager $cartManager
     * @param LoggerInterface $logger
     */
    public function __construct(
        private ProductRepository $productRepository,
        private CartView $cartView,
        private CartManager $cartManager,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws \Doctrine\DBAL\Exception
     */
    public function post(RequestInterface $request): ResponseInterface
    {
        try {
            $rawRequest = json_decode($request->getBody()->getContents(), true);
            $product = $this->productRepository->getByUuid($rawRequest['productUuid']);
            $cartUuid = $rawRequest['cart_uuid'];

            $cart = $this->cartManager->getCart($cartUuid);
            if (!$cart) {
                $cart = $this->cartManager->createCart();
                $this->logger->info('New cart created', ['uuid' => $cart->getUuid()]);
            }

            $cart->addItem(new CartItem(
                Uuid::uuid4()->toString(),
                $product->getUuid(),
                $product->getPrice(),
                $rawRequest['quantity'],
            ));

            $this->cartManager->saveCart($cart);

            return new JsonResponse([
                'status' => 'success',
                'cart' => $this->cartView->toArray($cart),
                'cart_uuid' => $cart->getUuid()
            ]);

        } catch (ProductNotFoundException $exception) {
            $this->logger->warning('Product not found in cart operation', [
                'uuid' => $exception->getProductUuid(),
                'error' => $exception->getMessage()
            ]);
            return new JsonResponse(
                ['error' => 'Product not found', 'uuid' => $exception->getProductUuid()],
                404
            );

        } catch (CartException $exception) {
            $this->logger->error('Cart operation failed', [
                'error' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString()
            ]);
            return new JsonResponse(
                ['error' => 'Cart service unavailable'],
                503
            );
        }
    }
}
