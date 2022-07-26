<?php

declare(strict_types=1);

namespace App\Parsers;

use Symfony\Component\DomCrawler\Crawler;


class ParserSantehOrbita extends Parser {

    private $parserClassName = 'ParserSantehOrbita';

    private $url = 'https://www.sanopt74.ru';

    private function parserCategories() {

        $html = $this->downloadHtml('https://www.sanopt74.ru/catalog/', $this->parserClassName . '/categories');

        $crawler = new Crawler($html);

        $crawler->filter('.intec-sections-tile.row.auto-clear .col-lg-3')->each(function (Crawler $node) {

            $title = $node->filter('.intec-section-name')->text();

            $title = explode(' (', $title)[0];

            $title = $this->stringToNormal($title);

            $url = $this->url . $node->filter('.intec-section-name')->attr('href');

            $this->db->insert('relations', ['title' => $title, 'url' => $url]);

            $this->getLogCategory($title);
        });
    }

    private function parserChildCategories() {

        $categories = $this->db->selectAll('relations', $this->parserClassName, '*');

        $node = '.intec-sections-tile.row.auto-clear .col-lg-3';

        foreach ($categories as $category) {
            $url = $category['url'];

            $html = $this->downloadHtml($url, $this->parserClassName . '/categories');

            $crawler = new Crawler($html);

            $countCategories = $crawler->filter($node)->count();

            if ($countCategories) {
                $crawler->filter($node)->each(function (Crawler $node, $i) use ($category) {

                    $title = $node->filter('.intec-section-name')->text();

                    $title = explode(' (', $title)[0];

                    $title = $this->stringToNormal($title);

                    $url = $this->url . $node->filter('.intec-section-name')->attr('href');

                    $parent_id = $category['category_id'];

                    $this->db->insert('relations', ['parent_id' => $parent_id, 'title' => $title, 'url' => $url,]);

                    $this->getLogCategory($title);
                });
            }
        }
    }

    private function parserRelations() {

        $categories = $this->db->selectAll('categories', $this->parserClassName);

        foreach ($categories as $category) {
            $url = $category['url'] . '?PAGEN_1=';

            $html = $this->downloadHtml($url . '1', $this->parserClassName . '/relations');

            $crawler = new Crawler($html);

            $crawler->filter('.intec-sections-tile.row')->count();

            if ($crawler->filter('.intec-sections-tile.row')->count() > 0) {
                continue;
            }

            $pagination = $crawler->filter('.bx-pagination-container')->count();

            $crawler->filter('.intec-catalog-section .catalog-section-element')->each(function (Crawler $node) use ($category, $url) {

                $this->db->insert('relations', ['category_id' => $category['category_id'], 'category_url' => $url . '1', 'product_url' => $this->url . $node->filter('.element-name a')->attr('href')]);

                $this->getLogRelation($node->filter('.element-name a')->attr('href'));
            });

            if ($pagination) {
                $counter = 1;

                start:

                $next = $crawler->filter('.bx-pag-next a')->count();

                if ($next) {
                    $counter++;

                    $html = $this->downloadHtml($url . $counter, $this->parserClassName . '/relations');

                    $crawler = new Crawler($html);

                    $crawler->filter('.intec-catalog-section .catalog-section-element')->each(function (Crawler $node) use ($counter, $category, $url) {

                        $this->db->insert('relations', ['category_id' => $category['category_id'], 'category_url' => $url . $counter, 'product_url' => $this->url . $node->filter('.element-name a')->attr('href')]);

                        $this->getLogRelation($node->filter('.element-name a')->attr('href'));
                    });

                    goto start;
                }
            }
        }
    }

    private function parserProducts() {

        $relations = $this->db->selectAll('relations', $this->parserClassName);

        foreach ($relations as $relation) {
            $html = $this->downloadHtml($relation['product_url'], $this->parserClassName . '/products');

            $crawler = new Crawler($html);

            var_dump($relation['product_url']);

            $title = $this->getTitle($crawler);
            $description = $this->getDescription($crawler);
            $price = $this->getPrice($crawler);
            $image = $this->getImage($crawler);
            $status = 1;

            if ($price == 0 || $title == '') {
                $status = 0;
            }

            $this->db->insert('relations', ['title' => $title, 'description' => $description, 'price' => $price, 'image' => $image, 'product_url' => $relation['product_url'], 'parser_class' => $this->parserClassName, 'is_parsed' => 1, 'is_update' => 1, 'status' => $status]);

            $this->getLogProduct($title);
        }
    }

    public function getCategories(): array {

        $this->parserCategories();
        $this->parserChildCategories();

        return $this->db->selectAll('relations', $this->parserClassName);
    }

    public function getRelations(): array {

        $this->parserRelations();

        return $this->db->selectAll('relations', $this->parserClassName);
    }

    public function getProducts(): array {

        $this->parserProducts();

        return $this->db->selectAll('products', $this->parserClassName);
    }

    public function getTitle(Crawler $crawler): string {

        try {
            $title = $crawler->filter('.intec-content-wrapper h1')->text();

            return $this->stringToNormal($title);
        }
        catch (\Exception $exception) {
            return '';
        }
    }

    public function getDescription(Crawler $crawler): string {

        try {
            return $crawler->filter('#tab-description')->html();
        }
        catch (\Exception $exception) {
            return '';
        }
    }

    public function getPrice(Crawler $crawler): int {

        try {
            $price = $crawler->filter('.item-additional-price .price-СайтРозничные')->text();

            $price = $this->stringToNormal($price);

            $price = mb_substr($price, 0, strlen($price) - 4);

            return (int)$price;
        }
        catch (\Exception $exception) {
            return 0;
        }
    }

    public function getImage(Crawler $crawler): string {

        try {
            $url = $this->url . $crawler->filter('.item-bigimage-container .item-bigimage-wrap .item-bigimage')->attr('src');

            return $this->downloadImage($url, $this->parserClassName);
        }
        catch (\Exception $exception) {
            return '';
        }
    }

}