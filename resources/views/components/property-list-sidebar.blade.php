<div class="property-list-sidebar bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
    <div class="property-list-sidebar-header px-5 py-4">
        <h3 class="text-white font-bold text-base leading-snug">
            Schedule your free site visit<br>today
        </h3>
    </div>
    <form action="{{ route('enquiry.consultation') }}" method="POST" class="p-5 space-y-4">
        @csrf
        <input type="hidden" name="source" value="All Properties Sidebar">
        <input type="hidden" name="looking_for" value="Other">
        <input type="hidden" name="last_name" value="-">

        <div>
            <input type="text" name="first_name" required placeholder="Name"
                   class="property-list-sidebar-input w-full border-0 border-b border-gray-300 bg-transparent px-0 py-2.5 text-sm outline-none focus:border-y2b-primary placeholder:text-gray-400">
        </div>
        <div>
            <x-form-mobile-input name="phone" placeholder="Mobile Number"
                   class="property-list-sidebar-input w-full border-0 border-b border-gray-300 bg-transparent px-0 py-2.5 text-sm outline-none focus:border-y2b-primary placeholder:text-gray-400" />
        </div>
        <div>
            <x-form-email-input name="email" placeholder="Email Address"
                   class="property-list-sidebar-input w-full border-0 border-b border-gray-300 bg-transparent px-0 py-2.5 text-sm outline-none focus:border-y2b-primary placeholder:text-gray-400" />
        </div>

        <p class="text-[11px] text-gray-400 leading-relaxed">
            By proceeding, I agree to {{ config('site.name') }} terms and conditions.
        </p>

        <button type="submit"
                class="w-full border border-gray-300 hover:border-y2b-primary hover:text-y2b-primary text-gray-700 font-semibold py-3 rounded-lg transition text-sm">
            Submit
        </button>

        <p class="text-[11px] text-gray-400 text-center leading-relaxed">
            Rest assured, you'll receive a call from our sales expert within the next 5 minutes.
        </p>
    </form>
</div>
