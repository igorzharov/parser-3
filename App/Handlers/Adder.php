<?php

declare(strict_types=1);

namespace App\Handlers;

use App\DB\DBParser;
use App\Functions\Helper;
use App\Functions\Logger;
use App\Parsers\ParserFactory;


class Adder {

    private DBParser $db;

    public function __construct() {
        $this->db = new DBParser();
    }

    use Helper;

    use Logger;

    public function adder() {

        $parserClassName = 'ParserSantehOrbita';

        $parserRelations = $this->db->selectAll('relations', '');

        $parser = new ParserFactory();

        $parser = $parser->create($parserClassName);

        $this->clearCache($parserClassName, 'relations');

//        $remoteRelations = $parser->getRelations();

//        $newRelations = array_diff($remoteRelations, $parserRelations);

//        var_dump($newRelations);

//        foreach ($newRelations as $newRelation) {
//
//            $this->db->insert('relations', [
//                'category_id' => $newRelation['category_id'],
//                'category_url' => $url . $counter,
//                'product_url' => $this->url . $node->filter('.element-name a')->attr('href')
//            ]);
//
//        }

//        $parser->getProducts();

    }

}