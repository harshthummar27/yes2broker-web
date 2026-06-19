@extends('layouts.app')

@section('title', 'Home Loan')

@section('content')
{{-- Hero + Form --}}
<section class="py-12 md:py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 grid lg:grid-cols-2 gap-10 lg:gap-14 items-start">
        <div>
            <h2 class="text-3xl md:text-4xl font-bold text-y2b-primary mb-6 leading-tight">
                Get Your Home Loan Fast &amp; Hassle‑Free
            </h2>
            <p class="text-gray-600 leading-relaxed">
                Navigating the real estate market can be overwhelming — that's where <strong>Yes2Broker</strong> steps in. Based in <strong>Ahmedabad</strong>, we offer more than just listings — we deliver tailored property solutions with speed, precision, and professionalism. Whether you're buying, selling, or investing, we combine market intelligence, personalized service, and end-to-end support to make every move smooth and rewarding.
            </p>
        </div>

        <div class="grid md:grid-cols-2 gap-6 items-start">
            <img src="{{ config('site.home_loan_image') }}" alt="Home Loan"
                 class="rounded-2xl shadow-lg w-full object-cover h-full min-h-[200px]">

            <div class="bg-gray-50 rounded-2xl shadow-lg p-5 md:p-6">
                <h2 class="text-xl font-bold text-y2b-primary mb-5">Let's get you started</h2>
                <form action="{{ route('enquiry.home-loan') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="source" value="Home Loan Page">
                    <input type="text" name="name" placeholder="Name"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 outline-none focus:border-y2b-primary focus:ring-1 focus:ring-y2b-primary bg-white text-sm">
                    <x-form-mobile-input name="phone" placeholder="Mobile Number"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 outline-none focus:border-y2b-primary focus:ring-1 focus:ring-y2b-primary bg-white text-sm" />
                    <x-form-email-input name="email" placeholder="Email"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 outline-none focus:border-y2b-primary focus:ring-1 focus:ring-y2b-primary bg-white text-sm" />
                    <input type="text" name="amount" placeholder="Amount"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 outline-none focus:border-y2b-primary focus:ring-1 focus:ring-y2b-primary bg-white text-sm">

                    <div>
                        <p class="text-sm font-semibold text-gray-800 mb-2">Have you Finalized Property?</p>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                <input type="radio" name="finalized" value="yes" class="text-y2b-primary focus:ring-y2b-primary">
                                Yes
                            </label>
                            <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                <input type="radio" name="finalized" value="no" class="text-y2b-primary focus:ring-y2b-primary" checked>
                                No
                            </label>
                        </div>
                    </div>

                    <label class="flex items-start gap-2 text-xs text-gray-600 cursor-pointer">
                        <input type="checkbox" name="terms" required class="mt-0.5 text-y2b-primary focus:ring-y2b-primary rounded">
                        I agree to Yes2Broker T&amp;C, Privacy Policy &amp; Cookie Policy.
                    </label>

                    <button type="submit"
                            class="w-full bg-y2b-primary hover:bg-y2b-primary-dark text-white font-semibold px-6 py-3 rounded-lg transition text-sm">
                        Check Availability
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

