<?php

declare(strict_types=1);

namespace App\Repository;

use App\DB\DBParser;

class ProductRepository
{
    private DBParser $db;

    public function __construct()
    {
        $this->db = new DBParser();
    }

    public function getProducts(): array
    {
        $selectWhere = ['products.is_parsed[=]' => 0, 'products.status[=]' => 1];

        // LEFT JOIN

        return $this->db->select('products', ['[>]relations' => ['product_id' => 'product_id']], ['relations.category_id', 'products.product_id', 'products.title', 'products.description', 'products.price', 'products.image', 'products.parser_class'], $selectWhere);
    }

    public function getUpdatedProducts(): array
    {
        $selectWhere = ['products.is_parsed[=]' => 1, 'products.is_update[=]' => 1, 'products.status[=]' => 1];

        // LEFT JOIN

        return $this->db->select('products', ['[>]relations' => ['product_id' => 'product_id']], ['relations.category_id', 'products.product_id', 'products.title', 'products.description', 'products.price', 'products.image'], $selectWhere);
    }

}