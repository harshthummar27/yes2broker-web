@extends('layouts.app')

@section('title', 'Contact')

@section('content')
{{-- Google Map --}}
<section class="w-full">
    <iframe
        loading="lazy"
        src="{{ config('site.maps_embed_url') }}"
        title="{{ config('site.address') }}"
        aria-label="{{ config('site.address') }}"
        class="w-full h-72 md:h-96 border-0 grayscale-[20%]"
        allowfullscreen
        referrerpolicy="no-referrer-when-downgrade">
    </iframe>
</section>

<section class="py-12 md:py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 grid lg:grid-cols-2 gap-10 lg:gap-14 items-start">
        {{-- Contact Details --}}
        <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8">
            <h2 class="text-2xl font-bold text-y2b-primary mb-8">Contact Details</h2>

            <div class="space-y-8">
                <div class="flex gap-4">
                    <a href="{{ config('site.maps_directions_url') }}" target="_blank" rel="noopener"
                       class="w-12 h-12 shrink-0 rounded-full bg-y2b-primary text-white flex items-center justify-center hover:bg-y2b-primary-dark transition">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 384 512"><path d="M172.268 501.67C26.97 291.031 0 269.413 0 192 0 85.961 85.961 0 192 0s192 85.961 192 192c0 77.413-26.97 99.031-172.268 309.67-9.535 13.774-29.93 13.773-39.464 0zM192 272c44.183 0 80-35.817 80-80s-35.817-80-80-80-80 35.817-80 80 35.817 80 80 80z"/></svg>
                    </a>
                    <div>
                        <h3 class="font-bold text-y2b-primary mb-1">
                            <a href="{{ config('site.maps_directions_url') }}" target="_blank" rel="noopener" class="hover:text-y2b-accent transition">
                                Corporate Office Address
                            </a>
                        </h3>
                        <p class="text-gray-600 text-sm leading-relaxed">{{ config('site.address') }}</p>
                    </div>
                </div>

                <div class="flex gap-4">
                    <a href="mailto:{{ config('site.email') }}"
                       class="w-12 h-12 shrink-0 rounded-full bg-y2b-primary text-white flex items-center justify-center hover:bg-y2b-primary-dark transition">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 512 512"><path d="M464 64H48C21.49 64 0 85.49 0 112v288c0 26.51 21.49 48 48 48h416c26.51 0 48-21.49 48-48V112c0-26.51-21.49-48-48-48zm0 48v40.805c-22.422 18.259-58.168 46.651-134.587 106.49-16.841 13.247-50.201 45.072-73.413 44.701-23.208.375-56.579-31.459-73.413-44.701C106.18 199.465 70.425 171.067 48 152.805V112h416zM48 400V214.398c22.914 18.251 55.409 43.862 104.938 82.646 21.857 17.205 60.134 55.186 103.062 54.955 42.717.231 80.509-37.199 103.053-54.947 49.528-38.783 82.032-64.401 104.947-82.653V400H48z"/></svg>
                    </a>
                    <div>
                        <h3 class="font-bold text-y2b-primary mb-1">
                            <a href="mailto:{{ config('site.email') }}" class="hover:text-y2b-accent transition">Email</a>
                        </h3>
                        <p class="text-gray-600 text-sm">{{ config('site.email') }}</p>
                    </div>
                </div>

                <div class="flex gap-4">
                    <a href="{{ config('site.phone_href') }}"
                       class="w-12 h-12 shrink-0 rounded-full bg-y2b-primary text-white flex items-center justify-center hover:bg-y2b-primary-dark transition">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 512 512"><path d="M497.39 361.8l-112-48a24 24 0 0 0-28 6.9l-49.6 60.6A370.66 370.66 0 0 1 130.6 204.11l60.6-49.6a23.94 23.94 0 0 0 6.9-28l-48-112A24.16 24.16 0 0 0 122.6.61l-104 24A24 24 0 0 0 0 48c0 256.5 207.9 464 464 464a24 24 0 0 0 23.4-18.6l24-104a24.29 24.29 0 0 0-14.01-27.6z"/></svg>
                    </a>
                    <div>
                        <h3 class="font-bold text-y2b-primary mb-1">
                            <a href="{{ config('site.phone_href') }}" class="hover:text-y2b-accent transition">Phone No.</a>
                        </h3>
                        <p class="text-gray-600 text-sm">{{ config('site.phone') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Consultation Form --}}
        <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8">
            <h2 class="text-2xl font-bold text-y2b-primary">Book Your Appointment</h2>
            <p class="text-gray-500 mt-1 mb-8">Free Consultation</p>

            <form action="{{ route('enquiry.consultation') }}" method="POST" class="space-y-5">
                @csrf
                <input type="hidden" name="source" value="Contact Page">
                <div class="grid sm:grid-cols-2 gap-5">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1.5">First Name</label>
                        <input type="text" id="first_name" name="first_name" required placeholder="First Name"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 outline-none focus:border-y2b-primary focus:ring-1 focus:ring-y2b-primary">
                    </div>
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1.5">Last Name</label>
                        <input type="text" id="last_name" name="last_name" required placeholder="Last Name"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 outline-none focus:border-y2b-primary focus:ring-1 focus:ring-y2b-primary">
                    </div>
                </div>

                <div class="grid sm:grid-cols-2 gap-5">
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1.5">Phone</label>
                        <input type="tel" id="phone" name="phone" required placeholder="Phone Number"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 outline-none focus:border-y2b-primary focus:ring-1 focus:ring-y2b-primary">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                        <input type="email" id="email" name="email" required placeholder="Email"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 outline-none focus:border-y2b-primary focus:ring-1 focus:ring-y2b-primary">
                    </div>
                </div>

                <div>
                    <label for="looking_for" class="block text-sm font-medium text-gray-700 mb-1.5">What are you looking for?</label>
                    <select id="looking_for" name="looking_for" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 outline-none focus:border-y2b-primary focus:ring-1 focus:ring-y2b-primary bg-white">
                        @foreach($consultationOptions as $option)
                            <option value="{{ $option }}">{{ $option }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="message" class="block text-sm font-medium text-gray-700 mb-1.5">Message</label>
                    <textarea id="message" name="message" rows="4" placeholder="Message"
                              class="w-full border border-gray-300 rounded-lg px-4 py-2.5 outline-none focus:border-y2b-primary focus:ring-1 focus:ring-y2b-primary resize-none"></textarea>
                </div>

                <button type="submit"
                        class="bg-y2b-primary hover:bg-y2b-primary-dark text-white font-semibold px-8 py-3 rounded-lg transition">
                    Submit Request
                </button>
            </form>
        </div>
    </div>
</section>
@endsection
