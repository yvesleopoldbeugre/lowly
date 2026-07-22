@props(['variant' => 'primary', 'href' => null, 'type' => 'button'])

@php
    $variants = [
        'primary' => 'bg-primary-600 text-white hover:bg-primary-700',
        'secondary' => 'border border-neutral-300 text-neutral-700 hover:bg-neutral-100',
        'ghost' => 'text-neutral-700 hover:bg-neutral-100',
        'danger' => 'bg-danger-600 text-white hover:bg-danger-700',
    ];

    $classes = 'inline-flex items-center justify-center rounded-md px-4 py-2 text-sm font-medium transition disabled:cursor-not-allowed disabled:opacity-50 '
        . $variants[$variant];
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
