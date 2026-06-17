@props([
    'src',
    'title' => 'Map',
    'height' => 'h-72 md:h-96',
])

@if(filled($src))
    <iframe
        loading="lazy"
        src="{{ $src }}"
        title="{{ $title }}"
        aria-label="{{ $title }}"
        {{ $attributes->merge(['class' => "w-full border-0 {$height}"]) }}
        allowfullscreen
        referrerpolicy="no-referrer-when-downgrade">
    </iframe>
@endif
