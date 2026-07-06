<?php

declare(strict_types=1);

use App\Support\SiteAsset;

if (! function_exists('site_asset')) {
    function site_asset(?string $path): string
    {
        return SiteAsset::url($path);
    }
}

if (! function_exists('mask_email')) {
    function mask_email(?string $email): string
    {
        if (blank($email) || ! str_contains($email, '@')) {
            return '*******@y2*.in';
        }

        [$local, $domain] = explode('@', $email, 2);
        $maskedLocal = str_repeat('*', max(7, strlen($local)));

        $lastDot = strrpos($domain, '.');

        if ($lastDot === false) {
            return $maskedLocal.'@'.substr($domain, 0, 2).'*';
        }

        $domainName = substr($domain, 0, $lastDot);
        $tld = substr($domain, $lastDot);
        $visible = substr($domainName, 0, min(2, strlen($domainName)));

        return $maskedLocal.'@'.$visible.'*'.$tld;
    }
}
