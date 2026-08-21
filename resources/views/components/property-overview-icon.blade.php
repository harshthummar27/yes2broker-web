@props([
    'icon' => 'default',
    'class' => 'property-overview-icon',
])

@php
    $icon = $icon ?: 'default';
@endphp

<svg {{ $attributes->merge(['class' => $class]) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    @switch($icon)
        @case('project-area')
            {{-- Land Plot Area / Boundary with corner markers --}}
            <path d="M3 7V5a2 2 0 0 1 2-2h2"/>
            <path d="M17 3h2a2 2 0 0 1 2 2v2"/>
            <path d="M21 17v2a2 2 0 0 1-2 2h-2"/>
            <path d="M7 21H5a2 2 0 0 1-2-2v-2"/>
            <rect x="7" y="7" width="10" height="10" rx="1.5" stroke-width="1.6" fill="currentColor" fill-opacity="0.12"/>
            <path d="M7 12h10M12 7v10" stroke-width="1.4" stroke-dasharray="2 2"/>
            @break

        @case('configuration')
            {{-- House / Room Layout & Floor Plan --}}
            <rect x="3" y="3" width="18" height="18" rx="2" stroke-width="1.8"/>
            <path d="M3 10h8v11M11 15h10M11 3v7" stroke-width="1.6"/>
            <circle cx="7" cy="6.5" r="1.2" fill="currentColor"/>
            <circle cx="16" cy="7.5" r="1.2" fill="currentColor"/>
            <circle cx="16" cy="18" r="1.2" fill="currentColor"/>
            @break

        @case('sizes')
            {{-- Dimension / Expand Measurement Arrows --}}
            <path d="M21 3v6M21 3h-6M21 3l-7 7"/>
            <path d="M3 21v-6M3 21h6M3 21l7-7"/>
            <rect x="6" y="6" width="12" height="12" rx="2" stroke-width="1.4" stroke-dasharray="2 2"/>
            @break

        @case('launch-date')
            {{-- Rocket Launch / Project Inception --}}
            <path d="M4.5 16.5c-1.5 1.26-2 5-2 5s3.74-.5 5-2c.71-.84.7-2.13-.09-2.91a2.18 2.18 0 0 0-2.91-.09z"/>
            <path d="M12 15l-3-3a22 22 0 0 1 2-3.95A12.88 12.88 0 0 1 22 2c0 2.72-.78 7.5-2.95 9.95A22 22 0 0 1 15 12z" fill="currentColor" fill-opacity="0.1"/>
            <path d="M15 9l-6 6"/>
            <circle cx="15.5" cy="8.5" r="1.5" fill="currentColor"/>
            @break

        @case('possession')
            {{-- Key Handover / Move-in Key & Home --}}
            <path d="M3 9.5L12 3l9 6.5V20a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V9.5z" fill="currentColor" fill-opacity="0.08"/>
            <circle cx="12" cy="11.5" r="2.2" stroke-width="1.6"/>
            <path d="M12 13.7v4.3M12 16h2.2" stroke-width="1.8"/>
            @break

        @case('price-range')
            {{-- Indian Rupee Price Tag Badge --}}
            <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z" fill="currentColor" fill-opacity="0.08"/>
            <circle cx="7" cy="7" r="1.5" fill="currentColor"/>
            <path d="M11 9h4M11 11.5h3.5M11 11.5c1.8 0 2.5 1.5 2.5 2.5s-1 2.5-3 2.5h-1l3.5 3.5" stroke-width="1.5"/>
            @break

        @case('rera')
            {{-- Official Government Verified Shield / RERA Badge --}}
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" fill="currentColor" fill-opacity="0.1"/>
            <path d="M9 12l2 2 4-4" stroke-width="2"/>
            @break

        @case('project-size')
            {{-- High-Rise Towers / Building Units --}}
            <rect x="3" y="3" width="8" height="18" rx="1" fill="currentColor" fill-opacity="0.08"/>
            <rect x="13" y="7" width="8" height="14" rx="1" fill="currentColor" fill-opacity="0.08"/>
            <path d="M6 7h2M6 11h2M6 15h2M16 11h2M16 15h2" stroke-width="1.8"/>
            @break

        @default
            {{-- Verified Information Check Circle --}}
            <circle cx="12" cy="12" r="10" fill="currentColor" fill-opacity="0.08"/>
            <path d="M12 8v4M12 16h.01" stroke-width="2"/>
    @endswitch
</svg>
