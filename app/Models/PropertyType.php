<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\LookupOptionService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PropertyType extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'filter_keyword',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saved(fn () => app(LookupOptionService::class)->clearCache());
        static::deleted(fn () => app(LookupOptionService::class)->clearCache());

        static::creating(function (PropertyType $propertyType): void {
            if (blank($propertyType->slug) && filled($propertyType->name)) {
                $propertyType->slug = Str::slug($propertyType->name);
            }

            if (blank($propertyType->filter_keyword) && filled($propertyType->slug)) {
                $propertyType->filter_keyword = $propertyType->slug;
            }
        });
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}
