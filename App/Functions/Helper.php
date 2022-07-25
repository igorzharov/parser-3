<?php

declare(strict_types=1);

namespace App\Functions;

class Helper {

    public static function curl(string $url, $referer = 'http://www.google.com') : string
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.121 Safari/537.36");
        curl_setopt($ch, CURLOPT_REFERER, $referer);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    public static function getHtml(string $url, string $parserName) : string {

        $cacheFolder = 'cache' . '/' . $parserName . '/';

        if (!file_exists($cacheFolder)) {
            mkdir($cacheFolder, 0777, true);
        }

        $file = $cacheFolder . md5($url);

        if (file_exists($file)) {
            return file_get_contents($file);
        }

        $html = self::curl($url);

        file_put_contents($file, $html);

        return $html;
    }
}