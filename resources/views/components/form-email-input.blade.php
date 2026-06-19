@props([
    'id' => null,
    'name' => 'email',
    'required' => true,
    'placeholder' => 'Email',
])

<input
    type="email"
    @if($id) id="{{ $id }}" @endif
    name="{{ $name }}"
    @if($required) required @endif
    placeholder="{{ $placeholder }}"
    maxlength="255"
    autocomplete="email"
    {{ $attributes }}
>
