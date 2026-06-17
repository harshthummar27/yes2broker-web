<?php

namespace App\Models;

use App\Support\MapEmbed;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Property extends Model
{
    protected $fillable = [
        'slug',
        'title',
        'location',
        'bhk',
        'area',
        'possession',
        'price',
        'price_min_lakhs',
        'image',
        'gallery',
        'description',
        'overview',
        'amenities',
        'faqs',
        'map_embed_url',
        'street_view_embed_url',
        'brochure_url',
        'city',
        'property_type',
        'is_new',
        'is_trending',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'gallery' => 'array',
            'overview' => 'array',
            'amenities' => 'array',
            'faqs' => 'array',
            'price_min_lakhs' => 'decimal:2',
            'is_new' => 'boolean',
            'is_trending' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Property $property): void {
            if (blank($property->slug) && filled($property->title)) {
                $property->slug = Str::slug($property->title);
            }

            if (filled($property->price)) {
                $property->price_min_lakhs = self::parsePriceMinLakhs($property->price);
            }

            if (blank($property->city)) {
                $property->city = str_contains(strtolower($property->location), 'gandhinagar')
                    ? 'Gandhinagar'
                    : 'Ahmedabad';
            }

            if (blank($property->map_embed_url) && filled($property->location)) {
                $property->map_embed_url = MapEmbed::mapUrl($property->location);
            }

            if (blank($property->street_view_embed_url) && filled($property->location)) {
                $property->street_view_embed_url = MapEmbed::streetViewUrl($property->location);
            }

            if (empty($property->gallery) && filled($property->image)) {
                $property->gallery = [$property->image];
            }

            $property->fillDefaultDetailFields();
        });
    }

    public function fillDefaultDetailFields(): void
    {
        if (blank($this->description)) {
            $this->description = sprintf(
                '%s is a premium real estate project located at %s. Offering %s across %s, with possession expected by %s and prices starting at %s.',
                $this->title,
                rtrim((string) $this->location, '.'),
                $this->bhk,
                $this->area,
                $this->possession,
                $this->price
            );
        }

        if (empty($this->overview)) {
            $this->overview = [
                'project_area' => $this->area,
                'configurations' => $this->bhk,
                'project_size' => 'Contact for details',
                'launch_date' => 'Contact for details',
                'price_range' => $this->price,
                'possession' => $this->possession,
                'rera_id' => 'Available on request',
            ];
        }

        if (empty($this->amenities) && ! $this->exists) {
            $this->amenities = [
                'Gymnasium',
                'Children\'s Play Area',
                '24×7 Security',
                'Power Backup',
                'Landscaped Gardens',
                'Parking',
            ];
        }

        if (empty($this->faqs) && ! $this->exists) {
            $this->faqs = [
                [
                    'question' => 'Where is '.$this->title.' located?',
                    'answer' => $this->location,
                ],
                [
                    'question' => 'What is the price range?',
                    'answer' => $this->price,
                ],
                [
                    'question' => 'When is possession expected?',
                    'answer' => $this->possession,
                ],
            ];
        }
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeFiltered(Builder $query, array $filters = []): Builder
    {
        $filters = array_filter($filters, fn ($value) => $value !== null && $value !== '');

        if (! empty($filters['city'])) {
            $city = strtolower($filters['city']);
            $query->where(function (Builder $builder) use ($city): void {
                if ($city === 'gandhinagar') {
                    $builder->whereRaw('LOWER(location) LIKE ?', ['%gandhinagar%']);
                } else {
                    $builder->whereRaw('LOWER(location) NOT LIKE ?', ['%gandhinagar%']);
                }
            });
        }

        if (! empty($filters['area'])) {
            $area = strtolower($filters['area']);
            $query->where(function (Builder $builder) use ($area): void {
                $builder->whereRaw('LOWER(location) LIKE ?', ["%{$area}%"])
                    ->orWhereRaw('LOWER(title) LIKE ?', ["%{$area}%"]);
            });
        }

        if (! empty($filters['type'])) {
            $typeMap = [
                'apartment' => 'bhk',
                'villa' => 'villa',
                'bungalow' => 'bungalow',
                'office' => 'office',
                'showroom' => 'showroom',
                'shop' => 'shop',
                'farmhouse' => 'farmhouse',
                'land' => 'plot',
            ];
            $needle = $typeMap[$filters['type']] ?? $filters['type'];
            $query->whereRaw('LOWER(bhk) LIKE ?', ['%'.strtolower($needle).'%']);
        }

        if (! empty($filters['budget'])) {
            $minLakhs = match ($filters['budget']) {
                '50l' => 50,
                '60l' => 60,
                '70l' => 70,
                '80l' => 80,
                '90l' => 90,
                '1cr' => 100,
                '2cr' => 200,
                '5cr' => 500,
                '10cr' => 1000,
                default => 0,
            };

            if ($minLakhs > 0) {
                $query->where('price_min_lakhs', '>=', $minLakhs);
            }
        }

        return $query;
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::get(fn () => self::resolveMediaUrl($this->image));
    }

    public function postcode(): string
    {
        if (preg_match('/\b(\d{6})\b/', $this->location, $matches)) {
            return $matches[1];
        }

        return '';
    }

    public static function resolveMediaUrl(?string $path): string
    {
        if (blank($path)) {
            return config('site.media_url').'/2025/09/img63-scaled.jpg';
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '//')) {
            return $path;
        }

        return Storage::disk('public')->url(ltrim($path, '/'));
    }

    public function galleryUrls(): array
    {
        $gallery = $this->gallery ?? [];

        if ($gallery === []) {
            return [$this->image_url];
        }

        return array_values(array_map(
            fn ($item) => is_string($item) ? self::resolveMediaUrl($item) : self::resolveMediaUrl($item['url'] ?? null),
            $gallery
        ));
    }

    public function toCardArray(): array
    {
        return [
            'slug' => $this->slug,
            'title' => $this->title,
            'location' => $this->location,
            'bhk' => $this->bhk,
            'area' => $this->area,
            'possession' => $this->possession,
            'price' => $this->price,
            'image' => $this->image_url,
            'is_new' => $this->is_new,
        ];
    }

    public function toDetailArray(): array
    {
        $overview = $this->overview ?? [];

        return [
            'slug' => $this->slug,
            'title' => $this->title,
            'location' => $this->location,
            'bhk' => $this->bhk,
            'area' => $this->area,
            'possession' => $this->possession,
            'price' => $this->price,
            'image' => $this->image_url,
            'gallery' => $this->galleryUrls(),
            'description' => $this->description ?? '',
            'overview' => [
                'project_area' => $overview['project_area'] ?? $this->area,
                'configurations' => $overview['configurations'] ?? $this->bhk,
                'project_size' => $overview['project_size'] ?? 'Contact for details',
                'launch_date' => $overview['launch_date'] ?? 'Contact for details',
                'price_range' => $overview['price_range'] ?? $this->price,
                'possession' => $overview['possession'] ?? $this->possession,
                'rera_id' => $overview['rera_id'] ?? 'Available on request',
            ],
            'amenities' => $this->amenities ?? [],
            'faqs' => $this->faqs ?? [],
            'map_embed_url' => $this->map_embed_url ?: (filled($this->location) ? MapEmbed::mapUrl($this->location) : null),
            'street_view_embed_url' => $this->street_view_embed_url ?: (filled($this->location) ? MapEmbed::streetViewUrl($this->location) : null),
            'brochure_url' => $this->brochure_url,
            'is_new' => $this->is_new,
        ];
    }

    public static function parsePriceMinLakhs(string $price): float
    {
        if (preg_match('/₹?\s*([\d.]+)\s*(Lakhs?|L\b|Cr)/i', $price, $matches)) {
            $value = (float) $matches[1];

            return str_contains(strtolower($matches[2]), 'cr') ? $value * 100 : $value;
        }

        return 0;
    }
}
