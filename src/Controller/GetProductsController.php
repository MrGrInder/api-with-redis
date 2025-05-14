<?php

declare(strict_types = 1);

namespace App\Controller;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use App\View\ProductsView;

readonly class GetProductsController
{
    /**
     * @param ProductsView $productsVew
     */
    public function __construct(
        private ProductsView $productsVew
    ) {
    }

    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    public function get(RequestInterface $request): ResponseInterface
    {
        $response = new JsonResponse();
        $category = $request->getQueryParams()['category'];

        $response->getBody()->write(
            json_encode(
                $this->productsVew->toArray($category),
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
            )
        );

        return $response
            ->withHeader('Content-Type', 'application/json; charset=utf-8')
            ->withStatus(200);
    }
}
