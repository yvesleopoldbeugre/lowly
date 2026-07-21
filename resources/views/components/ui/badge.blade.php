@props(['variant' => 'neutral'])

@php
    $variants = [
        'success' => 'bg-success-100 text-success-800',
        'warning' => 'bg-warning-100 text-warning-800',
        'danger' => 'bg-danger-100 text-danger-800',
        'neutral' => 'bg-neutral-100 text-neutral-600',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ' . ($variants[$variant] ?? $variants['neutral'])]) }}>
    {{ $slot }}
</span>
