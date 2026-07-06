@props([
    'icon' => 'default',
    'class' => 'property-overview-icon',
])

@php
    $icon = $icon ?: 'default';
@endphp

<svg {{ $attributes->merge(['class' => $class]) }} viewBox="0 0 48 48" fill="currentColor" aria-hidden="true">
    @switch($icon)
        @case('project-area')
            <path d="M8 38V10l4-2 4 2v28H8zm8 0V14.5l3-1.5 3 1.5V38H16zm14-6.5 6-10.5 6 10.5h-3.2l-.8-1.4H34l-.8 1.4H30zm4.2-4.2L36 22.4l-1.8 3.1h3.6zM6 40h36v2H6v-2z"/>
            @break
        @case('configuration')
            <path d="M8 8h14v2H10v12H8V8zm16 0h16v16H24V8zm2 2v12h12V10H26zM8 24h14v2H10v12H8V24zm16 0h16v16H24V24zm2 2v12h12V26H26z"/>
            @break
        @case('project-size')
            <path d="M10 38V18l14-8 14 8v20H10zm4-2h20v-6H14v6zm0-8h8v-6h-8v6zm10 0h6v-6h-6v6zm-10-8h20l-10-5.7L14 20zm-2 20h24v2H12v-2z"/>
            @break
        @case('launch-date')
        @case('possession')
            <path d="M10 12h4V8h2v4h16V8h2v4h4a2 2 0 0 1 2 2v24a2 2 0 0 1-2 2H10a2 2 0 0 1-2-2V14a2 2 0 0 1 2-2zm24 8H10v20h24V20zM16 24h4v4h-4v-4zm8 0h4v4h-4v-4zm8 0h4v4h-4v-4zM16 30h4v4h-4v-4zm8 0h4v4h-4v-4z"/>
            @break
        @case('price-range')
            <path d="M30.2 8 42 19.8 19.8 42 8 30.2 30.2 8zm-2.8 4.2L12.2 32l7.8 7.8L35.8 24 27.4 12.2zM33 14a3 3 0 1 1 0 6 3 3 0 0 1 0-6z"/>
            @break
        @case('rera')
            <path d="M12 8h24a2 2 0 0 1 2 2v28l-14-6-14 6V10a2 2 0 0 1 2-2zm2 4v19.2l12-5.1 12 5.1V12H14zm8 4h8v2h-8v-2zm0 6h12v2H22v-2z"/>
            @break
        @default
            <path d="M24 4a20 20 0 1 0 0 40 20 20 0 0 0 0-40zm-2 28.2 8.5-8.4-1.4-1.4L22 29.4l-3.1-3.1-1.4 1.4L22 32.2z"/>
    @endswitch
</svg>
