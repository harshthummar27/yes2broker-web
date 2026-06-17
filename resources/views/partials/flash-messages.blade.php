@if(session('success') || session('error') || $errors->any())
    <div id="flash-popup"
         class="fixed inset-0 z-[200] flex items-center justify-center p-4"
         role="dialog"
         aria-modal="true"
         aria-live="polite">
        <button type="button"
                id="flash-popup-backdrop"
                class="absolute inset-0 bg-black/50"
                aria-label="Close popup"></button>

        <div class="relative w-full max-w-lg rounded-2xl bg-white shadow-2xl border border-gray-200 overflow-hidden">
            <div class="flex items-start justify-between gap-4 px-5 py-4 border-b border-gray-100">
                <div class="min-w-0">
                    @if(session('success'))
                        <p class="text-sm font-semibold text-green-700">Success</p>
                        <p class="mt-1 text-sm text-gray-700 break-words">{{ session('success') }}</p>
                    @elseif(session('error'))
                        <p class="text-sm font-semibold text-red-700">Error</p>
                        <p class="mt-1 text-sm text-gray-700 break-words">{{ session('error') }}</p>
                    @elseif($errors->any())
                        <p class="text-sm font-semibold text-red-700">Please fix the following</p>
                        <ul class="mt-2 text-sm text-gray-700 list-disc list-inside space-y-0.5">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <button type="button"
                        id="flash-popup-close"
                        class="shrink-0 inline-flex items-center justify-center w-9 h-9 rounded-full bg-gray-100 text-gray-600 hover:bg-gray-200 hover:text-y2b-primary transition"
                        aria-label="Close">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 352 512" aria-hidden="true"><path d="M242.72 256l100.07-100.07c12.28-12.28 12.28-32.19 0-44.48l-22.24-22.24c-12.28-12.28-32.19-12.28-44.48 0L176 189.28 75.93 89.21c-12.28-12.28-32.19-12.28-44.48 0L9.21 111.45c-12.28 12.28-12.28 32.19 0 44.48L109.28 256 9.21 356.07c-12.28 12.28-12.28 32.19 0 44.48l22.24 22.24c12.28 12.28 32.2 12.28 44.48 0L176 322.72l100.07 100.07c12.28 12.28 32.2 12.28 44.48 0l22.24-22.24c12.28-12.28 12.28-32.19 0-44.48L242.72 256z"/></svg>
                </button>
            </div>

            <div class="px-5 py-4 flex items-center justify-end gap-3">
                <button type="button"
                        id="flash-popup-ok"
                        class="inline-flex items-center justify-center rounded-lg bg-y2b-primary px-5 py-2.5 text-sm font-semibold text-white hover:bg-y2b-primary-dark transition">
                    OK
                </button>
            </div>
        </div>
    </div>

    <script>
        (function () {
            var popup = document.getElementById('flash-popup');
            if (!popup) return;

            function closePopup() {
                popup.remove();
            }

            var close = document.getElementById('flash-popup-close');
            var ok = document.getElementById('flash-popup-ok');
            var backdrop = document.getElementById('flash-popup-backdrop');

            if (close) close.addEventListener('click', closePopup);
            if (ok) ok.addEventListener('click', closePopup);
            if (backdrop) backdrop.addEventListener('click', closePopup);

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') closePopup();
            });

            window.setTimeout(closePopup, 4500);
        })();
    </script>
@endif
