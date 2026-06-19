@php
    $consultationOptions = \App\Data\HomePageData::consultationOptions();
@endphp

<div id="consultation-modal"
     class="fixed inset-0 z-[100] hidden items-center justify-center p-4"
     role="dialog"
     aria-modal="true"
     aria-labelledby="consultation-modal-title">
    <div id="consultation-modal-backdrop"
         class="absolute inset-0 bg-black/60 backdrop-blur-sm"
         aria-hidden="true"></div>

    <div class="consultation-modal-panel relative w-full max-w-4xl max-h-[90vh] overflow-y-auto bg-white rounded-2xl shadow-2xl grid md:grid-cols-2">
        <button type="button"
                id="consultation-modal-close"
                class="absolute top-3 right-3 z-10 w-9 h-9 flex items-center justify-center rounded-full bg-white/90 text-gray-600 hover:text-y2b-primary hover:bg-white shadow transition"
                aria-label="Close">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 352 512"><path d="M242.72 256l100.07-100.07c12.28-12.28 12.28-32.19 0-44.48l-22.24-22.24c-12.28-12.28-32.19-12.28-44.48 0L176 189.28 75.93 89.21c-12.28-12.28-32.19-12.28-44.48 0L9.21 111.45c-12.28 12.28-12.28 32.19 0 44.48L109.28 256 9.21 356.07c-12.28 12.28-12.28 32.19 0 44.48l22.24 22.24c12.28 12.28 32.2 12.28 44.48 0L176 322.72l100.07 100.07c12.28 12.28 32.2 12.28 44.48 0l22.24-22.24c12.28-12.28 12.28-32.19 0-44.48L242.72 256z"/></svg>
        </button>

        {{-- Logo panel --}}
        <div class="consultation-modal-brand hidden md:flex items-center justify-center p-8 md:p-10 rounded-t-2xl md:rounded-l-2xl md:rounded-tr-none">
            <img src="{{ config('site.popup_logo') }}" alt="{{ config('site.name') }}"
                 class="max-w-[280px] w-full h-auto">
        </div>

        {{-- Form panel --}}
        <div class="p-6 md:p-8">
            <div class="md:hidden flex justify-center mb-6">
                <img src="{{ config('site.popup_logo') }}" alt="{{ config('site.name') }}" class="h-12 w-auto">
            </div>

            <h2 id="consultation-modal-title" class="text-xl font-bold text-y2b-primary">Book Your Appointment</h2>
            <p class="text-gray-500 text-sm mt-1 mb-6">Free Consultation</p>

            <form action="{{ route('enquiry.consultation') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="source" value="Consultation Modal">
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label for="modal_first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                        <input type="text" id="modal_first_name" name="first_name" required placeholder="First Name"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm outline-none focus:border-y2b-primary focus:ring-1 focus:ring-y2b-primary">
                    </div>
                    <div>
                        <label for="modal_last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                        <input type="text" id="modal_last_name" name="last_name" required placeholder="Last Name"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm outline-none focus:border-y2b-primary focus:ring-1 focus:ring-y2b-primary">
                    </div>
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label for="modal_phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                        <x-form-mobile-input id="modal_phone" name="phone" placeholder="Mobile Number"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm outline-none focus:border-y2b-primary focus:ring-1 focus:ring-y2b-primary" />
                    </div>
                    <div>
                        <label for="modal_email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <x-form-email-input id="modal_email" name="email" placeholder="Email"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm outline-none focus:border-y2b-primary focus:ring-1 focus:ring-y2b-primary" />
                    </div>
                </div>

                <div>
                    <label for="modal_looking_for" class="block text-sm font-medium text-gray-700 mb-1">What are you looking for?</label>
                    <select id="modal_looking_for" name="looking_for" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm outline-none focus:border-y2b-primary focus:ring-1 focus:ring-y2b-primary bg-white">
                        @foreach($consultationOptions as $option)
                            <option value="{{ $option }}">{{ $option }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="modal_message" class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                    <textarea id="modal_message" name="message" rows="3" placeholder="Message"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm outline-none focus:border-y2b-primary focus:ring-1 focus:ring-y2b-primary resize-none"></textarea>
                </div>

                <button type="submit"
                        class="bg-y2b-primary hover:bg-y2b-primary-dark text-white font-semibold px-6 py-2.5 rounded-lg transition text-sm">
                    Submit Request
                </button>
            </form>
        </div>
    </div>
</div>
