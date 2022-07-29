<?php

use App\Handlers\Adder;

require_once 'vendor/autoload.php';

$updater = new Adder();

$updater->adder();