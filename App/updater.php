<?php

use App\Handlers\Updater;

require_once 'vendor/autoload.php';

$updater = new Updater();

$updater->updater();