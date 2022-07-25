<?php

declare(strict_types=1);

use App\Parsers\ParserFactory;
use App\Parsers\ParserSantehOrbita;
use Medoo\Medoo;

require_once 'vendor/autoload.php';

$pdo = new PDO('mysql:dbname=parser;host=127.0.0.1', 'root', 'root');

$db = new Medoo([
    'pdo' => $pdo,
    'type' => 'mysql'
]);

$parser = new ParserFactory();

$parserType = 'ParserSantehOrbita';

$parser = $parser->create($parserType);

$categories = $parser->getCategories();

//$parser->getRelations();

//$parser->getProducts();