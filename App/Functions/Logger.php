<?php

declare(strict_types=1);

namespace App\Functions;

trait Logger {

    protected function getLogCategory(string $title) {
        echo 'Спарсил категорию - ' . $title . PHP_EOL;
    }

    protected function getLogRelation(string $title) {
        echo 'Спарсил отношение - ' . $title . PHP_EOL;
    }

    protected function getLogProduct(string $title) {
        echo 'Спарсил товар - ' . $title . PHP_EOL;
    }

}