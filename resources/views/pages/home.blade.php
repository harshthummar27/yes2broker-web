@extends('layouts.app')

@section('title', 'Home')

@section('content')
{{-- Hero section with rounded video card + bottom search filter --}}
<section class="py-3 md:py-4 px-2 sm:px-4 md:px-6 w-full">
    <div class="relative w-full h-[420px] sm:h-[480px] md:h-[520px] lg:h-[560px] rounded-xl md:rounded-2xl overflow-hidden shadow-xl flex items-end justify-center pb-6 sm:pb-8 md:pb-10">
        {{-- Video background --}}
        <video autoplay muted loop playsinline class="absolute inset-0 w-full h-full object-cover">
            <source src="{{ site_asset(config('site.hero_video')) }}" type="video/mp4">
        </video>

        {{-- Gradient overlay for bottom search bar readability --}}
        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent pointer-events-none"></div>

        {{-- Floating Search Bar at Bottom with margin --}}
        <div class="relative z-10 w-full max-w-5xl 2xl:max-w-6xl px-3 sm:px-6 mb-2 sm:mb-3 md:mb-4">
            <x-property-search
                :cities="$cities"
                :property-types="$propertyTypes"
                :budgets="$budgets"
                :default-city="$defaultCity" />
        </div>
    </div>
</section>

{{-- USP Cards --}}
<x-usp-cards :usps="$usps" />

{{-- Trending Properties --}}
<section class="py-10 md:py-14 bg-white overflow-x-hidden">
    <div class="max-w-7xl 2xl:max-w-[1480px] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between gap-4 mb-6 md:mb-8">
            <h2 class="text-xl md:text-2xl font-bold text-gray-900">Our Listing</h2>
            <a href="{{ route('properties.index') }}"
               class="shrink-0 text-sm font-semibold text-y2b-primary border border-y2b-primary px-4 py-1.5 rounded-full hover:bg-y2b-primary hover:text-white transition">
                See All
            </a>
        </div>

        <div class="trending-carousel-wrap relative">
            <button id="carousel-prev" type="button" aria-label="Previous"
                    class="trending-carousel-arrow trending-carousel-arrow-prev hidden md:flex">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 1000 1000"><path d="M646 125C629 125 613 133 604 142L308 442C296 454 292 471 292 487 292 504 296 521 308 533L604 854C617 867 629 875 646 875 663 875 679 871 692 858 704 846 713 829 713 812 713 796 708 779 692 767L438 487 692 225C700 217 708 204 708 187 708 171 704 154 692 142 675 129 663 125 646 125Z"/></svg>
            </button>

            <div id="property-carousel" class="trending-carousel property-mobile-track scroll-smooth">
                @forelse($trendingProperties as $property)
                    <x-property-trending-card :property="$property" />
                @empty
                    <div class="col-span-full w-full text-center py-12 text-gray-500">
                        <p>No properties available yet.</p>
                        <a href="{{ route('properties.index') }}" class="text-y2b-primary font-semibold hover:text-y2b-accent mt-2 inline-block">
                            Browse properties
                        </a>
                    </div>
                @endforelse
            </div>

            <button id="carousel-next" type="button" aria-label="Next"
                    class="trending-carousel-arrow trending-carousel-arrow-next hidden md:flex">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 1000 1000"><path d="M696 533C708 521 713 504 713 487 713 471 708 454 696 446L400 146C388 133 375 125 354 125 338 125 325 129 313 142 300 154 292 171 292 187 292 204 296 221 308 233L563 492 304 771C292 783 288 800 288 817 288 833 296 850 308 863 321 871 338 875 354 875 371 875 388 867 400 854L696 533Z"/></svg>
            </button>

            @if(count($trendingProperties) > 2)
                <div class="flex md:hidden items-center justify-center gap-2 mt-4" data-trending-carousel-dots>
                    @for($dot = 0; $dot < (int) ceil(count($trendingProperties) / 2); $dot++)
                        <button
                            type="button"
                            class="w-2.5 h-2.5 rounded-full transition {{ $dot === 0 ? 'bg-y2b-primary' : 'bg-gray-300' }}"
                            aria-label="Go to slide {{ $dot + 1 }}"
                            data-trending-carousel-dot="{{ $dot }}"
                        ></button>
                    @endfor
                </div>
            @endif
        </div>

        <x-listing-dream-banner class="mt-8 md:mt-10" :items="$dreamPromoItems" />
        @if($hasFormPromoBanner ?? false)
            <x-banner-promo-modal />
        @endif
    </div>
