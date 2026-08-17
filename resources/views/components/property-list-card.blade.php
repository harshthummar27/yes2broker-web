@props(['property'])

@php
    $whatsappText = rawurlencode('Hi, I am interested in '.$property['title'].' listed on Yes2Broker.');
    $whatsappUrl = config('site.whatsapp_href').'?text='.$whatsappText;
    $phoneHref = config('site.phone_href');
    $detailUrl = route('properties.show', $property['slug']);
    $hasRera = filled($property['rera_id'] ?? null) && ! str_contains(strtolower($property['rera_id']), 'request');
@endphp

<article class="property-list-card bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow">
    <div class="flex flex-col sm:flex-row sm:items-stretch">
        {{-- Image --}}
        <a href="{{ $detailUrl }}" class="property-list-card-image relative shrink-0 sm:w-[240px] md:w-[270px] lg:w-[300px]">
            <img src="{{ $property['image'] }}" alt="{{ $property['title'] }}"
                 class="w-full h-64 sm:h-full sm:min-h-[280px] md:min-h-[300px] lg:min-h-[320px] object-cover" loading="lazy">
            @if($hasRera)
                <span class="property-list-badge property-list-badge-rera">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    RERA Registered
                </span>
            @endif
            <span class="property-list-photo-count">
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/></svg>
                {{ $property['gallery_count'] ?? 1 }}
            </span>
        </a>

        {{-- Details --}}
        <div class="flex-1 p-5 md:p-6 lg:p-7 min-w-0 relative flex flex-col justify-between">
            <div>
                @if($property['is_new'] ?? false)
                    <span class="property-list-new-tag">New Project</span>
                @endif

                <div class="flex flex-wrap items-start justify-between gap-2 mb-1.5 pr-24">
                    <a href="{{ $detailUrl }}" class="group min-w-0">
                        <h3 class="text-xl md:text-2xl font-bold text-y2b-primary group-hover:text-y2b-accent transition leading-snug">
                            {{ $property['title'] }}
                        </h3>
                    </a>
                </div>

                <p class="text-sm md:text-base text-gray-500 mb-5 line-clamp-1">
                    {{ $property['bhk'] }} {{ $property['property_type_label'] ?? 'Flat' }} in {{ $property['short_location'] ?? $property['location'] }}
                </p>

                {{-- Config row --}}
                <div class="property-list-config grid grid-cols-3 gap-px bg-gray-200 rounded-lg overflow-hidden mb-5 text-center text-sm">
                    <div class="bg-gray-50 px-3 py-3.5">
                        <p class="text-[10px] uppercase tracking-wide text-gray-400 mb-1">Configuration</p>
                        <p class="font-semibold text-gray-800 text-xs sm:text-sm md:text-base">{{ $property['bhk'] }}</p>
                    </div>
                    <div class="bg-gray-50 px-3 py-3.5">
                        <p class="text-[10px] uppercase tracking-wide text-gray-400 mb-1">Project Area</p>
                        <p class="font-semibold text-gray-800 text-xs sm:text-sm md:text-base">{{ $property['area'] }}</p>
                    </div>
                    <div class="bg-gray-50 px-3 py-3.5">
                        <p class="text-[10px] uppercase tracking-wide text-gray-400 mb-1">Price</p>
                        <p class="font-semibold text-y2b-primary text-xs sm:text-sm md:text-base">{{ $property['price'] }}</p>
                    </div>
                </div>

                {{-- Meta tags --}}
                <div class="flex flex-wrap gap-2 mb-4 text-xs md:text-sm text-gray-500">
                    <span class="inline-flex items-center gap-1 bg-y2b-light/60 text-y2b-primary px-3 py-1 rounded-full font-medium">
                        Zero Brokerage
                    </span>
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full border border-gray-200">
                        <svg class="w-3.5 h-3.5 text-y2b-primary" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10z"/></svg>
                        Possession: {{ $property['possession'] }}
                    </span>
                    @if($property['is_trending'] ?? false)
                        <span class="inline-flex items-center px-3 py-1 rounded-full border border-gray-200">
                            Trending Project
                        </span>
                    @endif
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex flex-wrap items-center justify-between gap-3 pt-4 border-t border-gray-100 mt-2">
                <p class="text-xs text-gray-400 truncate">
                    {{ config('site.name') }} Verified Listing
                </p>
                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener"
                       class="property-list-action property-list-action-whatsapp" aria-label="WhatsApp">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.435 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    </a>
                    <a href="{{ $phoneHref }}"
                       class="property-list-action property-list-action-call" aria-label="Call">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg>
                    </a>
                    @if(filled($property['brochure_url'] ?? null))
                        <a href="{{ route('properties.show', $property['slug']) }}?brochure=1"
                           class="property-list-btn property-list-btn-secondary hidden sm:inline-flex">
                            Brochure
                        </a>
                    @endif
                    <a href="{{ $detailUrl }}"
                       class="property-list-btn property-list-btn-primary">
                        Enquire Now
                    </a>
                </div>
            </div>
        </div>
    </div>
</article>
