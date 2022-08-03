<?php

declare(strict_types=1);

namespace App\Repository;

use App\DB\DBRemote;

class ProductSenderRepository
{
    private DBRemote $db;

    public function __construct()
    {
        $this->db = new DBRemote();
    }

    public function addProducts(array $data)
    {
        $packageData = [];

        $count_add_packages = 0;

        $count_packages = round(count($data) / 100);

        foreach ($data as $key)
        {
            $start = microtime(true);

            $product_id = $this->addProduct($key);

            echo 'Отправил Товар ' . $key['id'] . ' - ' . round(microtime(true) - $start, 3) . ' сек.' . PHP_EOL;

            $key['product_id'] = $product_id;

            $packageData[] = $key;

            if ($count_packages == $count_add_packages)
            {
                $this->addPackage($packageData);
                echo '!!! Отправил Пакет - ' . round(microtime(true) - $start, 3) . ' сек.' . PHP_EOL;
                $packageData = [];
            }

            if (count($packageData) == 100)
            {
                $start = microtime(true);
                $this->addPackage($packageData);
                echo '!!! Отправил Пакет - ' . round(microtime(true) - $start, 3) . ' сек.' . PHP_EOL;
                $packageData = [];
                $count_add_packages++;
            }
        }
    }

    private function addProduct($data): int
    {
        unset($data['id'], $data['name'], $data['description'], $data['meta_title'], $data['meta_description'], $data['renter_id'], $data['category_id'], $data['store_id'], $data['layout_id'], $data['language_id']);

        $this->db->insert('oc_product', $data);

        return $this->db->id();
    }

    private function addPackage($packageData)
    {
        $this->addDescriptionPackage($packageData);
        $this->addImagePackage($packageData);
        $this->addCategoryPackage($packageData);
        $this->addStorePackage($packageData);
        $this->addRenterPackage($packageData);
    }

    private function addDescriptionPackage($packageData)
    {
        $data = [];

        foreach ($packageData as $key)
        {
            $data[] = ['product_id' => $key['product_id'], 'language_id' => $key['language_id'], 'name' => $key['name'], 'description' => $key['description'], 'meta_title' => $key['meta_title'], 'meta_description' => $key['meta_description']];
        }

        $this->db->insert('oc_product_description', $data);
    }

    private function addImagePackage($packageData)
    {
        $data = [];

        foreach ($packageData as $key)
        {
            $data[] = ['product_id' => $key['product_id'], 'image' => $key['image'], 'sort_order' => 0];
        }

        $this->db->insert('oc_product_image', $data);
    }

    private function addCategoryPackage($packageData)
    {
        $data = [];

        foreach ($packageData as $key)
        {
            foreach ($key['category_id'] as $path)
            {
                $data[] = ['product_id' => $key['product_id'], 'category_id' => $path];
            }
        }

        $this->db->insert('oc_product_to_category', $data);
    }

    private function addStorePackage($packageData)
    {
        $data = [];

        foreach ($packageData as $key)
        {
            $data[] = ['product_id' => $key['product_id'], 'store_id' => $key['store_id']];
        }

        $this->db->insert('oc_product_to_store', $data);
    }

    private function addRenterPackage($packageData)
    {
        $data = [];

        foreach ($packageData as $key)
        {
            $data[] = ['product_id' => $key['product_id'], 'renter_id' => $key['renter_id']];
        }

        $this->db->insert('oc_product_to_renter', $data);
    }

}