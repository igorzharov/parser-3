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
        foreach ($data as $key) {
            $start = microtime(true);

            $product_id = $this->addProduct($key);

            $key['product_id'] = $product_id;

            $this->addDescription($key);
            $this->addImage($key);
            $this->addCategory($key);
            $this->addLayout($key);
            $this->addStore($key);
            $this->addRenter($key);

            echo 'Отправил Товар ' . ' - ' . round(microtime(true) - $start, 3) . ' сек.' . PHP_EOL;
        }
    }

    private function addProduct($data): int
    {
        unset($data['name'], $data['description'], $data['meta_title'], $data['meta_description'], $data['renter_id'], $data['category_id'], $data['store_id'], $data['layout_id'], $data['language_id']);

        $this->db->insert('oc_product', $data);

        return $this->db->id();
    }

    private function addDescription($data)
    {
        $data = ['product_id' => $data['product_id'], 'language_id' => $data['language_id'], 'name' => $data['name'], 'description' => $data['description'], 'meta_title' => $data['meta_title'], 'meta_description' => $data['meta_description']];

        $this->db->insert('oc_product_description', $data);
    }

    private function addImage($data)
    {
        $data = ['product_id' => $data['product_id'], 'image' => $data['image'], 'sort_order' => 0];

        $this->db->insert('oc_product_image', $data);
    }

    private function addCategory($data)
    {
        $data = ['product_id' => $data['product_id'], 'category_id' => $data['category_id']];

        $this->db->insert('oc_product_to_category', $data);
    }

    private function addLayout($data)
    {
        $data = ['product_id' => $data['product_id'], 'store_id' => $data['store_id'], 'layout_id' => $data['layout_id']];

        $this->db->insert('oc_product_to_layout', $data);
    }

    private function addStore($data)
    {
        $data = ['product_id' => $data['product_id'], 'store_id' => $data['store_id']];

        $this->db->insert('oc_product_to_store', $data);
    }

    private function addRenter($data)
    {
        $data = ['product_id' => $data['product_id'], 'renter_id' => $data['renter_id']];

        $this->db->insert('oc_product_to_renter', $data);
    }

}