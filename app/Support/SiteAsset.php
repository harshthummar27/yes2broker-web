<?php

declare(strict_types=1);

namespace App\Support;

class SiteAsset
{
    public static function url(?string $path): string
    {
        if (blank($path)) {
            return '';
        }

        if (self::isAbsoluteUrl($path)) {
            return $path;
        }

        return asset(ltrim($path, '/'));
    }

    public static function media(string $relativePath): string
    {
        $base = rtrim((string) config('site.media_url', 'images/media'), '/');

        return self::url($base.'/'.ltrim($relativePath, '/'));
    }

    public static function isAbsoluteUrl(string $path): bool
    {
        return str_starts_with($path, 'http://')
            || str_starts_with($path, 'https://')
            || str_starts_with($path, '//');
    }
}
