<?php

declare(strict_types=1);

use App\Parsers\ParserFactory;

require_once 'vendor/autoload.php';

$parser = new ParserFactory();

$parserClassName = 'ParserSantehOrbita';

$parser = $parser->create($parserClassName);

$parser->clearAll($parserClassName);

$categories = $parser->getCategories();
$relations = $parser->getRelations('relations');
$products = $parser->getProducts();