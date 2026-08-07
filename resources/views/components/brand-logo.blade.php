@props(['variant' => 'dark'])

<img
    src="{{ asset($variant === 'light' ? 'images/OHC-Trade-Room-light.png' : 'images/logo-dark.png') }}"
    alt="OHC Trade Room"
    {{ $attributes->merge(['class' => 'ohc-shared-brand-logo']) }}
>
