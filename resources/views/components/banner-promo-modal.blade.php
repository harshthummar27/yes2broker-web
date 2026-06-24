<div id="banner-promo-modal"
     class="fixed inset-0 z-[100] hidden items-center justify-center p-4"
     role="dialog"
     aria-modal="true"
     aria-labelledby="banner-promo-modal-title">
    <div id="banner-promo-modal-backdrop"
         class="absolute inset-0 bg-black/60 backdrop-blur-sm"
         aria-hidden="true"></div>

    <div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl p-6 md:p-8">
        <button type="button"
                id="banner-promo-modal-close"
                class="absolute top-3 right-3 w-9 h-9 flex items-center justify-center rounded-full bg-gray-100 text-gray-600 hover:text-y2b-primary hover:bg-gray-200 transition"
                aria-label="Close">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 352 512"><path d="M242.72 256l100.07-100.07c12.28-12.28 12.28-32.19 0-44.48l-22.24-22.24c-12.28-12.28-32.19-12.28-44.48 0L176 189.28 75.93 89.21c-12.28-12.28-32.19-12.28-44.48 0L9.21 111.45c-12.28 12.28-12.28 32.19 0 44.48L109.28 256 9.21 356.07c-12.28 12.28-12.28 32.19 0 44.48l22.24 22.24c12.28 12.28 32.2 12.28 44.48 0L176 322.72l100.07 100.07c12.28 12.28 32.2 12.28 44.48 0l22.24-22.24c12.28-12.28 12.28-32.19 0-44.48L242.72 256z"/></svg>
        </button>

        <h2 id="banner-promo-modal-title" class="text-xl font-bold text-y2b-primary pr-8">Get in Touch</h2>
        <p class="text-gray-500 text-sm mt-1 mb-6">Share your details and we will contact you shortly.</p>

        <form action="{{ route('enquiry.banner-promo') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="source" id="banner-promo-source" value="Homepage Banner">
            <input type="hidden" name="promo_id" id="banner-promo-id" value="">

            <div>
                <label for="banner_promo_name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <input type="text" id="banner_promo_name" name="name" required placeholder="Your name"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm outline-none focus:border-y2b-primary focus:ring-1 focus:ring-y2b-primary">
            </div>

            <div>
                <label for="banner_promo_phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                <x-form-mobile-input id="banner_promo_phone" name="phone" placeholder="Mobile Number"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm outline-none focus:border-y2b-primary focus:ring-1 focus:ring-y2b-primary" />
            </div>

            <div>
                <label for="banner_promo_email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <x-form-email-input id="banner_promo_email" name="email" placeholder="Email address"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm outline-none focus:border-y2b-primary focus:ring-1 focus:ring-y2b-primary" />
            </div>

            <button type="submit"
                    class="w-full bg-y2b-primary hover:bg-y2b-primary-dark text-white font-semibold px-6 py-2.5 rounded-lg transition text-sm">
                Submit
            </button>
        </form>
    </div>
</div>
