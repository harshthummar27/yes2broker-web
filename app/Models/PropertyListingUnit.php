<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyListingUnit extends Model
{
    protected $fillable = [
        'property_id',
        'sort_order',
        'configuration',
        'size_value',
        'size_unit',
        'total_units',
        'available_units',
    ];

    protected function casts(): array
    {
        return [
            'size_value' => 'decimal:4',
            'total_units' => 'integer',
            'available_units' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * @return array<string, mixed>
     */
    public function toOverviewRow(): array
    {
        return [
            'configuration' => $this->configuration,
            'size_value' => (float) $this->size_value,
            'size_unit' => $this->size_unit,
            'total_units' => $this->total_units,
            'available_units' => $this->available_units,
        ];
    }
}
