{{-- Top bar --}}
<div class="bg-y2b-primary text-white text-sm">
    <div class="max-w-7xl mx-auto px-4 py-2 flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-wrap items-center gap-4">
            <a href="mailto:{{ config('site.email') }}" class="flex items-center gap-2 hover:text-y2b-light transition">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 512 512"><path d="M464 64H48C21.49 64 0 85.49 0 112v288c0 26.51 21.49 48 48 48h416c26.51 0 48-21.49 48-48V112c0-26.51-21.49-48-48-48zm0 48v40.805c-22.422 18.259-58.168 46.651-134.587 106.49-16.841 13.247-50.201 45.072-73.413 44.701-23.208.375-56.579-31.459-73.413-44.701C106.18 199.465 70.425 171.067 48 152.805V112h416zM48 400V214.398c22.914 18.251 55.409 43.862 104.938 82.646 21.857 17.205 60.134 55.186 103.062 54.955 42.717.231 80.509-37.199 103.053-54.947 49.528-38.783 82.032-64.401 104.947-82.653V400H48z"/></svg>
                {{ config('site.email') }}
            </a>
            <a href="{{ config('site.phone_href') }}" class="flex items-center gap-2 hover:text-y2b-light transition">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 448 512"><path d="M400 32H48A48 48 0 0 0 0 80v352a48 48 0 0 0 48 48h352a48 48 0 0 0 48-48V80a48 48 0 0 0-48-48zm-16.39 307.37l-15 65A15 15 0 0 1 354 416C194 416 64 286.29 64 126a15.7 15.7 0 0 1 11.63-14.61l65-15A18.23 18.23 0 0 1 144 96a16.27 16.27 0 0 1 13.79 9.09l30 70A17.9 17.9 0 0 1 189 181a17 17 0 0 1-5.5 11.61l-37.89 31a231.91 231.91 0 0 0 110.78 110.78l31-37.89A17 17 0 0 1 299 291a17.85 17.85 0 0 1 5.91 1.21l70 30A16.25 16.25 0 0 1 384 336a17.41 17.41 0 0 1-.39 3.37z"/></svg>
                {{ config('site.phone') }}
            </a>
        </div>

        {{-- Marquee --}}
        <div class="hidden md:flex flex-1 overflow-hidden mx-4 max-w-xl">
            <div class="marquee-track flex whitespace-nowrap gap-8 text-xs font-medium">
                @foreach(array_merge(config('site.marquee'), config('site.marquee')) as $text)
                    <span>{{ $text }}</span>
                @endforeach
            </div>
        </div>

        <div class="hidden lg:flex items-center gap-3">
            <span class="text-xs">Follow on</span>
            @foreach(config('site.social') as $platform => $url)
                <a href="{{ $url }}" target="_blank" rel="noopener" class="hover:text-y2b-accent transition">
                    @if($platform === 'facebook')
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 512 512"><path d="M504 256C504 119 393 8 256 8S8 119 8 256c0 123.78 90.69 226.38 209.25 245V327.69h-63V256h63v-54.64c0-62.15 37-96.48 93.67-96.48 27.14 0 55.52 4.84 55.52 4.84v61h-31.28c-30.8 0-40.41 19.12-40.41 38.73V256h68.78l-11 71.69h-57.78V501C413.31 482.38 504 379.78 504 256z"/></svg>
                    @elseif($platform === 'instagram')
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 448 512"><path d="M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.9 9.9 67.7 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.8 0-184.8zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z"/></svg>
                    @elseif($platform === 'linkedin')
                        <svg class="h-4 w-4 shrink-0" viewBox="0 0 16 16" aria-hidden="true">
                            <rect width="16" height="16" rx="2.5" fill="white"/>
                            <text x="8" y="11.25" text-anchor="middle" fill="#001b73" font-family="Arial, Helvetica, sans-serif" font-weight="700" font-size="8.5">in</text>
                        </svg>
                    @endif
                </a>
            @endforeach
        </div>
    </div>
