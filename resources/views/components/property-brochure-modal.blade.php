@props(['propertyTitle', 'propertySlug'])

<div id="property-brochure-modal"
     class="fixed inset-0 z-[100] hidden items-center justify-center p-4"
     role="dialog"
     aria-modal="true"
     aria-labelledby="property-brochure-modal-title">
    <div id="property-brochure-modal-backdrop"
         class="absolute inset-0 bg-black/60 backdrop-blur-sm"
         aria-hidden="true"></div>

    <div class="relative w-full max-w-lg max-h-[90vh] overflow-y-auto bg-white rounded-2xl shadow-2xl p-6 md:p-8">
        <button type="button"
                id="property-brochure-modal-close"
                class="absolute top-3 right-3 w-9 h-9 flex items-center justify-center rounded-full bg-gray-100 text-gray-600 hover:text-y2b-primary hover:bg-gray-200 transition"
                aria-label="Close">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 352 512"><path d="M242.72 256l100.07-100.07c12.28-12.28 12.28-32.19 0-44.48l-22.24-22.24c-12.28-12.28-32.19-12.28-44.48 0L176 189.28 75.93 89.21c-12.28-12.28-32.19-12.28-44.48 0L9.21 111.45c-12.28 12.28-12.28 32.19 0 44.48L109.28 256 9.21 356.07c-12.28 12.28-12.28 32.19 0 44.48l22.24 22.24c12.28 12.28 32.2 12.28 44.48 0L176 322.72l100.07 100.07c12.28 12.28 32.2 12.28 44.48 0l22.24-22.24c12.28-12.28 12.28-32.19 0-44.48L242.72 256z"/></svg>
        </button>

        <h2 id="property-brochure-modal-title" class="text-xl font-bold text-y2b-primary pr-8">
            Download Brochure — <span id="property-brochure-modal-name">{{ $propertyTitle }}</span>
        </h2>
        <p class="text-gray-500 text-sm mt-1 mb-6">Share your details to download the project brochure PDF.</p>

        <form action="{{ route('enquiry.property-inquiry') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="source" value="Brochure Download">
            <input type="hidden" name="property" value="{{ $propertyTitle }}">
            <input type="hidden" name="property_slug" value="{{ $propertySlug }}">
            <input type="hidden" name="download_brochure" value="1">

            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label for="brochure_first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                    <input type="text" id="brochure_first_name" name="first_name" required placeholder="First Name"
                           value="{{ old('first_name') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm outline-none focus:border-y2b-primary focus:ring-1 focus:ring-y2b-primary">
                </div>
                <div>
                    <label for="brochure_last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                    <input type="text" id="brochure_last_name" name="last_name" required placeholder="Last Name"
                           value="{{ old('last_name') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm outline-none focus:border-y2b-primary focus:ring-1 focus:ring-y2b-primary">
                </div>
            </div>

            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label for="brochure_phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <x-form-mobile-input id="brochure_phone" name="phone" placeholder="Mobile Number"
                           value="{{ old('phone') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm outline-none focus:border-y2b-primary focus:ring-1 focus:ring-y2b-primary" />
                </div>
                <div>
                    <label for="brochure_email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <x-form-email-input id="brochure_email" name="email" placeholder="Email"
                           value="{{ old('email') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm outline-none focus:border-y2b-primary focus:ring-1 focus:ring-y2b-primary" />
                </div>
            </div>

            <div>
                <label for="brochure_message" class="block text-sm font-medium text-gray-700 mb-1">Message <span class="text-gray-400 font-normal">(optional)</span></label>
                <textarea id="brochure_message" name="message" rows="3" placeholder="I'm interested in {{ $propertyTitle }}"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm outline-none focus:border-y2b-primary focus:ring-1 focus:ring-y2b-primary resize-none">{{ old('message') }}</textarea>
            </div>

            <button type="submit"
                    class="w-full bg-y2b-primary hover:bg-y2b-primary-dark text-white font-semibold px-6 py-2.5 rounded-lg transition text-sm">
                Submit &amp; Download Brochure
            </button>
        </form>
    </div>
</div>