</section>

{{-- About Company --}}
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl 2xl:max-w-[1480px] mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
        <div class="flex justify-center">
            <div class="about-company-image-box rounded-2xl shadow-xl p-8 md:p-10 flex items-center justify-center w-full max-w-md min-h-[280px] md:min-h-[360px]">
                <img src="{{ site_asset(config('site.about_image')) }}" alt="About Yes2Broker"
                     class="w-full max-h-72 md:max-h-80 object-contain">
            </div>
        </div>
        <div>
            <span class="text-sm font-semibold text-y2b-primary uppercase tracking-wide">About Company</span>
            <h2 class="text-3xl font-bold text-y2b-primary mt-2 mb-4">Welcome to Yes2Broker</h2>
            <p class="text-gray-600 leading-relaxed mb-6">
                we simplify real estate for buyers, sellers, and investors. Based in Ahmedabad, we bring you a curated selection of properties backed by transparent processes, expert guidance, and unmatched local knowledge.
            </p>
            <ul class="space-y-3 mb-8">
                @foreach([
                    'Personalized property guidance for every client',
                    'Get early access to exclusive listings',
                    '24/7 support for urgent property needs',
                ] as $point)
                    <li class="flex items-center gap-3 text-gray-600">
                        <svg class="w-5 h-5 text-y2b-primary shrink-0" fill="currentColor" viewBox="0 0 512 512"><path d="M504 256C504 119 393 8 256 8S8 119 8 256s111 248 248 248 248-111 248-248zm-448 0c0-110.5 89.5-200 200-200s200 89.5 200 200-89.5 200-200 200S56 366.5 56 256zm72 20v-40c0-6.6 5.4-12 12-12h116v-67c0-10.7 12.9-16 20.5-8.5l99 99c4.7 4.7 4.7 12.3 0 17l-99 99c-7.6 7.6-20.5 2.2-20.5-8.5v-67H140c-6.6 0-12-5.4-12-12z"/></svg>
                        {{ $point }}
                    </li>
                @endforeach
            </ul>
            <a href="{{ route('about') }}"
               class="inline-flex items-center gap-2 bg-y2b-primary hover:bg-y2b-primary-dark text-white font-semibold px-6 py-3 rounded transition">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                Explore More
            </a>
        </div>
    </div>
</section>

{{-- Popular Locations + Partners --}}
<section class="py-14">
    <div class="max-w-7xl 2xl:max-w-[1480px] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-y2b-primary">Popular Locations</h2>
            <h2 class="text-xl text-gray-500 mt-2">Our Network of Trusted Partners</h2>
        </div>

        <div class="locality-tabs-wrap relative mb-12 -mx-4 px-4 md:mx-0 md:px-0">
            <div class="locality-tabs-fade locality-tabs-fade-left md:hidden" aria-hidden="true"></div>
            <div class="locality-tabs-fade locality-tabs-fade-right md:hidden" aria-hidden="true"></div>
            <div class="locality-tabs flex items-center gap-2 md:gap-3 overflow-x-auto pb-2 md:pb-0 md:flex-wrap md:justify-center md:overflow-visible scroll-smooth snap-x snap-mandatory md:snap-none">
                @foreach($locations as $location)
                    <a href="{{ route('properties.index', ['area' => strtolower($location)]) }}"
                       class="shrink-0 snap-start whitespace-nowrap bg-y2b-light text-y2b-primary border border-[#e2eaff] font-medium text-xs sm:text-sm px-4 py-2 md:px-5 md:py-2 rounded-full hover:bg-y2b-primary hover:text-white hover:border-y2b-primary transition">
                        {{ $location }}
                    </a>
                @endforeach
            </div>
        </div>

        <div class="overflow-hidden">
            <div class="partner-track flex gap-12 items-center w-max">
                @foreach(array_merge($partners, $partners) as $partner)
                    <img src="{{ $partner['logo'] }}" alt="{{ $partner['name'] }}"
                         class="h-16 md:h-20 w-auto object-contain grayscale hover:grayscale-0 transition">
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- Testimonials placeholder --}}
<section class="py-14 bg-y2b-light/40">
    <div class="max-w-7xl 2xl:max-w-[1480px] mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl font-bold text-y2b-primary">Testimonials</h2>
        <h2 class="text-xl text-gray-600 mt-2 mb-10">What Our Clients Say?</h2>
        <div class="bg-white rounded-xl shadow-md p-8 max-w-2xl mx-auto">
            <div class="flex justify-center gap-1 text-amber-500 mb-4">
                @for($i = 0; $i < 5; $i++)
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                @endfor
            </div>
            <p class="text-gray-600 italic leading-relaxed">
                "Yes2Broker made our home buying journey incredibly smooth. Their dedicated manager guided us through every step, and we got the best price for our dream apartment in Ahmedabad."
            </p>
            <p class="font-semibold text-y2b-primary mt-4">— Happy Home Buyer</p>
        </div>
    </div>
