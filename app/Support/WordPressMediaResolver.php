<?php

declare(strict_types=1);

namespace App\Support;

class WordPressMediaResolver
{
    public static function isWordPressUploadUrl(?string $path): bool
    {
        if (blank($path)) {
            return false;
        }

        return str_contains($path, '/wp-content/uploads/');
    }

    public static function uploadPathFromUrl(string $url): ?string
    {
        if (! self::isWordPressUploadUrl($url)) {
            return null;
        }

        if (preg_match('#/wp-content/uploads/(.+)$#i', $url, $matches) !== 1) {
            return null;
        }

        return $matches[1];
    }

    public static function productionCdnUrl(string $url): string
    {
        $uploadPath = self::uploadPathFromUrl($url);

        if ($uploadPath === null) {
            return $url;
        }

        $base = rtrim((string) config('media-import.wordpress_base', 'https://yes2broker.in/wp-content/uploads'), '/');

        return $base.'/'.$uploadPath;
    }

    public static function localStoragePath(string $slug, string $url): string
    {
        $uploadPath = self::uploadPathFromUrl($url);
        $filename = $uploadPath !== null ? basename($uploadPath) : 'image.jpg';

        return 'properties/'.$slug.'/'.$filename;
    }

    public static function filenameFromUrl(string $url): string
    {
        $path = parse_url($url, PHP_URL_PATH);
        $basename = basename((string) $path);

        return $basename !== '' ? $basename : 'image.jpg';
    }
}
