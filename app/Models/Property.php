<?php

namespace App\Models;

use App\Data\HomePageData;
use App\Support\AmenityIcon;
use App\Support\IndianPrice;
use App\Support\MapEmbed;
use App\Support\PossessionFilter;
use App\Support\ProjectAreaUnit;
use App\Support\PropertyOverview;
use App\Support\PropertyUnitConfiguration;
use App\Support\SiteAsset;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Property extends Model
{
    protected $fillable = [
        'slug',
        'title',
        'location',
        'address_line_1',
        'address_line_2',
        'locality',
        'bhk',
        'area',
        'project_area_value',
        'project_area_unit',
        'possession',
        'possession_date',
        'price',
        'price_min_amount',
        'price_max_amount',
        'price_min_lakhs',
        'image',
        'gallery',
        'description',
        'overview',
        'amenities',
        'faqs',
        'map_embed_url',
        'street_view_embed_url',
        'show_street_view',
        'brochure_url',
        'city',
        'state',
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
            'project_area_value' => 'decimal:4',
            'possession_date' => 'date',
            'price_min_amount' => 'integer',
            'price_max_amount' => 'integer',
            'show_street_view' => 'boolean',
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

            $overview = $property->overview;
            $unitConfigs = is_array($overview['unit_configurations'] ?? null)
                ? $overview['unit_configurations']
                : [];

            if ($unitConfigs !== []) {
                $prices = array_filter(
                    array_map(fn($item) => isset($item['price']) && $item['price'] !== '' ? (float) $item['price'] : null, $unitConfigs),
                    fn($p) => $p !== null && $p > 0
                );

                if (!empty($prices)) {
                    $property->price_min_amount = (int) min($prices);
                    if (count($prices) > 1 && max($prices) > min($prices)) {
                        $property->price_max_amount = (int) max($prices);
                    } else {
                        $property->price_max_amount = null;
                    }
                } else {
                    $property->price_min_amount = null;
                    $property->price_max_amount = null;
                }
            }

            if (filled($property->price_min_amount)) {
                $property->price = IndianPrice::formatRange(
                    $property->price_min_amount,
                    $property->price_max_amount
                );
                $property->price_min_lakhs = IndianPrice::toMinLakhs($property->price_min_amount);
            } elseif (filled($property->price)) {
                $property->price_min_lakhs = self::parsePriceMinLakhs($property->price);
            }

            if (filled($property->possession_date) && ! self::isReadyToMove($property->possession)) {
                $property->possession = Carbon::parse($property->possession_date)->format('F Y');
            }

            if (blank($property->city)) {
                $property->city = app(\App\Services\LookupOptionService::class)->defaultCityName();
            }

            if (blank($property->state)) {
                $property->state = app(\App\Services\LookupOptionService::class)->defaultStateName();
            }

            if (filled($property->address_line_1)) {
                $property->location = self::composeLocation(
                    $property->address_line_1,
                    $property->address_line_2,
                    $property->locality,
                    $property->city,
                    $property->state,
                );
            }

            if (blank($property->project_area_unit)) {
                $property->project_area_unit = app(\App\Services\LookupOptionService::class)->defaultProjectUnitName();
            }

            if (filled($property->project_area_value) && filled($property->project_area_unit)) {
                $property->area = self::formatProjectArea(
                    $property->project_area_value,
                    $property->project_area_unit
                );
            }

            $property->syncOverviewFromListingFields();

            $locationForMaps = $property->displayLocation();

            if (blank($property->map_embed_url) && filled($locationForMaps)) {
                $property->map_embed_url = MapEmbed::mapUrl($locationForMaps);
            }

            if ($property->show_street_view === null) {
                $property->show_street_view = true;
            }

            if (! $property->show_street_view) {
                $property->street_view_embed_url = null;
            } elseif (blank($property->street_view_embed_url) && filled($locationForMaps)) {
                $property->street_view_embed_url = MapEmbed::streetViewUrl($locationForMaps);
            }

            if (empty($property->gallery) && filled($property->image)) {
                $property->gallery = [$property->image];
            }

            $property->fillDefaultDetailFields();
        });

        static::saved(function (Property $property): void {
            app(\App\Services\PropertyListingUnitService::class)->syncPropertyFromOverviewJson($property);
        });
    }

    /**
     * @return HasMany<PropertyListingUnit, $this>
     */
    public function listingUnits(): HasMany
    {
        return $this->hasMany(PropertyListingUnit::class)->orderBy('sort_order');
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function unitConfigurationItems(): array
    {
        return app(\App\Services\PropertyListingUnitService::class)->normalizedListForProperty($this);
    }

    public function fillDefaultDetailFields(): void
    {
        if (blank($this->description)) {
            $this->description = sprintf(
                '%s is a premium real estate project located at %s. Offering %s across %s, with possession expected by %s and prices starting at %s.',
                $this->title,
                rtrim($this->displayLocation(), '.'),
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

        if (empty($this->faqs) && ! $this->exists) {
            $this->faqs = [
                [
                    'question' => 'Where is '.$this->title.' located?',
                    'answer' => $this->displayLocation(),
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

    public function syncOverviewFromListingFields(): void
    {
        $this->overview = PropertyOverview::buildPayload([
            'area' => $this->area,
            'bhk' => $this->displayBhk(),
            'price' => $this->price,
            'possession' => $this->possession,
            'overview' => $this->overview ?? [],
        ]);

        $unitConfigurations = $this->unitConfigurationItems();

        if ($unitConfigurations !== []) {
            $composed = PropertyUnitConfiguration::composeBhkLabel($unitConfigurations);

            if (filled($composed)) {
                $this->bhk = $composed;
            }
        }
    }

    public static function formatProjectArea(float|string|null $value, ?string $unit): string
    {
        if ($value === null || $value === '' || blank($unit)) {
            return '';
        }

        $numeric = (float) $value;
        $formatted = ProjectAreaUnit::formatValue($numeric);

        return $formatted.' '.$unit;
    }

    /**
     * @return array{value: ?string, unit: ?string}
     */
    public static function parseProjectArea(?string $area): array
    {
        if (blank($area)) {
            return ['value' => null, 'unit' => null];
        }

        if (preg_match('/^([\d.]+)\s+(.+)$/u', trim($area), $matches) !== 1) {
            return ['value' => null, 'unit' => null];
        }

        return [
            'value' => $matches[1],
            'unit' => trim($matches[2]),
        ];
    }

    /**
     * @return list<array{name: string, icon: string}>
     */
    public function amenityItems(): array
    {
        $items = [];

        foreach ($this->amenities ?? [] as $amenity) {
            $name = is_string($amenity) ? $amenity : ($amenity['name'] ?? '');

            if (blank($name)) {
                continue;
            }

            $items[] = [
                'name' => $name,
                'icon' => AmenityIcon::resolve($name),
            ];
        }

        return $items;
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeFiltered(Builder $query, array $filters = []): Builder
    {
        $filters = array_filter($filters, fn ($value) => $value !== null && $value !== '');

        if (! empty($filters['city'])) {
            $city = City::query()
                ->active()
                ->where('slug', strtolower((string) $filters['city']))
                ->first();

            if ($city !== null) {
                $query->whereRaw('LOWER(city) = ?', [strtolower($city->name)]);
            }
        }

        if (! empty($filters['area'])) {
            $area = strtolower($filters['area']);
            $query->where(function (Builder $builder) use ($area): void {
                $builder->whereRaw('LOWER(location) LIKE ?', ["%{$area}%"])
                    ->orWhereRaw('LOWER(title) LIKE ?', ["%{$area}%"]);
            });
        }

        if (! empty($filters['type'])) {
            $propertyType = PropertyType::query()
                ->active()
                ->where('slug', (string) $filters['type'])
                ->first();

            $needle = $propertyType?->filter_keyword ?? (string) $filters['type'];
            $query->whereRaw('LOWER(bhk) LIKE ?', ['%'.strtolower($needle).'%']);
        }

        if (! empty($filters['budget'])) {
            $maxBudgetLakhs = HomePageData::budgetMaxLakhs($filters['budget']);

            if ($maxBudgetLakhs !== null) {
                $query->where('price_min_lakhs', '>', 0)
                    ->where('price_min_lakhs', '<=', $maxBudgetLakhs);
            }
        }

        if (! empty($filters['possession_filter']) && $filters['possession_filter'] !== 'all') {
            PossessionFilter::apply($query, $filters['possession_filter']);
        }

        return $query;
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::get(fn () => self::resolveMediaUrl($this->image));
    }

    protected function mapEmbedUrl(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => MapEmbed::normalizeEmbedInput($value),
        );
    }

    protected function streetViewEmbedUrl(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => MapEmbed::normalizeEmbedInput($value),
        );
    }

    public function displayLocation(): string
    {
        if (filled($this->address_line_1)) {
            return self::composeLocation(
                $this->address_line_1,
                $this->address_line_2,
                $this->locality,
                $this->city,
                $this->state,
            );
        }

        return (string) ($this->location ?? '');
    }

    public static function composeLocation(
        ?string $addressLine1,
        ?string $addressLine2 = null,
        ?string $locality = null,
        ?string $city = null,
        ?string $state = null,
    ): string {
        return implode(', ', array_filter([
            filled($addressLine1) ? trim($addressLine1) : null,
            filled($addressLine2) ? trim($addressLine2) : null,
            filled($locality) ? trim($locality) : null,
            filled($city) ? trim($city) : null,
            filled($state) ? trim($state) : null,
        ]));
    }

    public function postcode(): string
    {
        $location = $this->displayLocation();

        if (preg_match('/\b(\d{6})\b/', $location, $matches)) {
            return $matches[1];
        }

        return '';
    }

    public static function resolveMediaUrl(?string $path): string
    {
        if (blank($path)) {
            return SiteAsset::url((string) config('site.default_property_image'));
        }

        if (SiteAsset::isAbsoluteUrl($path)) {
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

    public function displayBhk(): string
    {
        $unitConfigurations = $this->unitConfigurationItems();

        if ($unitConfigurations !== []) {
            $composed = PropertyUnitConfiguration::composeBhkLabel($unitConfigurations);

            if (filled($composed)) {
                return $composed;
            }
        }

        return (string) ($this->bhk ?? '');
    }

    public function toCardArray(): array
    {
        $overview = $this->overview ?? [];
        $gallery = $this->galleryUrls();
        $locationParts = array_map('trim', explode(',', $this->displayLocation()));
        $shortLocation = filled($this->locality)
            ? $this->locality
            : ($locationParts[0] ?? $this->displayLocation());

        return [
            'slug' => $this->slug,
            'title' => $this->title,
            'location' => $this->displayLocation(),
            'short_location' => $shortLocation,
            'city' => $this->city ?? 'Ahmedabad',
            'bhk' => $this->displayBhk(),
            'area' => $this->area,
            'possession' => $this->possession,
            'price' => $this->price,
            'image' => $this->image_url,
            'gallery_count' => max(count($gallery), 1),
            'is_new' => $this->is_new,
            'is_trending' => $this->is_trending,
            'rera_id' => $overview['rera_id'] ?? null,
            'brochure_url' => $this->resolveBrochureUrl(),
            'property_type_label' => $this->propertyTypeLabel(),
        ];
    }

    public function propertyTypeLabel(): string
    {
        if (filled($this->property_type)) {
            return (string) $this->property_type;
        }

        $bhk = strtolower((string) $this->bhk);

        if (str_contains($bhk, 'villa')) {
            return 'Villa';
        }

        if (str_contains($bhk, 'bungalow')) {
            return 'Bungalow';
        }

        if (str_contains($bhk, 'office') || str_contains($bhk, 'showroom') || str_contains($bhk, 'commercial')) {
            return 'Commercial';
        }

        if (str_contains($bhk, 'plot') || str_contains($bhk, 'land')) {
            return 'Plot';
        }

        if (str_contains($bhk, 'farmhouse')) {
            return 'Farmhouse';
        }

        if (preg_match('/\d+\s*bhk/i', (string) $this->bhk)) {
            return 'Flat';
        }

        return 'Property';
    }

    public function toDetailArray(): array
    {
        $unitConfigurations = $this->unitConfigurationItems();
        $overviewInput = $this->overview ?? [];

        if ($unitConfigurations !== []) {
            $overviewInput['unit_configurations'] = $unitConfigurations;
        }

        $overview = PropertyOverview::buildPayload([
            'area' => $this->area,
            'bhk' => $this->displayBhk(),
            'price' => $this->price,
            'possession' => $this->possession,
            'overview' => $overviewInput,
        ]);

        return [
            'slug' => $this->slug,
            'title' => $this->title,
            'location' => $this->displayLocation(),
            'bhk' => $this->displayBhk(),
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
                'unit_configurations' => PropertyUnitConfiguration::presentationItems($unitConfigurations),
                'items' => PropertyUnitConfiguration::overviewGridItems($overview),
            ],
            'amenities' => $this->amenityItems(),
            'faqs' => $this->faqs ?? [],
            'map_embed_url' => $this->map_embed_url ?: (filled($this->displayLocation()) ? MapEmbed::mapUrl($this->displayLocation()) : null),
            'street_view_embed_url' => $this->resolveStreetViewEmbedUrl(),
            'brochure_url' => $this->resolveBrochureUrl(),
            'is_new' => $this->is_new,
        ];
    }

    public function resolveBrochureUrl(): ?string
    {
        if (blank($this->brochure_url)) {
            return null;
        }

        if (SiteAsset::isAbsoluteUrl($this->brochure_url)) {
            return $this->brochure_url;
        }

        return Storage::disk('public')->url(ltrim($this->brochure_url, '/'));
    }

    public function resolveStreetViewEmbedUrl(): ?string
    {
        if ($this->show_street_view === false) {
            return null;
        }

        if (filled($this->street_view_embed_url)) {
            return $this->street_view_embed_url;
        }

        return filled($this->displayLocation())
            ? MapEmbed::streetViewUrl($this->displayLocation())
            : null;
    }

    public static function parsePriceMinLakhs(string $price): float
    {
        $parsed = IndianPrice::parseRange($price);

        if ($parsed['min'] !== null) {
            return IndianPrice::toMinLakhs($parsed['min']);
        }

        return 0;
    }

    public static function isReadyToMove(?string $possession): bool
    {
        return filled($possession) && str_contains(strtolower($possession), 'ready');
    }

    public static function parsePossessionDate(?string $possession): ?string
    {
        if (blank($possession) || self::isReadyToMove($possession)) {
            return null;
        }

        $normalized = trim(str_replace(',', '', $possession));

        try {
            return Carbon::parse('1 '.$normalized)->startOfMonth()->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }

    public static function formatMonthYear(?string $date): ?string
    {
        if (blank($date)) {
            return null;
        }

        return Carbon::parse($date)->startOfMonth()->format('F Y');
    }

    public static function parseLaunchDate(?string $launchDate): ?string
    {
        return self::parsePossessionDate($launchDate);
    }

    /**
     * @return list<string>
     */
    public static function parseBhkSelections(array|string|null $bhk): array
    {
        if (is_array($bhk)) {
            return array_values(array_filter(array_map('trim', $bhk)));
        }

        if (blank($bhk)) {
            return [];
        }

        $knownOptions = array_keys(app(\App\Services\LookupOptionService::class)->configurationsForAdmin());
        $matched = [];

        foreach ($knownOptions as $option) {
            if (stripos($bhk, $option) !== false) {
                $matched[] = $option;
            }
        }

        if ($matched !== []) {
            return $matched;
        }

        return array_values(array_filter(array_map(
            'trim',
            preg_split('/\s*,\s*/', $bhk) ?: []
        )));
    }

    /**
     * @param  list<string>  $selections
     */
    public static function composeBhkSelections(array $selections): string
    {
        return implode(', ', array_values(array_filter(array_map('trim', $selections))));
    }
}