</section>

{{-- Localities --}}
@if(count($localities) > 0)
<section class="py-10 md:py-14 overflow-hidden">
    <div class="max-w-7xl 2xl:max-w-[1480px] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-6 md:mb-8">
            <h2 class="text-2xl md:text-3xl font-bold text-y2b-primary">Our Localities</h2>
            <p class="text-base md:text-xl text-gray-500 mt-2">Explore Properties By Localities</p>
        </div>

        <div class="locality-tabs-wrap relative mb-6 md:mb-8 -mx-4 px-4 md:mx-0 md:px-0">
            <div class="locality-tabs-fade locality-tabs-fade-left md:hidden" aria-hidden="true"></div>
            <div class="locality-tabs-fade locality-tabs-fade-right md:hidden" aria-hidden="true"></div>
            <div id="locality-tabs"
                 role="tablist"
                 aria-label="Property localities"
                 class="locality-tabs flex items-center gap-2 md:gap-3 overflow-x-auto pb-2 md:pb-0 md:flex-wrap md:justify-center md:overflow-visible scroll-smooth snap-x snap-mandatory md:snap-none">
                @foreach(array_keys($localities) as $index => $name)
                    <button type="button"
                            data-locality-tab="{{ $name }}"
                            class="locality-tab shrink-0 snap-start whitespace-nowrap px-4 py-2 md:px-6 rounded-full font-medium text-xs sm:text-sm transition {{ $index === 0 ? 'bg-y2b-primary text-white shadow-sm' : 'bg-y2b-light text-y2b-primary' }}">
                        {{ $name }}
                    </button>
                @endforeach
            </div>
        </div>

        @foreach($localities as $name => $properties)
            <div data-locality-panel="{{ $name }}"
                 class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 2xl:grid-cols-5 gap-2.5 sm:gap-3 {{ $loop->first ? '' : 'hidden' }}">
                @foreach($properties as $propertyItem)
                    <a href="{{ route('properties.show', $propertyItem['slug']) }}"
                       class="locality-property-link flex items-start gap-2.5 text-sm text-gray-700 hover:text-y2b-primary bg-white border border-gray-200 rounded-lg px-3 py-3 sm:px-4 sm:py-3 hover:border-y2b-primary hover:shadow-sm transition min-w-0">
                        <svg class="w-4 h-4 text-y2b-primary shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 384 512"><path d="M172.268 501.67C26.97 291.031 0 269.413 0 192 0 85.961 85.961 0 192 0s192 85.961 192 192c0 77.413-26.97 99.031-172.268 309.67-9.535 13.774-29.93 13.773-39.464 0zM192 272c44.183 0 80-35.817 80-80s-35.817-80-80-80-80 35.817-80 80 35.817 80 80 80z"/></svg>
                        <span class="line-clamp-2 leading-snug break-words">{{ $propertyItem['title'] }}</span>
                    </a>
                @endforeach
            </div>
        @endforeach
    </div>
</section>
@endif

