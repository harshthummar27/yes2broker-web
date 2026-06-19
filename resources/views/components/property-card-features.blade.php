@props([
    'property',
    'detailed' => false,
    'iconClass' => 'w-3.5 h-3.5',
])

<ul {{ $attributes->merge(['class' => 'space-y-1.5']) }}>
    <li class="flex items-center gap-1.5">
        <svg class="{{ $iconClass }} shrink-0 text-y2b-primary" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M7 13c1.66 0 3-1.34 3-3S8.66 7 7 7s-3 1.34-3 3 1.34 3 3 3zm12-6h-8v7H3V5H1v15h2v-3h18v3h2v-9c0-2.21-1.79-4-4-4z"/>
        </svg>
        <span>{{ $property['bhk'] }}</span>
    </li>
    <li class="flex items-center gap-1.5">
        <svg class="{{ $iconClass }} shrink-0 text-y2b-primary" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M3 3v8h8V3H3zm6 6H5V5h4v4zm-6 4v8h8v-8H3zm2 2h4v4H5v-4zm8-12v8h8V3h-8zm6 6h-4V5h4v4zm-6 4v8h8v-8h-8zm2 2h4v4h-4v-4z"/>
        </svg>
        <span>Project Area: {{ $property['area'] }}</span>
    </li>
    <li class="flex items-center gap-1.5">
        <svg class="{{ $iconClass }} shrink-0 text-y2b-primary" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2z"/>
        </svg>
        <span>
            @if($detailed)
                Possession Date: {{ $property['possession'] }}
            @else
                {{ $property['possession'] }}
            @endif
        </span>
    </li>
</ul>
