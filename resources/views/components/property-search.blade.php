@props(['propertyTypes' => [], 'budgets' => []])

<form action="{{ route('properties.index') }}" method="GET" class="bg-y2b-primary rounded-xl p-6 shadow-xl flex flex-wrap items-end justify-center gap-4 md:gap-6">
    <input type="hidden" name="ptype" value="buy">

    <div class="flex flex-col items-center flex-1 min-w-[140px] max-w-[200px]">
        <label for="city" class="text-white text-sm font-medium mb-1">City</label>
        <select name="city" id="city" class="w-full bg-transparent border-b border-white text-white text-center py-2 outline-none cursor-pointer">
            <option value="ahmedabad" class="text-gray-900">Ahmedabad</option>
            <option value="gandhinagar" class="text-gray-900">Gandhinagar</option>
        </select>
    </div>

    <div class="flex flex-col items-center flex-1 min-w-[140px] max-w-[200px]">
        <label for="area" class="text-white text-sm font-medium mb-1">Area / Project</label>
        <input type="text" name="area" id="area" placeholder="Enter Area / Project"
               class="w-full bg-transparent border-b border-white text-white text-center py-2 outline-none placeholder:text-blue-200">
    </div>

    <div class="flex flex-col items-center flex-1 min-w-[140px] max-w-[200px]">
        <label for="type" class="text-white text-sm font-medium mb-1">Type</label>
        <select name="type" id="type" class="w-full bg-transparent border-b border-white text-white text-center py-2 outline-none cursor-pointer">
            @foreach($propertyTypes as $value => $label)
                <option value="{{ $value }}" class="text-gray-900">{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div class="flex flex-col items-center flex-1 min-w-[140px] max-w-[200px]">
        <label for="budget" class="text-white text-sm font-medium mb-1">Budget</label>
        <select name="budget" id="budget" class="w-full bg-transparent border-b border-white text-white text-center py-2 outline-none cursor-pointer">
            @foreach($budgets as $value => $label)
                <option value="{{ $value }}" class="text-gray-900">{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <button type="submit" class="bg-white hover:bg-y2b-primary-dark hover:text-white text-black font-semibold px-6 py-3 rounded flex items-center gap-2 transition text-sm">
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M10 2a8 8 0 015.292 13.708l4.5 4.5-1.414 1.414-4.5-4.5A8 8 0 1110 2zm0 2a6 6 0 100 12 6 6 0 000-12z"/></svg>
        Search
    </button>
</form>
