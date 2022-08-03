<?php

declare(strict_types=1);

use App\Services\ProductService;

require_once 'vendor/autoload.php';

$updater = new ProductService();

$updater->send('ParserSantehOrbita');


//$updater = new \App\Services\CategoryService();
//
//$updater->generateTree();