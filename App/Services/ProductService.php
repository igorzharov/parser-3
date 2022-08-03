<?php

declare(strict_types=1);

namespace App\Services;

use App\Parsers\ParserFactory;
use App\Repository\ProductRepository;
use App\Repository\ProductSenderRepository;

class ProductService
{
    private ProductRepository $productRepository;

    private ProductSenderRepository $sender;

    public function __construct()
    {
        $this->productRepository = new ProductRepository();

        $this->sender = new ProductSenderRepository();
    }

    public function send($parserClassName)
    {
        $products = $this->getProducts();

        $start = microtime(true);
        
        $data = $this->generateData($products, $parserClassName);

        echo 'Сформировал данные для отправки - ' . round(microtime(true) - $start, 3) . ' сек.' . PHP_EOL;

        $this->sender->addProducts($data);
    }

    private function getProducts(): array
    {
        return $this->productRepository->getProducts();
    }

    private function generateData($products, $parserClassName): array
    {
        $dateNow = date('Y-m-d H:i:s');

        $data = [];

        $parser = ParserFactory::from($parserClassName)->create();

        $config = $parser->getConfig();

        $configCategories = $config->categoryMatching;

        foreach ($products as $product) {

            $data[] = [
                'id' => $product['product_id'],
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
                'language_id' => 1,
                'renter_id' => 23,
                'user_id' => 24,
                'category_id' => MapperService::map($product['category_id'], $configCategories)
            ];
        }

        return $data;
    }
}