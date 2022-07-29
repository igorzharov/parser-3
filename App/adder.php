<?php

declare(strict_types=1);

use App\Handlers\Adder;

require_once 'vendor/autoload.php';

$updater = new Adder();

$updater->adder();