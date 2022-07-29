<?php

declare(strict_types=1);

namespace App\Helpers;

trait GetHtml {

    use Curl;

    public function getHtml(string $url): string
    {
        return $this->curl($url);
    }
}

