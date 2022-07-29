<?php

declare(strict_types=1);

namespace App\Helpers;

trait DownloadHtml
{
    use Curl;

    public function downloadHtml(string $url, string $parserName): string
    {
        $cacheFolder = 'App/cache' . '/' . $parserName . '/';

        if (!file_exists($cacheFolder)) {
            mkdir($cacheFolder, 0777, true);
        }

        $file = $cacheFolder . md5($url);

        if (file_exists($file)) {
            return file_get_contents($file);
        }

        $html = $this->curl($url);

        file_put_contents($file, $html);

        return $html;
    }

}