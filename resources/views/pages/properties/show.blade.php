@extends('layouts.app')

@section('title', $property['title'])
@section('meta_description', Str::limit($property['description'], 155))

@section('content')
<x-page-banner :title="$property['title']" />

{{-- Gallery + Summary Sidebar --}}
<section class="py-10 md:py-14 bg-white">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid lg:grid-cols-5 gap-8 lg:gap-10 items-start">
            {{-- Gallery --}}
            <div class="lg:col-span-3" id="property-gallery">
                <div class="property-gallery-main rounded-2xl overflow-hidden bg-gray-100 shadow-md mb-4">
                    <img id="property-gallery-main"
                         src="{{ $property['gallery'][0] }}"
                         alt="{{ $property['title'] }}"
                         class="w-full h-72 md:h-[420px] object-cover">
                </div>

                @if(count($property['gallery']) > 1)
                    <div class="property-gallery-thumbs flex gap-3 overflow-x-auto pb-1">
                        @foreach($property['gallery'] as $index => $image)
                            <button type="button"
                                    data-property-gallery-thumb="{{ $index }}"
                                    data-image="{{ $image }}"
                                    class="property-gallery-thumb shrink-0 w-20 h-20 md:w-24 md:h-24 rounded-lg overflow-hidden border-2 transition {{ $index === 0 ? 'border-y2b-primary' : 'border-transparent opacity-70 hover:opacity-100' }}"
                                    aria-label="View image {{ $index + 1 }}">
                                <img src="{{ $image }}" alt="" class="w-full h-full object-cover">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Summary Card --}}
            <aside class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden lg:sticky lg:top-24">
                    <img src="{{ $property['image'] }}" alt="{{ $property['title'] }}"
                         class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h2 class="text-2xl font-bold text-y2b-primary mb-3">{{ $property['title'] }}</h2>

                        <p class="text-gray-500 text-sm flex items-start gap-2 mb-5">
                            <svg class="w-4 h-4 shrink-0 mt-0.5 text-y2b-accent" fill="currentColor" viewBox="0 0 384 512"><path d="M172.268 501.67C26.97 291.031 0 269.413 0 192 0 85.961 85.961 0 192 0s192 85.961 192 192c0 77.413-26.97 99.031-172.268 309.67-9.535 13.774-29.93 13.773-39.464 0zM192 272c44.183 0 80-35.817 80-80s-35.817-80-80-80-80 35.817-80 80 35.817 80 80 80z"/></svg>
                            {{ $property['location'] }}
                        </p>

                        <div class="mb-5">
                            <h3 class="text-sm font-bold text-y2b-primary uppercase tracking-wide mb-3">Features:</h3>
                            <ul class="text-sm text-gray-600 space-y-2">
                                <li class="flex items-center gap-2">
                                    <span class="text-y2b-accent">🛏</span> {{ $property['bhk'] }}
                                </li>
                                <li class="flex items-center gap-2">
                                    <span class="text-y2b-accent">📄</span> Project Area: {{ $property['area'] }}
                                </li>
                                <li class="flex items-center gap-2">
                                    <span class="text-y2b-accent">📏</span> Possession Date: {{ $property['possession'] }}
                                </li>
                            </ul>
                        </div>

                        <p class="text-xl font-bold text-y2b-primary mb-5">{{ $property['price'] }}</p>

                        <button type="button"
                                data-open-property-inquiry
                                class="w-full bg-y2b-primary hover:bg-y2b-primary-dark text-white font-semibold px-6 py-3 rounded-lg transition text-sm mb-3">
                            Download Brochure
                        </button>

                        <p class="text-center text-xs text-gray-400 uppercase tracking-wide">Project Brochure</p>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</section>

{{-- Description --}}
<section class="py-10 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4">
        <h2 class="text-2xl font-bold text-y2b-primary mb-4">Descriptions</h2>
        <p class="text-gray-600 leading-relaxed max-w-4xl">{{ $property['description'] }}</p>
    </div>