{{-- Consultation + Featured carousel --}}
<section id="consultation" class="consultation-section py-16">
    <div class="max-w-7xl 2xl:max-w-[1480px] mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-2 gap-12 lg:gap-16 items-start">
        <div class="text-white">
            <h2 class="text-3xl font-bold">Book Your Appointment</h2>
            <h2 class="text-xl text-blue-200 mt-1 mb-8">Free Consultation</h2>

            <form action="{{ route('enquiry.consultation') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="source" value="Home Page">
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm mb-1">First Name</label>
                        <input type="text" name="first_name" required placeholder="First Name"
                               class="w-full bg-white/10 border border-white/30 rounded px-4 py-2.5 text-white placeholder:text-blue-200 outline-none focus:border-white">
                    </div>
                    <div>
                        <label class="block text-sm mb-1">Last Name</label>
                        <input type="text" name="last_name" required placeholder="Last Name"
                               class="w-full bg-white/10 border border-white/30 rounded px-4 py-2.5 text-white placeholder:text-blue-200 outline-none focus:border-white">
                    </div>
                </div>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm mb-1">Phone</label>
                        <x-form-mobile-input name="phone" placeholder="Mobile Number"
                               class="w-full bg-white/10 border border-white/30 rounded px-4 py-2.5 text-white placeholder:text-blue-200 outline-none focus:border-white" />
                    </div>
                    <div>
                        <label class="block text-sm mb-1">Email</label>
                        <x-form-email-input name="email" placeholder="Email"
                               class="w-full bg-white/10 border border-white/30 rounded px-4 py-2.5 text-white placeholder:text-blue-200 outline-none focus:border-white" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm mb-1">What are you looking for?</label>
                    <select name="looking_for" required
                            class="w-full bg-white/10 border border-white/30 rounded px-4 py-2.5 text-white outline-none focus:border-white">
                        @foreach($consultationOptions as $option)
                            <option value="{{ $option }}" class="text-gray-900">{{ $option }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm mb-1">Message</label>
                    <textarea name="message" rows="4" placeholder="Message"
                              class="w-full bg-white/10 border border-white/30 rounded px-4 py-2.5 text-white placeholder:text-blue-200 outline-none focus:border-white resize-none"></textarea>
                </div>
                <button type="submit"
                        class="bg-white hover:bg-y2b-light text-y2b-primary font-bold px-8 py-3 rounded transition">
                    Submit Request
                </button>
            </form>
        </div>

        <div class="relative">
            @foreach($featuredCarousel as $index => $featured)
                <div data-featured-slide="{{ $index }}"
                     class="bg-white rounded-xl overflow-hidden shadow-2xl {{ $index > 0 ? 'hidden' : '' }}">
                    @if(! empty($featured['slug']))
                        <a href="{{ route('properties.show', $featured['slug']) }}" class="block group">
                            <img src="{{ $featured['image'] }}" alt="{{ $featured['title'] }}"
                                 class="w-full h-56 object-cover group-hover:opacity-95 transition">
                        </a>
                    @else
                        <img src="{{ $featured['image'] }}" alt="{{ $featured['title'] }}"
                             class="w-full h-56 object-cover">
                    @endif
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-y2b-primary mb-3">
                            @if(! empty($featured['slug']))
                                <a href="{{ route('properties.show', $featured['slug']) }}" class="hover:text-y2b-accent transition">
                                    {{ $featured['title'] }}
                                </a>
                            @else
                                {{ $featured['title'] }}
                            @endif
                        </h3>
                        <p class="text-sm text-gray-500 mb-1">Address</p>
                        <p class="text-sm font-semibold text-gray-800 mb-3">{{ $featured['address'] }}</p>
                        @if(! empty($featured['postcode']))
                            <p class="text-sm text-gray-500 mb-1">Post Code</p>
                            <p class="text-sm font-semibold text-gray-800 mb-3">{{ $featured['postcode'] }}</p>
                        @endif
                        @if(! empty($featured['slug']))
                            <a href="{{ route('properties.show', $featured['slug']) }}"
                               class="inline-block text-sm font-semibold text-y2b-primary border border-y2b-primary px-4 py-2 rounded hover:bg-y2b-primary hover:text-white transition">
                                View Details
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
            <div class="flex justify-center gap-2 mt-4">
                @foreach($featuredCarousel as $index => $featured)
                    <button type="button" data-featured-dot="{{ $index }}"
                            class="w-2.5 h-2.5 rounded-full transition {{ $index === 0 ? 'bg-white' : 'bg-white/40' }}"
                            aria-label="Slide {{ $index + 1 }}"></button>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endsection
