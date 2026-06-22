@props(['property'])

<a href="{{ route('properties.show', $property['slug']) }}"
   class="trending-card group flex items-stretch gap-2.5 p-2 bg-white border border-gray-200 rounded-xl hover:border-y2b-primary/30 hover:shadow-md transition-all">
    <div class="trending-card-image shrink-0 w-[108px] sm:w-[120px] rounded-lg overflow-hidden bg-gray-100">
        <img src="{{ $property['image'] }}" alt="{{ $property['title'] }}"
             class="w-full h-full min-h-[100px] object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy">
    </div>
    <div class="min-w-0 flex-1 flex flex-col justify-center py-0.5">
        <div class="flex items-center gap-1.5 mb-0.5">
            <h3 class="font-bold text-base text-gray-900 truncate group-hover:text-y2b-primary transition leading-tight">
                {{ $property['title'] }}
            </h3>
            <span class="shrink-0 w-[18px] h-[18px] rounded-full bg-y2b-accent flex items-center justify-center" title="Verified listing">
                <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
            </span>
        </div>
        <p class="text-xs text-gray-400 mb-0.5 truncate">{{ config('site.name') }}</p>
        <p class="text-sm text-gray-500 truncate leading-snug">{{ $property['bhk'] }} {{ $property['property_type_label'] ?? 'Flat' }}</p>
        <p class="text-sm text-gray-500 truncate leading-snug">
            {{ $property['short_location'] ?? $property['location'] }} {{ $property['city'] ?? 'Ahmedabad' }}
        </p>
        <p class="text-base font-bold text-gray-900 mt-0.5 truncate leading-tight">{{ $property['price'] }}</p>
    </div>
</a>
