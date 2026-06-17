@props(['propertyTypes' => [], 'budgets' => [], 'selected' => []])

@php
    $selectedCity = $selected['city'] ?? request('city', 'ahmedabad');
    $selectedArea = $selected['area'] ?? request('area', '');
    $selectedType = $selected['type'] ?? request('type', '');
    $selectedBudget = $selected['budget'] ?? request('budget', '');
@endphp

<form action="{{ route('properties.index') }}" method="GET" class="property-search-bar">
    <input type="hidden" name="ptype" value="buy">

    <div class="property-search-field">
        <label for="city" class="property-search-label">City</label>
        <div class="property-search-control">
            <select name="city" id="city" class="property-search-input property-search-select">
                <option value="ahmedabad" @selected($selectedCity === 'ahmedabad')>Ahmedabad</option>
                <option value="gandhinagar" @selected($selectedCity === 'gandhinagar')>Gandhinagar</option>
            </select>
            <svg class="property-search-chevron" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
        </div>
    </div>

    <div class="property-search-divider" aria-hidden="true"></div>

    <div class="property-search-field">
        <label for="area" class="property-search-label">Area / Project</label>
        <div class="property-search-control">
            <input type="text" name="area" id="area" placeholder="Enter Area / Project" value="{{ $selectedArea }}"
                   class="property-search-input">
        </div>
    </div>

    <div class="property-search-divider" aria-hidden="true"></div>

    <div class="property-search-field">
        <label for="type" class="property-search-label">Type</label>
        <div class="property-search-control">
            <select name="type" id="type" class="property-search-input property-search-select">
                @foreach($propertyTypes as $value => $label)
                    <option value="{{ $value }}" @selected($selectedType === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <svg class="property-search-chevron" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
        </div>
    </div>

    <div class="property-search-divider" aria-hidden="true"></div>

    <div class="property-search-field">
        <label for="budget" class="property-search-label">Budget</label>
        <div class="property-search-control">
            <select name="budget" id="budget" class="property-search-input property-search-select">
                @foreach($budgets as $value => $label)
                    <option value="{{ $value }}" @selected($selectedBudget === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <svg class="property-search-chevron" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
        </div>
    </div>

    <div class="property-search-divider property-search-divider-btn" aria-hidden="true"></div>

    <div class="property-search-action">
        <button type="submit" class="property-search-btn">
            <svg class="property-search-btn-icon" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M10 2a8 8 0 015.292 13.708l4.5 4.5-1.414 1.414-4.5-4.5A8 8 0 1110 2zm0 2a6 6 0 100 12 6 6 0 000-12z"/>
            </svg>
            Search
        </button>
    </div>
</form>
