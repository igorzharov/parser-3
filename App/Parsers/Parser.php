<?php

declare(strict_types=1);

namespace App\Parsers;

use App\DB\DBParser;
use App\Services\LoggerService;
use App\Helpers\ArrayFromJson;
use Symfony\Component\DomCrawler\Crawler;

abstract class Parser
{
    protected DBParser $db;

    public function __construct()
    {
        $this->db = new DBParser();
    }

    use LoggerService;
    use ArrayFromJson;

    abstract public function getCategories();

    abstract public function getRelations(string $fillableTable);

    abstract public function getProducts();

    abstract public function getTitle(Crawler $crawler): string;

    abstract public function getDescription(Crawler $crawler): string;

    abstract public function getPrice(Crawler $crawler): int;

    abstract public function getImage(Crawler $crawler): string;

    public function formattingRelations($parserClassName)
    {
        // PARSER RELATIONS
        $selectColumns = ['category_id', 'category_url', 'product_url', 'parser_class'];

        $oldRelations = $this->db->select('relations', $selectColumns, ['status[=]' => 1, 'parser_class[=]' => $parserClassName]);

        $oldRelations = array_map('json_encode', $oldRelations);

        // REMOTE RELATIONS

        $newRelations = $this->db->select('new_relations', $selectColumns, []);

        $newRelations = array_map('json_encode', $newRelations);

        // ARRAY DIFFERENCE

        $insertRelations = $this->arrayFromJson(array_diff($newRelations, $oldRelations));

        $disableRelations = $this->arrayFromJson(array_diff($oldRelations, $newRelations));

        $this->disableRelations($disableRelations);

        $this->insertRelations($insertRelations);
    }

    private function disableRelations(array $disableRelations)
    {
        foreach ($disableRelations as $disableRelation) {
            $this->db->update('relations', ['status' => 0], ['product_url[=]' => $disableRelation['product_url']]);

            $this->getLogDisableRelation($disableRelation['product_url']);
        }
    }

    private function insertRelations(array $insertRelations)
    {
        foreach ($insertRelations as $insertRelation) {
            $insertRelation = array_merge($insertRelation, ['status' => 1]);

            $this->db->insert('relations', $insertRelation);

            $this->getLogAddRelation($insertRelation['product_url']);
        }
    }

    public function clearCategories(string $parserClassName)
    {
        $this->db->clear('categories', $parserClassName);
    }

    public function clearRelations(string $parserClassName)
    {
        $this->db->clear('relations', $parserClassName);
    }

    public function clearProducts(string $parserClassName)
    {
        $this->db->clear('products', $parserClassName);
    }

    public function clearAll(string $parserClassName)
    {
        $this->clearCategories($parserClassName);
        $this->clearRelations($parserClassName);
        $this->clearProducts($parserClassName);
    }

}