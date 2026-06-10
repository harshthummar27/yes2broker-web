@extends('layouts.app')

@section('title', 'List Your Property')

@section('content')
<x-page-banner title="List Your Property" />

{{-- Intro --}}
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 grid lg:grid-cols-2 gap-12 items-center">
        <div>
            <h2 class="text-3xl font-bold text-y2b-primary mb-6">Your Space, Our Showcase</h2>
            <div class="space-y-4 text-gray-600 leading-relaxed mb-8">
                <p>
                    Ready to sell or rent your property? Partner with us to reach serious buyers and tenants through our trusted real estate platform. We offer easy listing, verified leads, and expert support to help you close faster. Whether you're a homeowner, agent, or developer, we make your listing work harder for you.
                </p>
                <p>
                    Turn your property into an opportunity. Whether you're a homeowner, agent, or builder, we provide a smart, simple way to list your property. With verified leads, maximum visibility, and end-to-end support, getting the right buyer or tenant is now easier than ever.
                </p>
            </div>

            <h4 class="text-lg font-bold text-y2b-primary mb-4">Types of Properties You Can List :</h4>
            <ul class="space-y-3">
                @foreach($listableTypes as $type)
                    <li class="flex items-start gap-3 text-gray-700">
                        <svg class="w-5 h-5 text-y2b-primary shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 512 512"><path d="M173.898 439.404l-166.4-166.4c-9.997-9.997-9.997-26.206 0-36.204l36.203-36.204c9.997-9.998 26.207-9.998 36.204 0L192 312.69 432.095 72.596c9.997-9.997 26.207-9.997 36.204 0l36.203 36.204c9.997 9.997 9.997 26.206 0 36.204l-294.4 294.401c-9.998 9.997-26.207 9.997-36.204-.001z"/></svg>
                        {{ $type }}
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="flex justify-center">
            <img src="{{ config('site.list_property_image') }}" alt="List Your Property"
                 class="rounded-2xl shadow-lg max-w-md w-full">
        </div>
    </div>
</section>

{{-- Listing Form --}}
<section class="py-16 bg-gray-50">
    <div class="max-w-4xl mx-auto px-4">
        <h3 class="text-2xl font-bold text-y2b-primary text-center mb-10">Enter Your Property Details</h3>

        <form action="#" method="POST" enctype="multipart/form-data"
              class="bg-white rounded-2xl shadow-lg p-6 md:p-10 space-y-6">
            @csrf

            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">Name</label>
                    <input type="text" id="name" name="name" required placeholder="John Doe"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 outline-none focus:border-y2b-primary focus:ring-1 focus:ring-y2b-primary">
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                    <input type="email" id="email" name="email" required placeholder="John Doe@gmail.com"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 outline-none focus:border-y2b-primary focus:ring-1 focus:ring-y2b-primary">
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1.5">Number</label>
                    <input type="tel" id="phone" name="phone" required placeholder="+91 20152 265412"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 outline-none focus:border-y2b-primary focus:ring-1 focus:ring-y2b-primary">
                </div>
                <div>
                    <label for="alternate_phone" class="block text-sm font-medium text-gray-700 mb-1.5">Alternate Number</label>
                    <input type="tel" id="alternate_phone" name="alternate_phone" required placeholder="+91 20152 265412"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 outline-none focus:border-y2b-primary focus:ring-1 focus:ring-y2b-primary">
                </div>
            </div>

            <div>
                <label for="property_title" class="block text-sm font-medium text-gray-700 mb-1.5">Property Title</label>
                <input type="text" id="property_title" name="property_title" required placeholder="Oshino Property Stars"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2.5 outline-none focus:border-y2b-primary focus:ring-1 focus:ring-y2b-primary">
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1.5">Type</label>
                    <select id="type" name="type"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 outline-none focus:border-y2b-primary focus:ring-1 focus:ring-y2b-primary bg-white">
                        @foreach($propertyTypes as $type)
                            <option value="{{ $type }}">{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1.5">Status</label>
                    <select id="status" name="status"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 outline-none focus:border-y2b-primary focus:ring-1 focus:ring-y2b-primary bg-white">
                        @foreach($listingStatuses as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label for="owner_type" class="block text-sm font-medium text-gray-700 mb-1.5">Specify Your Self</label>
                <select id="owner_type" name="owner_type"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 outline-none focus:border-y2b-primary focus:ring-1 focus:ring-y2b-primary bg-white">
                    @foreach($ownerTypes as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="remark" class="block text-sm font-medium text-gray-700 mb-1.5">Remark</label>
                <textarea id="remark" name="remark" rows="4"
                          class="w-full border border-gray-300 rounded-lg px-4 py-2.5 outline-none focus:border-y2b-primary focus:ring-1 focus:ring-y2b-primary resize-none"></textarea>
            </div>

            <div>
                <label for="property_address" class="block text-sm font-medium text-gray-700 mb-1.5">Property Address</label>
                <input type="text" id="property_address" name="property_address"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2.5 outline-none focus:border-y2b-primary focus:ring-1 focus:ring-y2b-primary">
            </div>

            <div>
                <label for="property_image" class="block text-sm font-medium text-gray-700 mb-1.5">Property Image</label>
                <input type="file" id="property_image" name="property_image[]" multiple accept="image/*"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2.5 outline-none focus:border-y2b-primary file:mr-4 file:py-1 file:px-3 file:rounded file:border-0 file:bg-y2b-light file:text-y2b-primary file:font-medium">
            </div>

            <div class="flex items-start gap-3">
                <input type="checkbox" id="verification" name="verification" required
                       class="mt-1 w-4 h-4 rounded border-gray-300 text-y2b-primary focus:ring-y2b-primary">
                <label for="verification" class="text-sm text-gray-600">
                    Click Below To Submit Your Details For Verifications
                </label>
            </div>

            <button type="submit"
                    class="w-full bg-y2b-primary hover:bg-y2b-primary-dark text-white font-semibold px-8 py-3.5 rounded-lg transition">
                Submit Now
            </button>
        </form>
    </div>
</section>
@endsection
