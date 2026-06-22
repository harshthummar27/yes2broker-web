@extends('layouts.app')

@section('title', 'About Us')

@section('content')
<x-page-banner title="About Us" />

{{-- About Company --}}
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 grid lg:grid-cols-2 gap-12 items-center">
        <div class="flex justify-center">
            <img src="{{ site_asset(config('site.about_check_image')) }}" alt="Yes2Broker"
                 class="rounded-2xl shadow-lg max-w-lg w-full">
        </div>
        <div>
            <span class="text-sm font-semibold text-y2b-primary uppercase tracking-wide">About Company</span>
            <h2 class="text-3xl font-bold text-y2b-primary mt-2 mb-4">Welcome to Yes2Broker</h2>
            <p class="text-gray-600 leading-relaxed mb-6">
                we simplify real estate for buyers, sellers, and investors. Based in Ahmedabad, we bring you a curated selection of properties backed by transparent processes, expert guidance, and unmatched local knowledge.
            </p>
            <ul class="space-y-3 mb-8">
                @foreach($highlights as $point)
                    <li class="flex items-center gap-3 text-gray-600">
                        <svg class="w-5 h-5 text-y2b-primary shrink-0" fill="currentColor" viewBox="0 0 512 512"><path d="M504 256C504 119 393 8 256 8S8 119 8 256s111 248 248 248 248-111 248-248zm-448 0c0-110.5 89.5-200 200-200s200 89.5 200 200-89.5 200-200 200S56 366.5 56 256zm72 20v-40c0-6.6 5.4-12 12-12h116v-67c0-10.7 12.9-16 20.5-8.5l99 99c4.7 4.7 4.7 12.3 0 17l-99 99c-7.6 7.6-20.5 2.2-20.5-8.5v-67H140c-6.6 0-12-5.4-12-12z"/></svg>
                        {{ $point }}
                    </li>
                @endforeach
            </ul>
            <a href="{{ route('properties.index') }}"
               class="inline-flex items-center gap-2 bg-y2b-primary hover:bg-y2b-primary-dark text-white font-semibold px-6 py-3 rounded transition">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                Explore More
            </a>
        </div>
    </div>
</section>

<x-usp-cards :usps="$usps" />

{{-- Newsletter --}}
<section class="py-12 bg-y2b-light/50">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <h3 class="text-xl md:text-2xl font-bold text-y2b-primary mb-4">
            Subscribe our Newsletter and Get latest property update.
        </h3>
        <form action="{{ route('enquiry.newsletter') }}" method="POST" class="flex flex-col sm:flex-row gap-3 max-w-lg mx-auto">
            @csrf
            <input type="hidden" name="source" value="About Page Newsletter">
            <x-form-email-input name="email" placeholder="Enter your email"
                   class="flex-1 px-4 py-3 rounded-lg border border-gray-300 outline-none focus:border-y2b-primary" />
            <button type="submit"
                    class="bg-y2b-primary hover:bg-y2b-primary-dark text-white font-semibold px-6 py-3 rounded-lg transition whitespace-nowrap">
                Join Our Newsletter!
            </button>
        </form>
    </div>
</section>

