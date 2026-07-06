@props([
    'amenities' => [],
    'limit' => 6,
    'iconClass' => 'w-3.5 h-3.5',
    'showLabels' => true,
])

@if(count($amenities) > 0)
    <div {{ $attributes->merge(['class' => 'property-amenities-inline']) }}>
        @foreach(array_slice($amenities, 0, $limit) as $amenity)
            @php
                $name = is_array($amenity) ? ($amenity['name'] ?? '') : $amenity;
                $icon = is_array($amenity) ? ($amenity['icon'] ?? 'default') : 'default';
            @endphp
            @if(filled($name))
                <span class="property-amenity-chip" title="{{ $name }}">
                    <span class="property-amenity-chip-icon">
                        <x-amenity-icon :icon="$icon" :class="$iconClass" />
                    </span>
                    @if($showLabels)
                        <span class="property-amenity-chip-label">{{ $name }}</span>
                    @endif
                </span>
            @endif
        @endforeach

        @if(count($amenities) > $limit)
            <span class="property-amenity-more">+{{ count($amenities) - $limit }} more</span>
        @endif
    </div>
@endif
