<?php

declare(strict_types=1);

use App\Support\SiteAsset;

if (! function_exists('site_asset')) {
    function site_asset(?string $path): string
    {
        return SiteAsset::url($path);
    }
}