</section>

{{-- Overview --}}
<section class="py-10 bg-white">
    <div class="max-w-7xl mx-auto px-4">
        <h2 class="text-2xl font-bold text-y2b-primary mb-8">Overview</h2>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach([
                ['label' => 'Project Area', 'value' => $property['overview']['project_area']],
                ['label' => 'Configurations & Sizes', 'value' => $property['overview']['configurations']],
                ['label' => 'Project Size', 'value' => $property['overview']['project_size']],
                ['label' => 'Launch Date', 'value' => $property['overview']['launch_date']],
                ['label' => 'Price Range', 'value' => $property['overview']['price_range']],
                ['label' => 'Possession Date', 'value' => $property['overview']['possession']],
            ] as $item)
                <div class="property-overview-card flex gap-4 p-5 rounded-xl border border-gray-100 bg-gray-50">
                    <div class="w-11 h-11 shrink-0 rounded-full bg-y2b-primary/10 text-y2b-primary flex items-center justify-center">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 512 512"><path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 mb-1">{{ $item['label'] }}</p>
                        <p class="text-sm font-semibold text-y2b-primary">{{ $item['value'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-5 p-5 rounded-xl border border-y2b-light bg-y2b-light/30">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 mb-1">RERA ID</p>
            <p class="text-sm font-medium text-y2b-primary break-words">{{ $property['overview']['rera_id'] }}</p>
        </div>
    </div>
</section>

{{-- Amenities --}}
<section class="py-10 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4">
        <h2 class="text-2xl font-bold text-y2b-primary mb-8">Additional Amenities</h2>
        <div class="grid md:grid-cols-2 gap-x-12 gap-y-4">
            @foreach($property['amenities'] as $amenity)
                <div class="flex items-start gap-3 text-gray-700 text-sm">
                    <span class="w-2 h-2 rounded-full bg-y2b-accent shrink-0 mt-2"></span>
                    {{ $amenity }}
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Map --}}
<section class="py-10 bg-white">
    <div class="max-w-7xl mx-auto px-4">
        <h2 class="text-2xl font-bold text-y2b-primary mb-6">Project Location</h2>
        <div class="rounded-2xl overflow-hidden shadow-md border border-gray-100">
            <iframe
                loading="lazy"
                src="{{ $property['map_embed_url'] }}"
                title="{{ $property['title'] }} location"
                aria-label="{{ $property['location'] }}"
                class="w-full h-72 md:h-96 border-0"
                allowfullscreen
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
    </div>
</section>

{{-- FAQ --}}
<section class="py-10 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4">
        <h2 class="text-2xl font-bold text-y2b-primary mb-8">Frequently Asked Questions</h2>
        <div class="max-w-3xl space-y-3" id="property-faq-accordion">
            @foreach($property['faqs'] as $index => $faq)
                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
                    <button type="button"
                            data-property-faq-trigger="{{ $index }}"
                            class="w-full flex items-center justify-between px-5 py-4 text-left font-semibold text-y2b-primary hover:bg-gray-50 transition {{ $index === 0 ? 'bg-gray-50' : '' }}">
                        <span>{{ $faq['question'] }}</span>
                        <svg class="w-5 h-5 shrink-0 text-y2b-accent transition-transform {{ $index === 0 ? 'rotate-180' : '' }}"
                             data-property-faq-icon="{{ $index }}" fill="currentColor" viewBox="0 0 448 512">
                            <path d="M207.029 381.476L12.686 187.132c-9.373-9.373-9.373-24.569 0-33.941l22.667-22.667c9.357-9.357 24.522-9.375 33.901-.04L224 284.505l154.745-154.021c9.379-9.335 24.544-9.317 33.901.04l22.667 22.667c9.373 9.373 9.373 24.569 0 33.941L240.971 381.476c-9.373 9.372-24.569 9.372-33.942 0z"/>
                        </svg>
                    </button>
                    <div data-property-faq-panel="{{ $index }}"
                         class="px-5 pb-4 text-gray-600 text-sm leading-relaxed {{ $index === 0 ? '' : 'hidden' }}">
                        {{ $faq['answer'] }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- CTA + Inquiry Form --}}
