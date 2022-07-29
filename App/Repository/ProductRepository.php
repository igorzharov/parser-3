<?php

declare(strict_types=1);

namespace App\Repository;

use App\DB\DBRemote;

class ProductRepository
{
    private DBRemote $db;

    public function __construct()
    {
        $this->db = new DBRemote();
    }

    public function getProducts(): array
    {
        return $this->db->select('products', ['product_id', 'title', 'description', 'price', 'image'], ['is_parsed[=]' => 0, 'status[=]' => 1]);
    }

    public function getUpdatedProducts(): array
    {
        return $this->db->select('products', ['product_id', 'title', 'description', 'price', 'image'], ['is_parsed[=]' => 0, 'is_update[=]' => 1, 'status[=]' => 1]);
    }

    public function create(Product $product): Product
    {
        $product = $this->db->insert('products', $product);
    }
}