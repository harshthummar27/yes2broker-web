<a href="{{ route('properties.index') }}"
   {{ $attributes->merge(['class' => 'listing-dream-banner group block rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow']) }}>
    <div class="listing-dream-banner-inner relative flex items-center gap-4 md:gap-8 px-5 py-6 sm:px-8 sm:py-8 md:px-12 md:py-10">
        <div class="hidden sm:block shrink-0 w-28 md:w-36">
            <img src="{{ site_asset(config('site.list_property_image')) }}" alt=""
                 class="w-full h-auto object-contain drop-shadow-md group-hover:scale-105 transition-transform duration-300"
                 loading="lazy">
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-lg sm:text-2xl md:text-3xl font-bold text-y2b-primary leading-snug">
                Your <span class="text-y2b-accent">Dream Property</span> just a click away
            </p>
            <p class="text-sm text-gray-600 mt-2 hidden md:block">
                Browse verified listings across Ahmedabad & Gandhinagar with zero brokerage.
            </p>
        </div>
        <span class="listing-dream-banner-btn shrink-0 inline-flex items-center justify-center bg-y2b-primary text-white font-semibold text-sm sm:text-base px-5 py-2.5 sm:px-6 sm:py-3 rounded-lg group-hover:bg-y2b-primary-dark transition whitespace-nowrap">
            Explore More
        </span>
    </div>
</a>