<section class="py-14 md:py-16 bg-y2b-primary text-white">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid lg:grid-cols-2 gap-10 lg:gap-14 items-start">
            <div>
                <h2 class="text-2xl md:text-3xl font-bold mb-4">Interested in This Property? Let's Talk!</h2>
                <p class="text-blue-100 leading-relaxed mb-6">
                    Buying a property is a big decision — and we're here to make it easier for you. Whether you're planning to invest, buy your dream home, or explore financing options, our expert advisors are just a message away. Share your details using the form and we'll connect you with personalized guidance, transparent pricing, and complete project information.
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="{{ config('site.phone_href') }}"
                       class="inline-flex items-center gap-2 bg-y2b-accent hover:bg-yellow-500 text-y2b-primary font-bold px-6 py-3 rounded-lg transition text-sm">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 512 512"><path d="M497.39 361.8l-112-48a24 24 0 0 0-28 6.9l-49.6 60.6A370.66 370.66 0 0 1 130.6 204.11l60.6-49.6a23.94 23.94 0 0 0 6.9-28l-48-112A24.16 24.16 0 0 0 122.6.61l-104 24A24 24 0 0 0 0 48c0 256.5 207.9 464 464 464a24 24 0 0 0 23.4-18.6l24-104a24.29 24.29 0 0 0-14.01-27.6z"/></svg>
                        Get in Touch
                    </a>
                    <button type="button"
                            data-open-property-inquiry
                            class="inline-flex items-center gap-2 border border-white/40 hover:bg-white/10 text-white font-semibold px-6 py-3 rounded-lg transition text-sm">
                        Virtual Tour
                    </button>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-2xl p-6 md:p-8 text-gray-800" id="property-inquiry-form">
                <h3 class="text-xl font-bold text-y2b-primary mb-1">Get in Touch</h3>
                <p class="text-gray-500 text-sm mb-6">Share your details for {{ $property['title'] }}</p>

                <form action="#" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="property" value="{{ $property['title'] }}">

                    <div>
                        <label for="inquiry_name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                        <input type="text" id="inquiry_name" name="name" required placeholder="Your Name"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm outline-none focus:border-y2b-primary focus:ring-1 focus:ring-y2b-primary">
                    </div>

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label for="inquiry_email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" id="inquiry_email" name="email" required placeholder="Email"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm outline-none focus:border-y2b-primary focus:ring-1 focus:ring-y2b-primary">
                        </div>
                        <div>
                            <label for="inquiry_phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                            <input type="tel" id="inquiry_phone" name="phone" required placeholder="Phone Number"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm outline-none focus:border-y2b-primary focus:ring-1 focus:ring-y2b-primary">
                        </div>
                    </div>

                    <div>
                        <label for="inquiry_message" class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                        <textarea id="inquiry_message" name="message" rows="3" placeholder="I'm interested in {{ $property['title'] }}"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm outline-none focus:border-y2b-primary focus:ring-1 focus:ring-y2b-primary resize-none"></textarea>
                    </div>

                    <button type="submit"
                            class="bg-y2b-primary hover:bg-y2b-primary-dark text-white font-semibold px-6 py-2.5 rounded-lg transition text-sm">
                        Submit
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

{{-- Related Properties --}}
@if(count($relatedProperties) > 0)
<section class="py-14 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl font-bold text-y2b-primary">Similar Properties</h2>
            <a href="{{ route('properties.index') }}"
               class="text-sm font-semibold text-y2b-primary hover:text-y2b-accent transition">
                View All
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
            @foreach($relatedProperties as $related)
                <x-property-card-grid :property="$related" />
            @endforeach
        </div>
    </div>
</section>
@endif

<x-property-inquiry-modal :property-title="$property['title']" />
@endsection
