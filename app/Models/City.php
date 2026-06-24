<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\LookupOptionService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class City extends Model
{
    protected $fillable = [
        'name',
        'slug',
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

        static::creating(function (City $city): void {
            if (blank($city->slug) && filled($city->name)) {
                $city->slug = Str::slug($city->name);
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
