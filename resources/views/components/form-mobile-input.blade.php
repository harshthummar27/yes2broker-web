@props([
    'id' => null,
    'name' => 'phone',
    'required' => true,
    'placeholder' => 'Mobile Number',
])

<input
    type="tel"
    @if($id) id="{{ $id }}" @endif
    name="{{ $name }}"
    @if($required) required @endif
    placeholder="{{ $placeholder }}"
    inputmode="numeric"
    maxlength="10"
    @if($required) minlength="10" @endif
    pattern="[0-9]{10}"
    title="Enter a 10-digit mobile number"
    autocomplete="tel"
    {{ $attributes }}
>
