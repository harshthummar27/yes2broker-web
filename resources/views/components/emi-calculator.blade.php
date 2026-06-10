<div id="emi-calculator" class="max-w-6xl mx-auto grid lg:grid-cols-5 gap-8 lg:gap-12 items-start">
    {{-- Sliders --}}
    <div class="lg:col-span-3 bg-white rounded-2xl shadow-lg p-6 md:p-8 space-y-8">
        <div>
            <div class="flex flex-wrap items-baseline gap-2 mb-3">
                <span class="font-bold text-gray-900">Loan Amount (₹)</span>
                <span id="emi-loan-label" class="font-bold text-y2b-primary"></span>
            </div>
            <input type="range" id="emi-loan-amount" min="100000" max="50000000" step="100000" value="6800000"
                   class="emi-slider w-full">
            <div class="flex justify-between text-sm text-gray-500 mt-1">
                <span class="font-semibold text-y2b-primary">₹1.00 Lac</span>
                <span class="font-semibold text-y2b-primary">₹5.00 Cr</span>
            </div>
        </div>

        <div>
            <div class="flex flex-wrap items-baseline gap-2 mb-3">
                <span class="font-bold text-gray-900">Tenure (Years)</span>
                <span id="emi-tenure-label" class="font-bold text-y2b-primary"></span>
            </div>
            <input type="range" id="emi-tenure" min="1" max="30" step="1" value="20"
                   class="emi-slider w-full">
            <div class="flex justify-between text-sm text-gray-500 mt-1">
                <span>1</span>
                <span>30</span>
            </div>
        </div>

        <div>
            <div class="flex flex-wrap items-baseline gap-2 mb-3">
                <span class="font-bold text-gray-900">Rate of Interest (%)</span>
                <span id="emi-rate-label" class="font-bold text-y2b-primary"></span>
            </div>
            <input type="range" id="emi-interest" min="0.5" max="15" step="0.1" value="8.8"
                   class="emi-slider w-full">
            <div class="flex justify-between text-sm text-gray-500 mt-1">
                <span>0.5%</span>
                <span>15%</span>
            </div>
        </div>
    </div>

    {{-- Results --}}
    <div class="lg:col-span-2 flex flex-col items-center gap-5">
        <p class="text-lg font-semibold text-gray-900">Your EMI Per Month</p>
        <p id="emi-amount" class="text-4xl font-extrabold text-y2b-primary">₹0</p>

        <div class="relative w-56 h-56">
            <canvas id="emi-chart" width="224" height="224" class="w-full h-full"></canvas>
            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                <div class="text-center text-xs text-gray-500">
                    <span class="inline-block w-2 h-2 rounded-full bg-y2b-primary mr-1"></span>Principal
                    <span class="inline-block w-2 h-2 rounded-full bg-y2b-light mr-1 ml-2"></span>Interest
                </div>
            </div>
        </div>

        <div class="w-full space-y-3">
            <div class="bg-y2b-light/60 rounded-xl p-4 text-center shadow-sm">
                <span class="block text-sm text-gray-700 mb-1">Loan Amount</span>
                <span id="emi-loan-out" class="text-lg font-bold text-y2b-primary">₹0</span>
            </div>
            <div class="bg-y2b-light/60 rounded-xl p-4 text-center shadow-sm">
                <span class="block text-sm text-gray-700 mb-1">Total Interest</span>
                <span id="emi-interest-out" class="text-lg font-bold text-y2b-primary">₹0</span>
            </div>
            <div class="bg-y2b-light/60 rounded-xl p-4 text-center shadow-sm">
                <span class="block text-sm text-gray-700 mb-1">Total Amount Payable</span>
                <span id="emi-total-out" class="text-lg font-bold text-y2b-primary">₹0</span>
            </div>
        </div>
    </div>
</div>
