<?php

declare(strict_types=1);

namespace App\Services;

use App\Repository\ProductRepository;

class ProductService
{
    private ProductRepository $repository;

    public function __construct()
    {
        $this->repository = new ProductRepository();
    }

    public function getProducts(): array
    {
        return $this->repository->select('products', ['product_id', 'title', 'description', 'price', 'image'], ['is_parsed[=]' => 0, 'status[=]' => 1]);
    }
}