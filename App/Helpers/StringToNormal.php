<?php

declare(strict_types=1);

namespace App\Helpers;

trait StringToNormal
{
    public function stringToNormal(string $string): string
    {
        $string = trim($string);

        $string = str_replace(' ', '___', $string);

        $string = preg_replace('/[^\w_]+/u', '', $string);

        $string = str_replace('___', ' ', $string);

        return mb_strtoupper(mb_substr($string, 0, 1)) . mb_substr($string, 1);
    }
}