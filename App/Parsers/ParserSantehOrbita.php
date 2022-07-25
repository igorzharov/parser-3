<?php

declare(strict_types=1);

namespace App\Parsers;

use App\DB\DBParser;
use App\Functions\Logger;
use Symfony\Component\DomCrawler\Crawler;
use App\Functions\Helper;

class ParserSantehOrbita extends Parser
{

    private $parserName = 'ParserSantehOrbita';

    private $url = 'https://www.sanopt74.ru';

    private function parserCategories()
    {
        $html = Helper::getHtml('https://www.sanopt74.ru/catalog/', $this->parserName . '/categories');

        $crawler = new Crawler($html);

        $crawler->filter('.intec-sections-tile.row.auto-clear .col-lg-3')->each(function (Crawler $node) {

            $title = $node->filter('.intec-section-name')->text();

            $title = explode(' (', $title)[0];

            $url = $this->url . $node->filter('.intec-section-name')->attr('href');

            $this->insert('categories', [
                'title' => $title,
                'url' => $url
            ]);

            $this->getLogCategory($title);

        });

    }

    private function parserChildCategories() {

        $categories = $this->selectAll('categories', '*');

        $node = '.intec-sections-tile.row.auto-clear .col-lg-3';

        foreach ($categories as $category) {

            $url = $category['url'];

            $html = Helper::getHtml($url, $this->parserName . '/categories');

            $crawler = new Crawler($html);

            $countCategories = $crawler->filter($node)->count();

            if ($countCategories) {

                $crawler->filter($node)->each(function (Crawler $node, $i) use ($category) {
                    $title = $node->filter('.intec-section-name')->text();

                    $title = explode(' (', $title)[0];

                    $url = $this->url . $node->filter('.intec-section-name')->attr('href');

                    $parent_id = $category['category_id'];

                    $this->insert('categories', [
                        'parent_id' => $parent_id,
                        'title' => $title,
                        'url' => $url,
                    ]);

                    $this->getLogCategory($title);

                });

            }

        }

    }

    private function parserRelations() : array {

        $categories = $this->selectAll('categories');

        foreach ($categories as $category) {

            $url = $category . '?PAGEN_1=1';

            $html = Helper::getHtml($url, $this->parserName . '/relations');

            $crawler = new Crawler($html);

            $crawler->filter($node)->each(function (Crawler $node, $i) use ($category) {

        }

    }

    public function getCategories() : array {

        $this->parserCategories();
        $this->parserChildCategories();

        return $this->selectAll('categories');

    }

    public function getRelations(): array
    {
        // TODO: Implement getRelations() method.
    }

    public function getProducts(): array
    {
        // TODO: Implement getProducts() method.
    }

    public function getTitle() : string
    {
        return 'ttile';
    }

    public function getDescription() : string
    {
        // TODO: Implement getDescription() method.
    }

    public function getPrice() : int
    {
        // TODO: Implement getPrice() method.
    }

    public function getImage(): string
    {
        // TODO: Implement getImage() method.
    }
}