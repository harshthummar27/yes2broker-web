@props(['propertyTypes' => [], 'budgets' => [], 'selected' => []])

@php
    $selectedCity = $selected['city'] ?? request('city', 'ahmedabad');
    $selectedArea = $selected['area'] ?? request('area', '');
    $selectedType = $selected['type'] ?? request('type', '');
    $selectedBudget = $selected['budget'] ?? request('budget', '');

    $fieldClass = 'w-full bg-transparent border-0 border-b border-white text-white text-[15px] font-medium text-center py-1.5 px-6 outline-none placeholder:text-blue-200';
    $selectClass = $fieldClass . ' appearance-none cursor-pointer';
    $chevronClass = 'absolute right-0 top-1/2 -translate-y-1/2 w-4 h-4 text-white pointer-events-none';
@endphp

<form action="{{ route('properties.index') }}" method="GET"
      class="flex flex-wrap md:flex-nowrap items-stretch bg-y2b-primary rounded-xl shadow-xl">
    <input type="hidden" name="ptype" value="buy">

    <div class="flex-1 min-w-[140px] px-6 py-5 flex flex-col items-center justify-center">
        <label for="city" class="text-white text-sm font-semibold mb-2 text-center">City</label>
        <div class="relative w-full max-w-[200px]">
            <select name="city" id="city" class="{{ $selectClass }}">
                <option value="ahmedabad" class="text-gray-900" @selected($selectedCity === 'ahmedabad')>Ahmedabad</option>
                <option value="gandhinagar" class="text-gray-900" @selected($selectedCity === 'gandhinagar')>Gandhinagar</option>
            </select>
            <svg class="{{ $chevronClass }}" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
        </div>
    </div>

    <div class="hidden md:block w-px self-stretch bg-white/35 my-4" aria-hidden="true"></div>

    <div class="flex-1 min-w-[140px] px-6 py-5 flex flex-col items-center justify-center">
        <label for="area" class="text-white text-sm font-semibold mb-2 text-center">Area / Project</label>
        <div class="relative w-full max-w-[200px]">
            <input type="text" name="area" id="area" placeholder="Enter Area / Project" value="{{ $selectedArea }}"
                   class="{{ $fieldClass }}">
        </div>
    </div>

    <div class="hidden md:block w-px self-stretch bg-white/35 my-4" aria-hidden="true"></div>

    <div class="flex-1 min-w-[140px] px-6 py-5 flex flex-col items-center justify-center">
        <label for="type" class="text-white text-sm font-semibold mb-2 text-center">Type</label>
        <div class="relative w-full max-w-[200px]">
            <select name="type" id="type" class="{{ $selectClass }}">
                @foreach($propertyTypes as $value => $label)
                    <option value="{{ $value }}" class="text-gray-900" @selected($selectedType === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <svg class="{{ $chevronClass }}" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
        </div>
    </div>

    <div class="hidden md:block w-px self-stretch bg-white/35 my-4" aria-hidden="true"></div>

    <div class="flex-1 min-w-[140px] px-6 py-5 flex flex-col items-center justify-center">
        <label for="budget" class="text-white text-sm font-semibold mb-2 text-center">Budget</label>
        <div class="relative w-full max-w-[200px]">
            <select name="budget" id="budget" class="{{ $selectClass }}">
                @foreach($budgets as $value => $label)
                    <option value="{{ $value }}" class="text-gray-900" @selected($selectedBudget === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <svg class="{{ $chevronClass }}" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
        </div>
    </div>

    <div class="hidden md:block w-px self-stretch bg-white/35 my-4" aria-hidden="true"></div>

    <div class="flex items-center justify-center p-4 md:px-6 md:py-5 w-full md:w-auto shrink-0">
        <button type="submit"
                class="inline-flex items-center justify-center gap-2 bg-white text-y2b-primary text-sm font-bold px-6 py-3 rounded hover:bg-y2b-primary-dark hover:text-white transition whitespace-nowrap">
            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M10 2a8 8 0 015.292 13.708l4.5 4.5-1.414 1.414-4.5-4.5A8 8 0 1110 2zm0 2a6 6 0 100 12 6 6 0 000-12z"/>
            </svg>
            Search
        </button>
    </div>
</form>
