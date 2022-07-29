<?php

declare(strict_types=1);

namespace App\Handlers;

use App\DB\DBParser;
use App\Helpers\GetHtml;
use App\Services\LoggerService;
use App\Parsers\ParserFactory;
use Symfony\Component\DomCrawler\Crawler;

class Updater
{
    private DBParser $db;

    public function __construct()
    {
        $this->db = new DBParser();
    }

    use LoggerService;
    use GetHtml;

    public function updater()
    {
        $selectColumns = ['product_id', 'title', 'description', 'price', 'image', 'product_url', 'date_added', 'date_modify', 'parser_class', 'is_parsed', 'is_update', 'status'];

        $products = $this->db->select('products', $selectColumns, []);

        foreach ($products as $product) {
            $productId = $product['product_id'];

            $parserClassName = $product['parser_class'];

            $parser = ParserFactory::from($parserClassName)->create();

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

            $updateColumns = ['title' => $titleRemote, 'description' => $descriptionRemote, 'price' => $priceRemote, 'date_modify' => 'NOW()', 'is_update' => 1];

            if ($hashRemote != $hashParser) {
                $this->db->update('products', $updateColumns, ['product_id[=]' => $productId]);

                $this->getLogUpdateProduct($titleRemote);
            }

            $this->getLogPassProduct($titleParser);
        }
    }

}