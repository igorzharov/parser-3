<?php

declare(strict_types=1);

namespace App\Handlers;

use App\DB\DBParser;
use App\Services\Helper;
use App\Services\LoggerService;
use App\Helpers\ArrayFromJson;
use App\Helpers\ClearCache;
use App\Parsers\ParserFactory;

use Rogervila\ArrayDiffMultidimensional;

class Adder
{
    private DBParser $db;

    public function __construct()
    {
        $this->db = new DBParser();
    }

    use ClearCache;
    use LoggerService;

    public function adder()
    {
        $parserClassName = 'ParserSantehOrbita';

        $parser = ParserFactory::from($parserClassName)->create();

        $this->clearCache($parserClassName, 'relations');

        $this->db->clear('new_relations', $parserClassName);

        $parser->getRelations('new_relations');

        $parser->formattingRelations($parserClassName);

        $this->disableProducts();

        $this->addProducts();
    }

    private function addProducts()
    {
        $selectColumns = ['product_id', 'product_url', 'parser_class'];

        $where = ['is_parsed[=]' => 0, 'status' => 1];

        $relations = $this->db->select('relations', $selectColumns, $where);

        foreach ($relations as $relation) {
            $parserClassName = $relation['parser_class'];

            $parser = ParserFactory::from($parserClassName)->create();

            $parser->getProducts();
        }
    }

    private function disableProducts()
    {
        $selectColumns = ['product_id', 'status'];

        $where = ['status[=]' => 0];

        $relations = $this->db->select('relations', $selectColumns, $where);

        foreach ($relations as $relation) {
            $data = $this->db->update('products', ['status' => 0], ['product_id[=]' => $relation['product_id']]);

            if ($data->rowCount()) {
                $this->getLogDisableProduct((string)$relation['product_id']);
            }
        }
    }

}