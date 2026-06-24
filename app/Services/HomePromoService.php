<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\HomePromoItem;
use Illuminate\Support\Collection;

class HomePromoService
{
    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function forHome(): Collection
    {
        return $this->activeForPlacement(HomePromoItem::PLACEMENT_HOME);
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function forPropertiesList(): Collection
    {
        return $this->activeForPlacement(HomePromoItem::PLACEMENT_PROPERTIES);
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function activeForPlacement(string $placement): Collection
    {
        return HomePromoItem::query()
            ->active()
            ->forPlacement($placement)
            ->ordered()
            ->with('property')
            ->get()
            ->filter(fn (HomePromoItem $item): bool => $this->isValid($item))
            ->map(fn (HomePromoItem $item) => $item->toCardArray())
            ->values();
    }

    private function isValid(HomePromoItem $item): bool
    {
        if ($item->isBanner()) {
            if (! filled($item->banner_image)) {
                return false;
            }

            if ($item->isFormBanner()) {
                return true;
            }

            return filled($item->link_url);
        }

        return $item->property !== null;
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     */
    public function hasFormBanner(array $items): bool
    {
        foreach ($items as $item) {
            if (($item['type'] ?? '') === HomePromoItem::TYPE_BANNER
                && ($item['link_action'] ?? '') === HomePromoItem::LINK_ACTION_FORM) {
                return true;
            }
        }

        return false;
    }
}
