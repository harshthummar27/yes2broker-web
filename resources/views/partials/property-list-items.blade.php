@php
    $startIndex = $startIndex ?? 0;
    $promos = $promos ?? [];
@endphp

@foreach($properties as $property)
    <x-property-list-card :property="$property" />

    @if(count($promos) > 0 && ($startIndex + $loop->iteration) % 6 === 0)
        @php
            $promoIndex = (int) (($startIndex + $loop->iteration) / 6) - 1;
            $promo = $promos[$promoIndex % count($promos)];
        @endphp
        <x-home-promo-item :item="$promo" />
    @endif
@endforeach
