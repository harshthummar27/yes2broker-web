<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\LookupOptionService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Locality extends Model
{
    protected $fillable = [
        'city_id',
        'name',
        'slug',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'city_id' => 'integer',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saved(fn () => app(LookupOptionService::class)->clearCache());
        static::deleted(fn () => app(LookupOptionService::class)->clearCache());

        static::creating(function (Locality $locality): void {
            if (blank($locality->slug) && filled($locality->name)) {
                $locality->slug = Str::slug($locality->name);
            }
        });
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
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
