<div id="home-loan-bank-modal"
     class="fixed inset-0 z-[100] hidden items-center justify-center p-4"
     role="dialog"
     aria-modal="true"
     aria-labelledby="home-loan-bank-modal-title">
    <div id="home-loan-bank-modal-backdrop"
         class="absolute inset-0 bg-black/60 backdrop-blur-sm"
         aria-hidden="true"></div>

    <div class="relative w-full max-w-lg max-h-[90vh] overflow-y-auto bg-white rounded-2xl shadow-2xl p-6 md:p-8">
        <button type="button"
                id="home-loan-bank-modal-close"
                class="absolute top-3 right-3 w-9 h-9 flex items-center justify-center rounded-full bg-gray-100 text-gray-600 hover:text-y2b-primary hover:bg-gray-200 transition"
                aria-label="Close">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 352 512"><path d="M242.72 256l100.07-100.07c12.28-12.28 12.28-32.19 0-44.48l-22.24-22.24c-12.28-12.28-32.19-12.28-44.48 0L176 189.28 75.93 89.21c-12.28-12.28-32.19-12.28-44.48 0L9.21 111.45c-12.28 12.28-12.28 32.19 0 44.48L109.28 256 9.21 356.07c-12.28 12.28-12.28 32.19 0 44.48l22.24 22.24c12.28 12.28 32.2 12.28 44.48 0L176 322.72l100.07 100.07c12.28 12.28 32.2 12.28 44.48 0l22.24-22.24c12.28-12.28 12.28-32.19 0-44.48L242.72 256z"/></svg>
        </button>

        <h2 id="home-loan-bank-modal-title" class="text-xl font-bold text-y2b-primary pr-8">
            Home Loan Inquiry — <span id="home-loan-bank-modal-name"></span>
        </h2>
        <p class="text-gray-500 text-sm mt-1 mb-6">Fill in your details and our team will get back to you.</p>

        <form action="#" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="bank" id="home-loan-bank-modal-input" value="">

            <input type="text" name="name" required placeholder="Name"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2.5 outline-none focus:border-y2b-primary focus:ring-1 focus:ring-y2b-primary bg-white text-sm">
            <input type="tel" name="phone" required placeholder="Telephone"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2.5 outline-none focus:border-y2b-primary focus:ring-1 focus:ring-y2b-primary bg-white text-sm">
            <input type="email" name="email" required placeholder="Email"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2.5 outline-none focus:border-y2b-primary focus:ring-1 focus:ring-y2b-primary bg-white text-sm">
            <input type="text" name="amount" placeholder="Loan Amount"
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
                Submit Request
            </button>
        </form>
    </div>
</div>
