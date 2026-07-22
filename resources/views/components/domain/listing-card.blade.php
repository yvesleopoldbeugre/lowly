@props(['title', 'subtitle', 'dailyRate', 'photoUrl' => null, 'href'])

<article class="flex flex-col overflow-hidden rounded-lg border border-neutral-200 bg-white transition hover:shadow-md">
    <a href="{{ $href }}">
        <img
            src="{{ $photoUrl ?? 'https://placehold.co/400x280?text=LOWLY' }}"
            alt="{{ $title }}"
            class="h-40 w-full object-cover"
        >
        <div class="flex flex-col gap-1 p-4">
            <h3 class="text-sm font-semibold text-neutral-900">{{ $title }}</h3>
            <p class="text-sm text-neutral-500">{{ $subtitle }}</p>
            <p class="text-sm font-semibold text-primary-700">{{ $dailyRate }} FCFA / jour</p>
        </div>
    </a>
</article>
