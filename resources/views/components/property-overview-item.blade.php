@props([
    'icon' => 'default',
    'label' => '',
    'value' => '',
    'valueStyle' => 'default',
])

@php
    $lines = array_values(array_filter(preg_split("/\r\n|\n|\r/", (string) $value) ?: [], fn (string $line): bool => $line !== ''));
    $primaryLine = $lines[0] ?? '';
    $secondaryLines = array_slice($lines, 1);
@endphp

<div {{ $attributes->merge(['class' => 'property-overview-item']) }}>
    <x-property-overview-icon :icon="$icon" />
    <div class="property-overview-content">
        <p class="property-overview-label">{{ $label }}:</p>
        @if($valueStyle === 'list')
            @foreach($lines as $line)
                <p class="property-overview-value">{{ $line }}</p>
            @endforeach
        @else
            @if($primaryLine !== '')
                <p class="property-overview-value">{{ $primaryLine }}</p>
            @endif
            @foreach($secondaryLines as $line)
                <p class="property-overview-value-sub">{{ $line }}</p>
            @endforeach
        @endif
    </div>
</div>
