<?php

declare(strict_types=1);

namespace App\Handlers;

use App\DB\DBParser;
use App\Functions\Helper;
use App\Functions\Logger;
use App\Parsers\ParserFactory;

use Rogervila\ArrayDiffMultidimensional;


class Adder {

    private DBParser $db;

    public function __construct() {

        $this->db = new DBParser();
    }

    use Helper;

    use Logger;


    public function adder() {

        $parserClassName = 'ParserSantehOrbita';

        $parser = new ParserFactory();

        $parser = $parser->create($parserClassName);

//        $this->clearCache($parserClassName, 'relations');

//        $this->db->clear('temp_relations', $parserClassName);

//        $parser->getRelations('temp_relations');

        // PARSER

        $parserRelations = $this->db->selectWhere('relations', ['category_id', 'product_url', 'parser_class'], ['is_parsed[=]' => 1], $parserClassName);

        $parserRelations = array_map('json_encode', $parserRelations);

        // REMOTE

        $remoteRelations = $this->db->selectWhere('temp_relations', ['category_id', 'product_url', 'parser_class'], [], $parserClassName);

        $remoteRelations = array_map('json_encode', $remoteRelations);

//      ARRAY DIFFERENCE

        $newRelations = $this->jsonToArray(array_diff($remoteRelations, $parserRelations));

        $oldRelations = $this->jsonToArray(array_diff($parserRelations, $remoteRelations));

        foreach ($newRelations as $index => $newRelation) {
            var_dump($newRelation);
        }

        $this->addRelations($newRelations);

        $this->disableRelations($oldRelations);

    }

    private function disableRelations(array $oldRelations) {

        foreach ($oldRelations as $oldRelation) {

            $this->db->update('relations', ['status' => 0], ['product_url[=]' => $oldRelation['product_url']]);

            $this->getLogDisableProduct($oldRelation['product_url']);

        }

    }

    private function addRelations(array $newRelations) {

        foreach ($newRelations as $index => $newRelation) {

            $newRelation = array_merge($newRelation, ['status' => 1]);

            $this->db->insert('relations', $newRelation);

            $this->getLogRelation($newRelation['product_url']);
        }

    }

    private function jsonToArray(array $array) : array {

        return array_map(function($item) {return json_decode($item, true);}, $array);

    }

}