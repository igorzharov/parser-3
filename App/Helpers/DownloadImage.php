<?php

declare(strict_types=1);

namespace App\Helpers;

trait DownloadImage
{
    public function downloadImage(string $url, string $parserName): string
    {
        $imageFolder = 'var/image' . '/' . $parserName . '/';

        if (!file_exists($imageFolder)) {
            mkdir($imageFolder, 0777, true);
        }

        $file = $imageFolder . md5($url) . '.jpg';

        if (!file_exists($file)) {
            file_put_contents($file, file_get_contents($url));
        }

        return 'catalog/' . $file;
    }
}