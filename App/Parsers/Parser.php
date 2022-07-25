<?php

declare(strict_types=1);

namespace App\Parsers;

use App\DB\DBParser;
use App\Functions\Logger;

abstract class Parser
{
    use Logger;

    use DBParser;

    abstract public function getCategories() : array;

    abstract public function getRelations() : array;

    abstract public function getProducts() : array;

    abstract public function getTitle() : string;

    abstract public function getDescription() :string;

    abstract public function getPrice() : int;

    abstract public function getImage() : string;
}