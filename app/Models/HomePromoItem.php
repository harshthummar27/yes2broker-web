<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class HomePromoItem extends Model
{
    public const TYPE_BANNER = 'banner';

    public const TYPE_PROPERTY = 'property';

    public const LINK_ACTION_URL = 'url';

    public const LINK_ACTION_FORM = 'form';

    public const PLACEMENT_HOME = 'home';

    public const PLACEMENT_PROPERTIES = 'properties';

    protected $fillable = [
        'type',
        'placement',
        'banner_image',
        'link_action',
        'form_title',
        'property_id',
        'slogan',
        'link_url',
        'button_text',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function scopeForPlacement(Builder $query, string $placement): Builder
    {
        return $query->where('placement', $placement);
    }

    public function isBanner(): bool
    {
        return $this->type === self::TYPE_BANNER;
    }

    public function isProperty(): bool
    {
        return $this->type === self::TYPE_PROPERTY;
    }

    public function isFormBanner(): bool
    {
        return $this->isBanner() && $this->link_action === self::LINK_ACTION_FORM;
    }

    public function isUrlBanner(): bool
    {
        return $this->isBanner() && $this->link_action !== self::LINK_ACTION_FORM;
    }

    public function imageUrl(): string
    {
        if ($this->isBanner() && filled($this->banner_image)) {
            return Storage::disk('public')->url(ltrim($this->banner_image, '/'));
        }

        if ($this->isProperty() && $this->property) {
            return $this->property->image_url;
        }

        return '';
    }

    public function headline(): string
    {
        if (filled($this->slogan)) {
            return $this->slogan;
        }

        if ($this->isProperty() && $this->property) {
            return $this->property->title;
        }

        return '';
    }

    public function subtitle(): ?string
    {
        if ($this->isProperty() && $this->property) {
            return $this->property->location;
        }

        return null;
    }

    public function href(): string
    {
        if (filled($this->link_url)) {
            $link = trim($this->link_url);

            if (str_starts_with($link, '/')) {
                return url($link);
            }

            return $link;
        }

        if ($this->isProperty() && $this->property) {
            return route('properties.show', $this->property->slug);
        }

        return route('properties.index');
    }

    public function opensInNewTab(): bool
    {
        if (! filled($this->link_url)) {
            return false;
        }

        return str_starts_with(trim($this->link_url), 'http://')
            || str_starts_with(trim($this->link_url), 'https://');
    }

    public function toCardArray(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'link_action' => $this->link_action ?: self::LINK_ACTION_URL,
            'form_title' => $this->form_title ?: 'Get in Touch',
            'image' => $this->imageUrl(),
            'headline' => $this->headline(),
            'subtitle' => $this->subtitle(),
            'href' => $this->href(),
            'button_text' => $this->button_text ?: 'Explore More',
            'opens_in_new_tab' => $this->opensInNewTab(),
        ];
    }
}
