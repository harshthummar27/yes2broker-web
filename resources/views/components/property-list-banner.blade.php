@props(['banner'])

<a href="{{ route('properties.show', $banner['slug']) }}"
   class="property-list-banner block rounded-xl overflow-hidden shadow-sm hover:shadow-md transition group">
    <div class="property-list-banner-inner flex flex-col sm:flex-row items-center gap-4 sm:gap-6 p-4 sm:p-5 md:p-6">
        <div class="shrink-0 w-full sm:w-40 md:w-48">
            <img src="{{ $banner['image'] }}" alt="{{ $banner['title'] }}"
                 class="w-full h-36 sm:h-28 md:h-32 object-cover rounded-lg shadow-md group-hover:scale-[1.02] transition-transform">
        </div>
        <div class="flex-1 text-center sm:text-left min-w-0">
            <p class="text-xs font-semibold uppercase tracking-wider text-y2b-primary/70 mb-1">Featured Project</p>
            <h4 class="text-lg md:text-xl font-bold text-y2b-primary mb-1 group-hover:text-y2b-accent transition">
                {{ $banner['title'] }}
            </h4>
            <p class="text-sm text-gray-600 mb-0.5">{{ $banner['subtitle'] ?? '' }}</p>
            <p class="text-sm font-medium text-gray-500 flex items-center justify-center sm:justify-start gap-1">
                <svg class="w-3.5 h-3.5 text-y2b-primary shrink-0" fill="currentColor" viewBox="0 0 384 512"><path d="M172.268 501.67C26.97 291.031 0 269.413 0 192 0 85.961 85.961 0 192 0s192 85.961 192 192c0 77.413-26.97 99.031-172.268 309.67-9.535 13.774-29.93 13.773-39.464 0zM192 272c44.183 0 80-35.817 80-80s-35.817-80-80-80-80 35.817-80 80 35.817 80 80 80z"/></svg>
                {{ $banner['location'] }}
            </p>
        </div>
        <div class="shrink-0 w-full sm:w-auto">
            <span class="property-list-btn property-list-btn-primary w-full sm:w-auto inline-flex justify-center">
                Explore Now
            </span>
        </div>
    </div>
</a>