{{-- How It Works --}}
<section class="py-14 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4">
        <h2 class="text-2xl md:text-3xl font-bold text-y2b-primary text-center mb-10">How it works?</h2>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($howItWorks as $step)
                <div class="bg-white rounded-xl p-6 shadow-sm text-center hover:shadow-md transition">
                    <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-y2b-light flex items-center justify-center text-y2b-primary">
                        @if($step['icon'] === 'form')
                            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 384 512"><path d="M224 136V0H24C10.7 0 0 10.7 0 24v464c0 13.3 10.7 24 24 24h336c13.3 0 24-10.7 24-24V160H248c-13.2 0-24-10.8-24-24zm64 236c0 6.6-5.4 12-12 12H108c-6.6 0-12-5.4-12-12v-8c0-6.6 5.4-12 12-12h168c6.6 0 12 5.4 12 12v8zm0-64c0 6.6-5.4 12-12 12H108c-6.6 0-12-5.4-12-12v-8c0-6.6 5.4-12 12-12h168c6.6 0 12 5.4 12 12v8zm0-72v8c0 6.6-5.4 12-12 12H108c-6.6 0-12-5.4-12-12v-8c0-6.6 5.4-12 12-12h168c6.6 0 12 5.4 12 12zm96-114.1v6.1H256V0h6.1c6.4 0 12.5 2.5 17 7l97.9 98c4.5 4.5 7 10.6 7 16.9z"/></svg>
                        @elseif($step['icon'] === 'experts')
                            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 640 512"><path d="M96 224c35.3 0 64-28.7 64-64s-28.7-64-64-64-64 28.7-64 64 28.7 64 64 64zm448 0c35.3 0 64-28.7 64-64s-28.7-64-64-64-64 28.7-64 64 28.7 64 64 64zm32 32h-64c-17.6 0-33.5 7.1-45.1 18.6 40.3 22.1 68.9 62 75.1 109.4h66c17.7 0 32-14.3 32-32v-32c0-35.3-28.7-64-64-64zm-256 0c61.9 0 112-50.1 112-112S381.9 32 320 32 208 82.1 208 144s50.1 112 112 112zm76.8 32h-8.3c-20.8 10-43.9 16-68.5 16s-47.6-6-68.5-16h-8.3C179.6 288 128 339.6 128 403.2V432c0 26.5 21.5 48 48 48h288c26.5 0 48-21.5 48-48v-28.8c0-63.6-51.6-115.2-115.2-115.2zm-223.7-13.4C161.5 263.1 145.6 256 128 256H64c-35.3 0-64 28.7-64 64v32c0 17.7 14.3 32 32 32h65.9c6.3-47.4 34.9-87.3 75.2-109.4z"/></svg>
                        @elseif($step['icon'] === 'documents')
                            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 576 512"><path d="M570.69,236.27,512,184.44V48a16,16,0,0,0-16-16H432a16,16,0,0,0-16,16V99.67L314.78,10.3C308.5,4.61,296.53,0,288,0s-20.46,4.61-26.74,10.3l-256,226A18.27,18.27,0,0,0,0,248.2a18.64,18.64,0,0,0,4.09,10.71L25.5,282.7a21.14,21.14,0,0,0,12,5.3,21.67,21.67,0,0,0,10.69-4.11l15.9-14V480a32,32,0,0,0,32,32H480a32,32,0,0,0,32-32V269.88l15.91,14A21.94,21.94,0,0,0,538.63,288a20.89,20.89,0,0,0,11.87-5.31l21.41-23.81A21.64,21.64,0,0,0,576,248.19,21,21,0,0,0,570.69,236.27ZM288,176a64,64,0,1,1-64,64A64,64,0,0,1,288,176ZM400,448H176a16,16,0,0,1-16-16,96,96,0,0,1,96-96h64a96,96,0,0,1,96,96A16,16,0,0,1,400,448Z"/></svg>
                        @else
                            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 576 512"><path d="M271.06,144.3l54.27,14.3a8.59,8.59,0,0,1,6.63,8.1c0,4.6-4.09,8.4-9.12,8.4h-35.6a30,30,0,0,1-11.19-2.2c-5.24-2.2-11.28-1.7-15.3,2l-19,17.5a11.68,11.68,0,0,0-2.25,2.66,11.42,11.42,0,0,0,3.88,15.74,83.77,83.77,0,0,0,34.51,11.5V240c0,8.8,7.83,16,17.37,16h17.37c9.55,0,17.38-7.2,17.38-16V222.4c32.93-3.6,57.84-31,53.5-63-3.15-23-22.46-41.3-46.56-47.7L282.68,97.4a8.59,8.59,0,0,1-6.63-8.1c0-4.6,4.09-8.4,9.12-8.4h35.6A30,30,0,0,1,332,83.1c5.23,2.2,11.28,1.7,15.3-2l19-17.5A11.31,11.31,0,0,0,368.47,61a11.43,11.43,0,0,0-3.84-15.78,83.82,83.82,0,0,0-34.52-11.5V16c0-8.8-7.82-16-17.37-16H295.37C285.82,0,278,7.2,278,16V33.6c-32.89,3.6-57.85,31-53.51,63C227.63,119.6,247,137.9,271.06,144.3ZM565.27,328.1c-11.8-10.7-30.2-10-42.6,0L430.27,402a63.64,63.64,0,0,1-40,14H272a16,16,0,0,1,0-32h78.29c15.9,0,30.71-10.9,33.25-26.6a31.2,31.2,0,0,0,.46-5.46A32,32,0,0,0,352,320H192a117.66,117.66,0,0,0-74.1,26.29L71.4,384H16A16,16,0,0,0,0,400v96a16,16,0,0,0,16,16H372.77a64,64,0,0,0,40-14L564,377a32,32,0,0,0,1.28-48.9Z"/></svg>
                        @endif
                    </div>
                    <p class="text-sm text-gray-600 leading-relaxed">{{ $step['description'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Bank Partners --}}
<section class="py-14 bg-white">
    <div class="max-w-7xl mx-auto px-4">
        <h2 class="text-2xl md:text-3xl font-bold text-y2b-primary text-center mb-10">Top Home Loan Bank Partners</h2>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($bankPartners as $bank)
                <div class="border border-gray-200 rounded-xl p-5 flex flex-col items-center text-center hover:shadow-lg transition">
                    <img src="{{ $bank['logo'] }}" alt="{{ $bank['name'] }}"
                         class="h-12 object-contain mb-4">
                    <h3 class="font-bold text-y2b-primary text-sm mb-2">{{ $bank['name'] }}</h3>
                    <p class="text-sm font-semibold text-gray-800 mb-4">{{ $bank['rate'] }}</p>
                    <button type="button"
                            data-open-home-loan-bank
                            data-bank-name="{{ $bank['name'] }}"
                            class="inline-block text-sm font-semibold text-y2b-primary border border-y2b-primary px-5 py-1.5 rounded hover:bg-y2b-primary hover:text-white transition">
                        Know more
                    </button>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- EMI Calculator --}}
<section class="py-14 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4">
        <h2 class="text-2xl md:text-3xl font-bold text-y2b-primary text-center mb-10">EMI Calculator</h2>
        <x-emi-calculator />
    </div>
</section>

<x-home-loan-bank-modal />
@endsection
