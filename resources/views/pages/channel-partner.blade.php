@extends('layouts.app')

@section('title', 'Become Channel Partner')

@section('content')
<x-page-banner title="Become Channel Partner" />

<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 grid lg:grid-cols-2 gap-12 items-start">
        {{-- Intro + Image --}}
        <div>
            <h2 class="text-3xl font-bold text-y2b-primary mb-6">Your Trusted Real Estate Growth Partner</h2>
            <p class="text-gray-600 leading-relaxed mb-8">
                Navigating the real estate market can be overwhelming — that's where <strong>Yes2Broker</strong> steps in. Based in <strong>Ahmedabad</strong>, we offer more than just listings — we deliver tailored property solutions with speed, precision, and professionalism. Whether you're buying, selling, or investing, we combine market intelligence, personalized service, and end-to-end support to make every move smooth and rewarding.
            </p>
            <img src="{{ site_asset(config('site.channel_partner_image')) }}" alt="Channel Partner"
                 class="rounded-2xl shadow-lg w-full object-cover">
        </div>

        {{-- Partner Form --}}
        <div class="bg-gray-50 rounded-2xl shadow-lg p-6 md:p-8 lg:sticky lg:top-8">
            <form action="{{ route('enquiry.channel-partner') }}" method="POST" class="space-y-5">
                @csrf
                <input type="hidden" name="source" value="Channel Partner Page">

                <div class="grid sm:grid-cols-2 gap-5">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">Name</label>
                        <input type="text" id="name" name="name" required placeholder="Jhon Doe"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 outline-none focus:border-y2b-primary focus:ring-1 focus:ring-y2b-primary bg-white">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                        <x-form-email-input id="email" name="email" placeholder="Jhon.doe@status.com"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 outline-none focus:border-y2b-primary focus:ring-1 focus:ring-y2b-primary bg-white" />
                    </div>
                </div>

                <div class="grid sm:grid-cols-2 gap-5">
                    <div>
                        <label for="mobile" class="block text-sm font-medium text-gray-700 mb-1.5">Mobile</label>
                        <x-form-mobile-input id="mobile" name="mobile" :required="false" placeholder="Mobile Number"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 outline-none focus:border-y2b-primary focus:ring-1 focus:ring-y2b-primary bg-white" />
                    </div>
                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700 mb-1.5">City</label>
                        <input type="text" id="city" name="city" placeholder="Ex. Ahmedabad"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 outline-none focus:border-y2b-primary focus:ring-1 focus:ring-y2b-primary bg-white">
                    </div>
                </div>

                <div>
                    <label for="full_address" class="block text-sm font-medium text-gray-700 mb-1.5">Full Address</label>
                    <input type="text" id="full_address" name="full_address"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 outline-none focus:border-y2b-primary focus:ring-1 focus:ring-y2b-primary bg-white">
                </div>

                <div>
                    <label for="company_name" class="block text-sm font-medium text-gray-700 mb-1.5">Your Company Name</label>
                    <input type="text" id="company_name" name="company_name" placeholder="Abc Infotech"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 outline-none focus:border-y2b-primary focus:ring-1 focus:ring-y2b-primary bg-white">
                </div>

                <div>
                    <label for="gst_number" class="block text-sm font-medium text-gray-700 mb-1.5">GST Number</label>
                    <input type="text" id="gst_number" name="gst_number" placeholder="ABCDE1234F"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 outline-none focus:border-y2b-primary focus:ring-1 focus:ring-y2b-primary bg-white">
                </div>

                <div>
                    <label for="remark" class="block text-sm font-medium text-gray-700 mb-1.5">Remark</label>
                    <textarea id="remark" name="remark" rows="4"
                              class="w-full border border-gray-300 rounded-lg px-4 py-2.5 outline-none focus:border-y2b-primary focus:ring-1 focus:ring-y2b-primary resize-none bg-white"></textarea>
                </div>

                <button type="submit"
                        class="w-full bg-y2b-primary hover:bg-y2b-primary-dark text-white font-semibold px-8 py-3.5 rounded-lg transition">
                    Submit
                </button>
            </form>
        </div>
    </div>
</section>
@endsection
