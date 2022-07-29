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

    public function updateRelations() {

        $parserClassName = 'ParserSantehOrbita';

        $parser = new ParserFactory();

        $parser = $parser->create($parserClassName);

        $this->clearCache($parserClassName, 'relations');

        $this->db->clear('new_relations', $parserClassName);

        $parser->getRelations('new_relations');

        // PARSER RELATIONS

        $selectColumns = ['category_id', 'category_url', 'product_url', 'parser_class'];

        $oldRelations = $this->db->select('relations', $selectColumns, ['is_parsed[=]' => 1, 'status[=]' => 1, 'parser_class[=]' => $parserClassName]);

        $oldRelations = array_map('json_encode', $oldRelations);

        // REMOTE RELATIONS

        $newRelations = $this->db->select('new_relations', $selectColumns, []);

        $newRelations = array_map('json_encode', $newRelations);

//      ARRAY DIFFERENCE

        $insertRelations = $this->jsonToArray(array_diff($newRelations, $oldRelations));

        $disableRelations = $this->jsonToArray(array_diff($oldRelations, $newRelations));

        $this->disableRelations($disableRelations);

        $this->insertRelations($insertRelations);

    }

    private function disableRelations(array $disableRelations) {

        foreach ($disableRelations as $disableRelation) {

            $this->db->update('relations', ['status' => 0], ['product_url[=]' => $disableRelation['product_url']]);

            $this->getLogDisableRelation($disableRelation['product_url']);

        }

    }

    private function insertRelations(array $insertRelations) {

        foreach ($insertRelations as $insertRelation) {

            $insertRelation = array_merge($insertRelation, ['status' => 1]);

            $this->db->insert('relations', $insertRelation);

            $this->getLogAddRelation($insertRelation['product_url']);
        }

    }

    private function jsonToArray(array $array) : array {

        return array_map(function($item) {return json_decode($item, true);}, $array);

    }

}