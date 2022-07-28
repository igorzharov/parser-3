<?php

declare(strict_types=1);

namespace App\Parsers;

use App\DB\DBParser;
use App\Functions\Helper;
use App\Functions\Logger;
use Symfony\Component\DomCrawler\Crawler;


abstract class Parser {

    protected $db;

    public function __construct() {

        $this->db = new DBParser();
    }

    use Logger;

    use Helper;

    abstract public function getCategories();

    abstract public function getRelations(string $fillTable);

    abstract public function getProducts();

    abstract public function getTitle(Crawler $crawler): string;

    abstract public function getDescription(Crawler $crawler): string;

    abstract public function getPrice(Crawler $crawler): int;

    abstract public function getImage(Crawler $crawler): string;

    public function clearCategories(string $parserClassName) {

        $this->db->clear('categories', $parserClassName);
    }

    public function clearRelations(string $parserClassName) {

        $this->db->clear('relations', $parserClassName);
    }

    public function clearProducts(string $parserClassName) {

        $this->db->clear('products', $parserClassName);
    }

    public function clearAll(string $parserClassName) {

        $this->clearCategories($parserClassName);
        $this->clearRelations($parserClassName);
        $this->clearProducts($parserClassName);
    }

}