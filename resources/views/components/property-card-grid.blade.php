@props(['property'])

<a href="{{ route('properties.show', $property['slug']) }}"
   class="property-card block bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow h-full flex flex-col">
    <div class="relative overflow-hidden h-52 shrink-0">
        <img src="{{ $property['image'] }}" alt="{{ $property['title'] }}"
             class="property-card-image w-full h-full object-cover" loading="lazy">
        @if($property['is_new'] ?? true)
            <span class="absolute top-3 right-3 bg-y2b-primary text-white text-xs font-bold px-3 py-1 rounded">New</span>
        @endif
    </div>
    <div class="p-4 flex flex-col flex-1">
        <h3 class="font-bold text-lg text-y2b-primary mb-2">{{ $property['title'] }}</h3>
        <p class="text-gray-500 text-xs flex items-start gap-1.5 mb-3 line-clamp-2">
            <svg class="w-3.5 h-3.5 shrink-0 mt-0.5 text-y2b-primary" fill="currentColor" viewBox="0 0 384 512"><path d="M172.268 501.67C26.97 291.031 0 269.413 0 192 0 85.961 85.961 0 192 0s192 85.961 192 192c0 77.413-26.97 99.031-172.268 309.67-9.535 13.774-29.93 13.773-39.464 0zM192 272c44.183 0 80-35.817 80-80s-35.817-80-80-80-80 35.817-80 80 35.817 80 80 80z"/></svg>
            {{ $property['location'] }}
        </p>
        <x-property-card-features :property="$property" class="text-xs text-gray-600 mb-4 flex-1" />
        <div class="flex items-center justify-between gap-3 mt-auto pt-2 border-t border-gray-100">
            <p class="font-bold text-y2b-primary text-sm">{{ $property['price'] }}</p>
            <span class="text-sm font-semibold text-y2b-primary border border-y2b-primary px-4 py-1.5 rounded hover:bg-y2b-primary hover:text-white transition shrink-0">
                Details
            </span>
        </div>
    </div>
</a>
