<?php

declare(strict_types=1);

namespace App\Handlers;

use App\DB\DBParser;
use App\Functions\Helper;
use App\Functions\Logger;
use App\Parsers\ParserFactory;
use Symfony\Component\DomCrawler\Crawler;


class Updater {

    private DBParser $db;

    public function __construct() {
        $this->db = new DBParser();
    }

    use Helper;

    use Logger;

    public function updater() {

        $products = $this->db->selectAll('products', '');

        foreach ($products as $product) {

            $productId = $product['product_id'];

            $parserType = $product['parser_class'];

            $parser = new ParserFactory();

            $parser = $parser->create($parserType);

            $html = $this->getHtml($product['product_url']);

            $crawler = new Crawler($html);

            $titleRemote = $parser->getTitle($crawler);

            $descriptionRemote = $parser->getDescription($crawler);

            $priceRemote = $parser->getPrice($crawler);

            $titleParser = $product['title'];

            $descriptionParser = $product['description'];

            $priceParser = $product['price'];

            $hashRemote = md5($titleRemote . $descriptionRemote . $priceRemote);

            $hashParser = md5($titleParser . $descriptionParser . $priceParser);

            if ($hashRemote != $hashParser) {

                $this->db->updateProduct('products', [
                    'title' => $titleRemote,
                    'description' => $descriptionRemote,
                    'price' => $priceRemote,
                    'date_modify' => 'NOW()'
                ], $productId);

                $this->getLogUpdateProduct($titleRemote);

            }

            $this->getLogPassProduct($titleParser);

        }

    }

}