</div>

{{-- Main header --}}
<header class="bg-white shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between gap-4">
        <a href="{{ route('home') }}" class="shrink-0">
            <img src="{{ config('site.logo') }}" alt="{{ config('site.name') }}" class="h-12 md:h-14 w-auto">
        </a>

        <nav class="hidden xl:flex items-center gap-6 text-sm font-medium text-gray-700">
            @foreach([
                ['route' => 'home', 'label' => 'Home'],
                ['route' => 'about', 'label' => 'About Us'],
                ['route' => 'properties.index', 'label' => 'All Properties'],
                ['route' => 'list-property', 'label' => 'List Your Property'],
                ['route' => 'channel-partner', 'label' => 'Become Channel Partner'],
                ['route' => 'home-loan', 'label' => 'Home Loan'],
                ['route' => 'contact', 'label' => 'Contact'],
            ] as $item)
                <a href="{{ route($item['route']) }}"
                   class="nav-link hover:text-y2b-primary transition {{ request()->routeIs($item['route']) ? 'active' : '' }}">
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        <div class="flex items-center gap-3">
            <button type="button"
                    data-open-consultation
                    class="hidden sm:inline-flex bg-y2b-primary hover:bg-y2b-primary-dark text-white text-sm font-semibold px-5 py-2.5 rounded transition cursor-pointer">
                INQUIRE NOW
            </button>

            <button id="mobile-menu-toggle" type="button" class="xl:hidden p-2 text-y2b-primary" aria-label="Toggle menu">
                <svg id="menu-icon-open" class="w-6 h-6" fill="currentColor" viewBox="0 0 1000 1000"><path d="M104 333H896C929 333 958 304 958 271S929 208 896 208H104C71 208 42 237 42 271S71 333 104 333ZM104 583H896C929 583 958 554 958 521S929 458 896 458H104C71 458 42 487 42 521S71 583 104 583ZM104 833H896C929 833 958 804 958 771S929 708 896 708H104C71 708 42 737 42 771S71 833 104 833Z"/></svg>
                <svg id="menu-icon-close" class="w-6 h-6 hidden" fill="currentColor" viewBox="0 0 1000 1000"><path d="M742 167L500 408 258 167C246 154 233 150 217 150 196 150 179 158 167 167 154 179 150 196 150 212 150 229 154 242 171 254L408 500 167 742C138 771 138 800 167 829 196 858 225 858 254 829L496 587 738 829C750 842 767 846 783 846 800 846 817 842 829 829 842 817 846 804 846 783 846 767 842 750 829 737L588 500 833 258C863 229 863 200 833 171 804 137 775 137 742 167Z"/></svg>
            </button>
        </div>
    </div>

    <div id="mobile-menu" class="hidden xl:hidden border-t bg-white">
        <nav class="max-w-7xl mx-auto px-4 py-4 flex flex-col gap-3 text-sm font-medium">
            @foreach([
                ['route' => 'home', 'label' => 'Home'],
                ['route' => 'about', 'label' => 'About Us'],
                ['route' => 'properties.index', 'label' => 'All Properties'],
                ['route' => 'list-property', 'label' => 'List Your Property'],
                ['route' => 'channel-partner', 'label' => 'Become Channel Partner'],
                ['route' => 'home-loan', 'label' => 'Home Loan'],
                ['route' => 'contact', 'label' => 'Contact'],
            ] as $item)
                <a href="{{ route($item['route']) }}" class="py-2 border-b border-gray-100 hover:text-y2b-primary">
                    {{ $item['label'] }}
                </a>
            @endforeach
            <button type="button" data-open-consultation
                    class="mt-2 w-full bg-y2b-primary text-white text-center py-3 rounded font-semibold cursor-pointer">
                INQUIRE NOW
            </button>
        </nav>
    </div>
</header>
