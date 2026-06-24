@props([
    'items' => [],
])

@if(count($items) > 0)
<section {{ $attributes->merge(['class' => 'home-promo-section']) }}>
    <div class="space-y-4 md:space-y-5">
        @foreach($items as $item)
            <x-home-promo-item :item="$item" />
        @endforeach
    </div>
</section>
@endif
