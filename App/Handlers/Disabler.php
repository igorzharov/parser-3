<?php

declare(strict_types=1);

namespace App\Handlers;

use App\DB\DBParser;
use App\Functions\Logger;

class Disabler {

    private DBParser $db;

    public function __construct() {

        $this->db = new DBParser();
    }

    use Logger;

    public function disabler() {

        $relations = $this->db->selectWhere('relations', 'product_id', ['status[=]' => 0]);

        foreach ($relations as $relation) {

            $productId = $relation;

            $this->db->update('products', ['status[0]' => 0], ['product_id' => $productId]);

            $this->getLogDisableProduct($productId);

        }

    }

}