{{-- Our Services --}}
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-12">
            <span class="inline-flex items-center gap-2 text-sm font-semibold text-y2b-primary uppercase tracking-wide mb-3">
                <span class="w-2 h-2 rounded-full bg-y2b-accent"></span>
                Our Services
            </span>
            <h2 class="text-3xl font-bold text-y2b-primary">Tailored services designed to meet your real estate needs.</h2>
        </div>

        <div class="grid md:grid-cols-3 gap-8 mb-10">
            @foreach($services as $service)
                <div class="bg-gray-50 rounded-xl p-6 border border-gray-100 hover:shadow-lg transition">
                    <div class="w-14 h-14 rounded-full bg-y2b-primary/10 flex items-center justify-center mb-4">
                        @if($service['icon'] === 'buy')
                            <svg class="w-7 h-7 text-y2b-primary" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                        @elseif($service['icon'] === 'sell')
                            <svg class="w-7 h-7 text-y2b-primary" fill="currentColor" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                        @else
                            <svg class="w-7 h-7 text-y2b-primary" fill="currentColor" viewBox="0 0 24 24"><path d="M12 7V3H2v18h20V7H12zM6 19H4v-2h2v2zm0-4H4v-2h2v2zm0-4H4V9h2v2zm0-4H4V5h2v2zm4 12H8v-2h2v2zm0-4H8v-2h2v2zm0-4H8V9h2v2zm0-4H8V5h2v2zm10 12h-8v-2h2v-2h-2v-2h2v-2h-2V9h8v10zm-2-8h-2v2h2v-2zm0 4h-2v2h2v-2z"/></svg>
                        @endif
                    </div>
                    <h4 class="font-bold text-y2b-primary text-lg mb-3">{{ $service['title'] }}</h4>
                    <p class="text-sm text-gray-600 leading-relaxed">{{ $service['description'] }}</p>
                </div>
            @endforeach
        </div>

        <div class="bg-y2b-primary rounded-xl p-8 text-center text-white">
            <h4 class="text-xl font-bold mb-2">We provide the best service for you, check out all our services.</h4>
            <p class="text-blue-100 max-w-2xl mx-auto">
                From buying and selling to consultation and legal support, we offer complete real estate solutions tailored to your needs.
            </p>
        </div>
    </div>
</section>

{{-- How It Works --}}
<section class="py-16 bg-y2b-primary text-white">
    <div class="max-w-7xl mx-auto px-4">
        <div class="max-w-3xl">
            <span class="inline-flex items-center gap-2 text-sm font-semibold uppercase tracking-wide text-y2b-accent mb-3">
                <span class="w-2 h-2 rounded-full bg-y2b-accent"></span>
                How It Work
            </span>
            <h2 class="text-3xl font-bold mb-4">The best home, perfectly suited for you.</h2>
            <p class="text-blue-100 leading-relaxed mb-8">
                We help you find your ideal property in Ahmedabad—whether you're buying, renting, or investing. With a seamless process and expert guidance at every step, your perfect home is just a few clicks away.
            </p>

            <div class="space-y-3" id="how-it-works-accordion">
                @foreach($howItWorks as $index => $step)
                    <div class="border border-white/20 rounded-lg overflow-hidden">
                        <button type="button"
                                data-accordion-trigger="{{ $index }}"
                                class="w-full flex items-center justify-between px-5 py-4 text-left font-semibold hover:bg-white/5 transition {{ $index === 0 ? 'bg-white/10' : '' }}">
                            <span>{{ $step['title'] }}</span>
                            <svg class="w-5 h-5 shrink-0 transition-transform {{ $index === 0 ? 'rotate-180' : '' }}"
                                 data-accordion-icon="{{ $index }}" fill="currentColor" viewBox="0 0 448 512">
                                <path d="M207.029 381.476L12.686 187.132c-9.373-9.373-9.373-24.569 0-33.941l22.667-22.667c9.357-9.357 24.522-9.375 33.901-.04L224 284.505l154.745-154.021c9.379-9.335 24.544-9.317 33.901.04l22.667 22.667c9.373 9.373 9.373 24.569 0 33.941L240.971 381.476c-9.373 9.372-24.569 9.372-33.942 0z"/>
                            </svg>
                        </button>
                        <div data-accordion-panel="{{ $index }}"
                             class="px-5 pb-4 text-blue-100 text-sm leading-relaxed {{ $index === 0 ? '' : 'hidden' }}">
                            {{ $step['description'] }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- Popular Locations + Partners --}}
<section class="py-14 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-y2b-primary">Popular Locations</h2>
            <h2 class="text-xl text-gray-500 mt-2">Our Network of Trusted Partners</h2>
        </div>

        <div class="flex flex-wrap justify-center gap-3 mb-12">
            @foreach($locations as $location)
                <a href="{{ route('properties.index', ['area' => strtolower($location)]) }}"
                   class="inline-block bg-y2b-light text-y2b-primary border border-[#e2eaff] font-medium text-sm px-5 py-2 rounded-full hover:bg-y2b-primary hover:text-white hover:border-y2b-primary transition">
                    {{ $location }}
                </a>
            @endforeach
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

@endsection
