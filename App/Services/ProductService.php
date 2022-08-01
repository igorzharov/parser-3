<?php

declare(strict_types=1);

namespace App\Services;

use App\Repository\ProductRepository;
use App\Repository\ProductSenderRepository;

class ProductService
{
    private ProductRepository $repository;

    private ProductSenderRepository $sender;

    public function __construct()
    {
        $this->repository = new ProductRepository();
        $this->sender = new ProductSenderRepository();
    }

    public function getProducts(): array
    {
        return $this->repository->getProducts();
    }

    public function send()
    {
        $products = $this->getProducts();

        $data = $this->generateData($products);

        $this->sender->addProducts($data);
    }

    public function generateData($products): array
    {
        $dateNow = date('Y-m-d H:i:s');

        $data = [];

        foreach ($products as $product) {
            $data[] = [
                'name' => $product['title'],
                'description' => $product['description'],
                'meta_title' => 'Купить ' . $product['title'] . ' в Челябинске - ТЦ ОРБИТА',
                'meta_description' => 'Онлайн каталог товаров торгового центра Орбита. Более 110 продавцов, тысячи товаров по низкой цене. Работает доставка курьером и самовывоз. Заказывай онлайн',
                'model' => '',
                'location' => 'Сектион',
                'quantity' => 100,
                'stock_status_id' => 1,
                'image' => $product['image'],
                'shipping' => 1,
                'price' => $product['price'],
                'tax_class_id' => 0,
                'date_available' => $dateNow,
                'moderate_status' => 1,
                'date_added' => $dateNow,
                'date_modified' => $dateNow,
                'id_external' => $product['product_id'],
                'store_id' => 0,
                'layout_id' => 1,
                'language_id' => 0,
                'renter_id' => 23,
                'category_id' => $product['category_id']
            ];
        }

        return $data;
    }
}