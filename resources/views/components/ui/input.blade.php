@props(['label' => null, 'type' => 'text', 'name' => null, 'error' => null])

<label class="block">
    @if ($label)
        <span class="mb-1 block text-xs font-medium text-neutral-500">{{ $label }}</span>
    @endif
    <input
        type="{{ $type }}"
        @if ($name) name="{{ $name }}" @endif
        {{ $attributes->merge(['class' => 'w-full rounded-md border border-neutral-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500']) }}
    >
    @if ($error)
        <span class="mt-1 block text-xs text-danger-600">{{ $error }}</span>
    @endif
</label>
