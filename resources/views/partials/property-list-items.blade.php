@php
    $startIndex = $startIndex ?? 0;
    $banners = $banners ?? [];
@endphp

@foreach($properties as $property)
    <x-property-list-card :property="$property" />

    @if(count($banners) > 0 && ($startIndex + $loop->iteration) % 6 === 0)
        @php
            $bannerIndex = (int) (($startIndex + $loop->iteration) / 6) - 1;
            $banner = $banners[$bannerIndex % count($banners)];
        @endphp
        <x-property-list-banner :banner="$banner" />
    @endif
@endforeach
