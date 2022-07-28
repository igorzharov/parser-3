<?php

declare(strict_types=1);

namespace App\Functions;

trait Helper {

    public function curl(string $url, $referer = 'http://www.google.com'): string {

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

    public function downloadHtml(string $url, string $parserName): string {

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

    public function getHtml(string $url): string {

        return $this->curl($url);
    }

    public function downloadImage(string $url, string $parserName): string {

        $imageFolder = 'App/image' . '/' . $parserName . '/';

        if (!file_exists($imageFolder)) {
            mkdir($imageFolder, 0777, true);
        }

        $file = $imageFolder . md5($url) . '.jpg';

        if (!file_exists($file)) {
            file_put_contents($file, file_get_contents($url));
        }

        return 'catalog/' . $file;
    }

    public function stringToNormal(string $string): string {

        $string = trim($string);

        $string = str_replace(' ', '___', $string);

        $string = preg_replace('/[^\w_]+/u', '', $string);

        $string = str_replace('___', ' ', $string);

        return mb_strtoupper(mb_substr($string, 0, 1)) . mb_substr($string, 1);
    }

    public function clearCache(string $parserClassName, string $cacheFolder) {

        $files = glob('App/cache/' . $parserClassName . '/' . $cacheFolder . '/*');

        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

}