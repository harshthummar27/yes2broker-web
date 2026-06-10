@props(['usps' => []])

<section class="py-12 {{ $attributes->get('class', 'bg-gray-50') }}">
    <div class="max-w-7xl mx-auto px-4 grid md:grid-cols-3 gap-6">
        @foreach($usps as $usp)
            <div class="usp-card rounded-xl p-6">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-full bg-y2b-primary/10 flex items-center justify-center shrink-0">
                        @if($usp['icon'] === 'home')
                            <svg class="w-6 h-6 text-y2b-primary" fill="currentColor" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                        @elseif($usp['icon'] === 'agreement')
                            <svg class="w-6 h-6 text-y2b-primary" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
                        @else
                            <svg class="w-6 h-6 text-y2b-primary" fill="currentColor" viewBox="0 0 24 24"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z"/></svg>
                        @endif
                    </div>
                    <div>
                        <h4 class="font-bold text-y2b-primary text-lg mb-2">{{ $usp['title'] }}</h4>
                        <p class="text-sm text-gray-600 leading-relaxed">{!! $usp['description'] !!}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>
