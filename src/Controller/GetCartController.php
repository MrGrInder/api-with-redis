<?php

declare(strict_types = 1);

namespace App\Controller;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use App\Repository\CartManager;
use App\View\CartView;

readonly class GetCartController
{
    /**
     * @param CartView $cartView
     * @param CartManager $cartManager
     */
    public function __construct(
        public CartView $cartView,
        public CartManager $cartManager
    ) {
    }

    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws \Doctrine\DBAL\Exception
     */
    public function get(RequestInterface $request): ResponseInterface
    {
        $response = new JsonResponse();
        $cart = $this->cartManager->getCart($request->getQueryParams()['cart_uuid']);

        if (!$cart) {
            $response->getBody()->write(
                json_encode(
                    ['message' => 'Cart not found'],
                    JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
                )
            );

            return $response
                ->withHeader('Content-Type', 'application/json; charset=utf-8')
                ->withStatus(404);
        } else {
            $response->getBody()->write(
                json_encode(
                    $this->cartView->toArray($cart),
                    JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
                )
            );
        }

        return $response
            ->withHeader('Content-Type', 'application/json; charset=utf-8')
            ->withStatus(200);
    }
}
