<?php

declare(strict_types=1);

namespace App\Helpers;

trait ArrayFromJson
{
    private function arrayFromJson(array $array): array
    {
        return array_map(function ($item) {
            return json_decode($item, true);
        }, $array);
    }
}