<?php

declare(strict_types=1);

namespace App\Helpers;

trait ClearCache
{
    public function clearCache(string $parserClassName, string $cacheFolder)
    {
        $files = glob('App/cache/' . $parserClassName . '/' . $cacheFolder . '/*');

        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }
}