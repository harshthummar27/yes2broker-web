@props(['usps' => []])

<section class="py-12 {{ $attributes->get('class', 'bg-gray-50') }}">
    <div class="max-w-7xl mx-auto px-4">
        <div id="usp-carousel" class="usp-carousel scroll-smooth">
            @foreach($usps as $usp)
                <div class="usp-carousel-item">
                    <x-usp-card :usp="$usp" class="h-full" />
                </div>
            @endforeach
        </div>

        @if(count($usps) > 1)
            <div class="flex md:hidden items-center justify-center gap-2 mt-4" data-usp-carousel-dots>
                @foreach($usps as $index => $usp)
                    <button
                        type="button"
                        class="w-2.5 h-2.5 rounded-full transition {{ $index === 0 ? 'bg-y2b-primary' : 'bg-gray-300' }}"
                        aria-label="Go to {{ $usp['title'] }}"
                        data-usp-carousel-dot="{{ $index }}"
                    ></button>
                @endforeach
            </div>
        @endif
    </div>
</section>
