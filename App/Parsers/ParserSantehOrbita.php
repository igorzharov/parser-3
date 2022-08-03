<?php

declare(strict_types=1);

namespace App\Parsers;

use App\Config\ParserSantehOrbitaConfig;
use App\Helpers\DownloadHtml;
use App\Helpers\DownloadImage;
use App\Helpers\StringToNormal;
use Symfony\Component\DomCrawler\Crawler;

class ParserSantehOrbita extends Parser
{

    use DownloadHtml;
    use DownloadImage;
    use StringToNormal;

    public ParserSantehOrbitaConfig $config;

    public function __construct()
    {
        parent::__construct();

        $this->config = new ParserSantehOrbitaConfig();
    }

    private string $parserClassName = 'ParserSantehOrbita';

    private string $url = 'https://www.sanopt74.ru';

    private function parserCategories()
    {
        $html = $this->downloadHtml('https://www.sanopt74.ru/catalog/', $this->parserClassName . '/categories');

        $crawler = new Crawler($html);

        $crawler->filter('.intec-sections-tile.row.auto-clear .col-lg-3')->each(function (Crawler $node)
        {
            $title = $node->filter('.intec-section-name')->text();

            $title = explode(' (', $title)[0];

            $title = $this->stringToNormal($title);

            $url = $this->url . $node->filter('.intec-section-name')->attr('href');

            $this->db->insert('categories', ['title' => $title, 'url' => $url, 'parser_class' => $this->parserClassName]);

            $this->getLogCategory($title);
        });
    }

    private function parserChildCategories()
    {
        $selectColumns = ['category_id', 'parent_id', 'title', 'description', 'url', 'image', 'parser_class', 'status'];

        $categories = $this->db->select('categories', $selectColumns, ['parser_class' => $this->parserClassName]);

        $node = '.intec-sections-tile.row.auto-clear .col-lg-3';

        foreach ($categories as $category)
        {
            $url = $category['url'];

            $html = $this->downloadHtml($url, $this->parserClassName . '/categories');

            $crawler = new Crawler($html);

            $countCategories = $crawler->filter($node)->count();

            if ($countCategories)
            {
                $crawler->filter($node)->each(function (Crawler $node, $i) use ($category)
                {
                    $title = $node->filter('.intec-section-name')->text();

                    $title = explode(' (', $title)[0];

                    $title = $this->stringToNormal($title);

                    $url = $this->url . $node->filter('.intec-section-name')->attr('href');

                    $parent_id = $category['category_id'];

                    $this->db->insert('categories', ['parent_id' => $parent_id, 'title' => $title, 'url' => $url, 'parser_class' => $this->parserClassName]);

                    $this->getLogCategory($title);
                });
            }
        }
    }

    private function parserRelations(string $fillableTable)
    {
        $selectColumns = ['category_id', 'parent_id', 'title', 'description', 'url', 'image', 'parser_class', 'status'];

        $categories = $this->db->select('categories', $selectColumns, ['parser_class' => $this->parserClassName]);

        foreach ($categories as $category)
        {
            $url = $category['url'] . '?PAGEN_1=';

            $counter = 1;

            $html = $this->downloadHtml($url . $counter, $this->parserClassName . '/relations');

            $crawler = new Crawler($html);

            if ($crawler->filter('.intec-sections-tile.row')->count() > 0)
            {
                continue;
            }

            $this->relationCrawler($fillableTable, $crawler, $counter, $category);

            $pagination = $crawler->filter('.bx-pagination-container')->count();

            if ($pagination)
            {
                start:

                $next = $crawler->filter('.bx-pag-next a')->count();

                if ($next)
                {
                    $counter++;

                    $html = $this->downloadHtml($url . $counter, $this->parserClassName . '/relations');

                    $crawler = new Crawler($html);

                    $this->relationCrawler($fillableTable, $crawler, $counter, $category);

                    goto start;
                }
            }
        }
    }

    private function relationCrawler($fillableTable, $crawler, $counter, $category)
    {
        $crawler->filter('.intec-catalog-section .catalog-section-element')->each(function (Crawler $node) use ($fillableTable, $counter, $category)
        {
            $url = $category['url'] . '?PAGEN_1=';

            $productUrl = $this->url . $node->filter('.element-name a')->attr('href');

            $this->db->insert($fillableTable, ['category_id' => $category['category_id'], 'category_url' => $url . $counter, 'product_url' => $productUrl, 'parser_class' => $this->parserClassName]);

            $this->getLogRelation($productUrl);
        });
    }

    private function parserProducts()
    {
        $selectColumns = ['product_id', 'category_id', 'category_url', 'product_url', 'parser_class', 'is_parsed', 'status'];

        $where = ['is_parsed[=]' => 0, 'status' => 1];

        $relations = $this->db->select('relations', $selectColumns, $where);

        foreach ($relations as $relation)
        {
            $html = $this->downloadHtml($relation['product_url'], $this->parserClassName . '/products');

            $crawler = new Crawler($html);

            $title = $this->getTitle($crawler);
            $description = $this->getDescription($crawler);
            $price = $this->getPrice($crawler);
            $image = $this->getImage($crawler);
            $status = 1;

            if ($price == 0 || $title == '')
            {
                $status = 0;
            }

            $insertColumns = ['product_id' => $relation['product_id'], 'title' => $title, 'description' => $description, 'price' => $price, 'image' => $image, 'product_url' => $relation['product_url'], 'parser_class' => $this->parserClassName, 'is_parsed' => 0, 'is_update' => 0, 'status' => $status];

            // SQL
            $this->db->insert('products', $insertColumns);

            // SQL
            $this->db->update('relations', ['is_parsed' => 1], ['product_id[=]' => $relation['product_id']]);

            // LOG
            $this->getLogProduct($title);
        }
    }

    public function getCategories()
    {
        $this->parserCategories();
        $this->parserChildCategories();
    }

    public function getRelations(string $fillableTable)
    {
        $this->parserRelations($fillableTable);
    }

    public function getProducts()
    {
        $this->parserProducts();
    }

    public function getTitle(Crawler $crawler): string
    {
        try
        {
            $title = $crawler->filter('.intec-content-wrapper h1')->text();

            return $this->stringToNormal($title);
        }
        catch (\Exception $exception)
        {
            return '';
        }
    }

    public function getDescription(Crawler $crawler): string
    {
        try
        {
            return $crawler->filter('#tab-description')->html();
        }
        catch (\Exception $exception)
        {
            return '';
        }
    }

    public function getPrice(Crawler $crawler): int
    {
        try
        {
            $price = $crawler->filter('.item-additional-price .price-СайтРозничные')->text();

            $price = str_replace(' ', '', $price);

            $price = $this->stringToNormal($price);

            $price = mb_substr($price, 0, strlen($price) - 4);

            return (int)$price;
        }
        catch (\Exception $exception)
        {
            return 0;
        }
    }

    public function getImage(Crawler $crawler): string
    {
        try
        {
            $url = $this->url . $crawler->filter('.item-bigimage-container .item-bigimage-wrap .item-bigimage')->attr('src');

            return $this->downloadImage($url, $this->parserClassName);
        }
        catch (\Exception $exception)
        {
            return '';
        }
    }

}