<footer class="bg-y2b-footer text-gray-300">
    <div class="max-w-7xl mx-auto px-4 py-12 grid md:grid-cols-2 lg:grid-cols-4 gap-10">
        <div>
            <a href="{{ route('home') }}">
                <img src="{{ config('site.logo_footer') }}" alt="{{ config('site.name') }}" class="h-12 mb-4">
            </a>
            <p class="text-sm leading-relaxed text-gray-400">
                We specialize in helping buyers, sellers, and investors find the best residential and commercial properties with expert advice, transparent service, and local market knowledge.
            </p>
            <div class="flex items-center gap-3 mt-5">
                <span class="text-xs">Follow on</span>
                @foreach(config('site.social') as $platform => $url)
                    <a href="{{ $url }}" target="_blank" rel="noopener" class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center hover:bg-y2b-primary transition">
                        <span class="sr-only">{{ ucfirst($platform) }}</span>
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 512 512">
                            @if($platform === 'facebook')
                                <path d="M279.14 288l14.22-92.66h-88.91v-60.13c0-25.35 12.42-50.06 52.24-50.06h40.42V6.26S260.43 0 225.36 0c-73.22 0-121.08 44.38-121.08 124.72v70.62H22.89V288h81.39v224h100.17V288z"/>
                            @elseif($platform === 'instagram')
                                <path d="M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.9 9.9 67.7 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.8 0-184.8zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z"/>
                            @else
                                <path d="M100.28 448H7.4V148.9h92.88zM53.79 108.1C24.09 108.1 0 83.5 0 53.8a53.79 53.79 0 0 1 107.58 0c0 29.7-24.1 54.3-53.79 54.3zM447.9 448h-92.68V302.4c0-34.7-.7-79.2-48.29-79.2-48.29 0-55.69 37.7-55.69 76.7V448h-92.78V148.9h89.08v40.8h1.3c12.4-23.5 42.69-48.3 87.88-48.3 94 0 111.28 61.9 111.28 142.3V448z"/>
                            @endif
                        </svg>
                    </a>
                @endforeach
            </div>
        </div>

        <div>
            <h3 class="text-white font-semibold mb-4">Quick Link</h3>
            <ul class="space-y-2 text-sm">
                <li><a href="{{ route('home') }}" class="hover:text-white transition">Home</a></li>
                <li><a href="{{ route('properties.index') }}" class="hover:text-white transition">All Properties</a></li>
                <li><a href="{{ route('about') }}" class="hover:text-white transition">About</a></li>
                <li><a href="{{ route('contact') }}" class="hover:text-white transition">Contact Us</a></li>
            </ul>
        </div>

        <div>
            <h3 class="text-white font-semibold mb-4">Top Properties</h3>
            <ul class="space-y-2 text-sm">
                @foreach($topProperties as $property)
                    <li>
                        <a href="{{ route('properties.show', $property['slug']) }}" class="hover:text-white transition">
                            {{ $property['title'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        <div>
            <h3 class="text-white font-semibold mb-4">Contact Us</h3>
            <ul class="space-y-4 text-sm">
                <li class="flex gap-3">
                    <svg class="w-5 h-5 shrink-0 text-y2b-accent mt-0.5" fill="currentColor" viewBox="0 0 384 512"><path d="M172.268 501.67C26.97 291.031 0 269.413 0 192 0 85.961 85.961 0 192 0s192 85.961 192 192c0 77.413-26.97 99.031-172.268 309.67-9.535 13.774-29.93 13.773-39.464 0zM192 272c44.183 0 80-35.817 80-80s-35.817-80-80-80-80 35.817-80 80 35.817 80 80 80z"/></svg>
                    <a href="{{ config('site.maps_url') }}" target="_blank" rel="noopener" class="hover:text-white transition">
                        {{ config('site.address') }}
                    </a>
                </li>
                <li class="flex gap-3">
                    <svg class="w-5 h-5 shrink-0 text-y2b-accent" fill="currentColor" viewBox="0 0 448 512"><path d="M400 32H48A48 48 0 0 0 0 80v352a48 48 0 0 0 48 48h352a48 48 0 0 0 48-48V80a48 48 0 0 0-48-48zm-16.39 307.37l-15 65A15 15 0 0 1 354 416C194 416 64 286.29 64 126a15.7 15.7 0 0 1 11.63-14.61l65-15A18.23 18.23 0 0 1 144 96a16.27 16.27 0 0 1 13.79 9.09l30 70A17.9 17.9 0 0 1 189 181a17 17 0 0 1-5.5 11.61l-37.89 31a231.91 231.91 0 0 0 110.78 110.78l31-37.89A17 17 0 0 1 299 291a17.85 17.85 0 0 1 5.91 1.21l70 30A16.25 16.25 0 0 1 384 336a17.41 17.41 0 0 1-.39 3.37z"/></svg>
                    <a href="{{ config('site.phone_href') }}" class="hover:text-white transition">{{ config('site.phone') }}</a>
                </li>
                <li class="flex gap-3">
                    <svg class="w-5 h-5 shrink-0 text-y2b-accent" fill="currentColor" viewBox="0 0 512 512"><path d="M464 64H48C21.49 64 0 85.49 0 112v288c0 26.51 21.49 48 48 48h416c26.51 0 48-21.49 48-48V112c0-26.51-21.49-48-48-48zm0 48v40.805c-22.422 18.259-58.168 46.651-134.587 106.49-16.841 13.247-50.201 45.072-73.413 44.701-23.208.375-56.579-31.459-73.413-44.701C106.18 199.465 70.425 171.067 48 152.805V112h416zM48 400V214.398c22.914 18.251 55.409 43.862 104.938 82.646 21.857 17.205 60.134 55.186 103.062 54.955 42.717.231 80.509-37.199 103.053-54.947 49.528-38.783 82.032-64.401 104.947-82.653V400H48z"/></svg>
                    <a href="mailto:{{ config('site.email') }}" class="hover:text-white transition">{{ config('site.email') }}</a>
                </li>
            </ul>
        </div>
    </div>

    <div class="border-t border-white/10 py-4 text-center text-sm text-gray-500">
        Copyright &copy; {{ date('Y') }}, All Rights Reserved.
    </div>
</footer>
