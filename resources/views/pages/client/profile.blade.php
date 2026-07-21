<x-layouts::guest :title="$title">
    <div class="mx-auto max-w-xl px-4 py-10">
        <h1 class="mb-6 text-2xl font-semibold text-neutral-900">Mon profil</h1>

        <x-ui.card
            x-data="updateProfileForm({{ Illuminate\Support\Js::from(['full_name' => $user->full_name, 'email' => $user->email, 'phone' => $user->phone]) }})"
        >
            <form @submit.prevent="submit" class="flex flex-col gap-4">
                <x-ui.input label="Nom complet" x-model="full_name" />
                <template x-if="errors.full_name"><p class="-mt-3 text-xs text-danger-600" x-text="errors.full_name"></p></template>

                <x-ui.input label="Email" type="email" x-model="email" />
                <template x-if="errors.email"><p class="-mt-3 text-xs text-danger-600" x-text="errors.email"></p></template>

                <x-ui.input label="Téléphone" type="tel" x-model="phone" />
                <template x-if="errors.phone"><p class="-mt-3 text-xs text-danger-600" x-text="errors.phone"></p></template>

                <template x-if="generalError"><p class="text-sm text-danger-600" x-text="generalError"></p></template>
                <template x-if="saved"><p class="text-sm text-success-700">Profil mis à jour.</p></template>

                <x-ui.button type="submit" x-bind:disabled="loading" class="self-start">
                    <span x-show="!loading">Enregistrer</span>
                    <span x-show="loading">Enregistrement…</span>
                </x-ui.button>
            </form>
        </x-ui.card>
    </div>
</x-layouts::guest>
