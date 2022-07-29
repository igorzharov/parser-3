<?php

declare(strict_types=1);

namespace App\Services;

trait LoggerService
{

    protected function getLogCategory(string $title)
    {
        echo 'Спарсил категорию - ' . $title . PHP_EOL;
    }

    protected function getLogRelation(string $title)
    {
        echo 'Спарсил отношение - ' . $title . PHP_EOL;
    }

    protected function getLogAddRelation(string $title)
    {
        echo 'Добавил отношение - ' . $title . PHP_EOL;
    }

    protected function getLogDisableRelation(string $title)
    {
        echo 'Отключил отношение - ' . $title . PHP_EOL;
    }

    protected function getLogProduct(string $title)
    {
        echo 'Спарсил товар - ' . $title . PHP_EOL;
    }

    protected function getLogUpdateProduct(string $title)
    {
        echo 'Обновил товар - ' . $title . PHP_EOL;
    }

    protected function getLogPassProduct(string $title)
    {
        echo 'Пропустил товар - ' . $title . PHP_EOL;
    }

    protected function getLogDisableProduct(string $title)
    {
        echo 'Отключил товар - ' . $title . PHP_EOL;
    }

}