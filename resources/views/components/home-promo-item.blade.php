@props(['item'])

@if(($item['type'] ?? '') === 'banner')
    @if(($item['link_action'] ?? 'url') === 'form')
        <button type="button"
                data-open-banner-promo
                data-promo-id="{{ $item['id'] }}"
                data-form-title="{{ $item['form_title'] }}"
                class="home-promo-banner-link group block w-full rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow cursor-pointer">
            <img src="{{ $item['image'] }}"
                 alt=""
                 class="w-full h-auto object-cover group-hover:opacity-95 transition-opacity pointer-events-none"
                 loading="lazy">
        </button>
    @else
        <a href="{{ $item['href'] }}"
           @if($item['opens_in_new_tab'] ?? false)
               target="_blank" rel="noopener noreferrer"
           @endif
           class="home-promo-banner-link group block rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow">
            <img src="{{ $item['image'] }}"
                 alt=""
                 class="w-full h-auto object-cover group-hover:opacity-95 transition-opacity"
                 loading="lazy">
        </a>
    @endif
@else
    <a href="{{ $item['href'] }}"
       @if($item['opens_in_new_tab'] ?? false)
           target="_blank" rel="noopener noreferrer"
       @endif
       class="listing-dream-banner group block rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow">
        <div class="listing-dream-banner-inner relative flex items-center gap-4 md:gap-8 px-5 py-6 sm:px-8 sm:py-8 md:px-12 md:py-10">
            @if(filled($item['image']))
                <div class="shrink-0 w-24 sm:w-28 md:w-36">
                    <img src="{{ $item['image'] }}" alt=""
                         class="w-full h-auto max-h-28 md:max-h-32 object-contain drop-shadow-md group-hover:scale-105 transition-transform duration-300"
                         loading="lazy">
                </div>
            @endif
            <div class="flex-1 min-w-0">
                @if(filled($item['headline']))
                    <p class="text-base sm:text-xl md:text-2xl font-bold text-y2b-primary leading-snug line-clamp-2">
                        {{ $item['headline'] }}
                    </p>
                @endif
                @if(filled($item['subtitle'] ?? null))
                    <p class="text-sm text-gray-600 mt-2 line-clamp-2 hidden sm:block">
                        {{ $item['subtitle'] }}
                    </p>
                @endif
            </div>
            <span class="listing-dream-banner-btn shrink-0 inline-flex items-center justify-center bg-y2b-primary text-white font-semibold text-sm sm:text-base px-5 py-2.5 sm:px-6 sm:py-3 rounded-lg group-hover:bg-y2b-primary-dark transition whitespace-nowrap">
                {{ $item['button_text'] }}
            </span>
        </div>
    </a>
@endif
