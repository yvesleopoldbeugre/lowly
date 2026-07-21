<x-layouts::guest :title="$title">
    <div class="flex min-h-[calc(100vh-8rem)] items-center justify-center px-4 py-12">
        <div
            x-data="authTabs('{{ $initialTab }}')"
            class="w-full max-w-md rounded-lg border border-neutral-200 bg-white p-6"
        >
            <a href="{{ url('/') }}" class="mb-6 block text-center text-xl font-semibold text-primary-700">LOWLY</a>

            <div class="mb-6 flex rounded-md bg-neutral-100 p-1 text-sm">
                <button
                    type="button"
                    @click="setTab('connexion')"
                    :class="tab === 'connexion' ? 'bg-white shadow-sm text-neutral-900' : 'text-neutral-500'"
                    class="flex-1 rounded-md px-3 py-1.5 font-medium"
                >Connexion</button>
                <button
                    type="button"
                    @click="setTab('inscription')"
                    :class="tab === 'inscription' ? 'bg-white shadow-sm text-neutral-900' : 'text-neutral-500'"
                    class="flex-1 rounded-md px-3 py-1.5 font-medium"
                >Créer un compte</button>
            </div>

            {{-- Connexion --}}
            <form x-show="tab === 'connexion'" x-data="loginForm()" @submit.prevent="submit" class="flex flex-col gap-4">
                <x-ui.input label="Email" type="email" x-model="email" placeholder="vous@exemple.com" />
                <template x-if="errors.email"><p class="-mt-3 text-xs text-danger-600" x-text="errors.email"></p></template>

                <x-ui.input label="Mot de passe" type="password" x-model="password" placeholder="••••••••" />
                <template x-if="errors.password"><p class="-mt-3 text-xs text-danger-600" x-text="errors.password"></p></template>

                <label class="flex items-center gap-2 text-sm text-neutral-600">
                    <input type="checkbox" x-model="remember" class="rounded border-neutral-300">
                    Se souvenir de moi
                </label>

                <template x-if="generalError"><p class="text-sm text-danger-600" x-text="generalError"></p></template>

                <x-ui.button type="submit" class="w-full justify-center" x-bind:disabled="loading">
                    <span x-show="!loading">Se connecter</span>
                    <span x-show="loading">Connexion…</span>
                </x-ui.button>
            </form>

            {{-- Inscription --}}
            <form x-show="tab === 'inscription'" x-data="registerForm()" @submit.prevent="submit" class="flex flex-col gap-4">
                <x-ui.input label="Nom complet" x-model="full_name" />
                <template x-if="errors.full_name"><p class="-mt-3 text-xs text-danger-600" x-text="errors.full_name"></p></template>

                <x-ui.input label="Email" type="email" x-model="email" placeholder="vous@exemple.com" />
                <template x-if="errors.email"><p class="-mt-3 text-xs text-danger-600" x-text="errors.email"></p></template>

                <x-ui.input label="Mot de passe" type="password" x-model="password" placeholder="••••••••" />
                <template x-if="errors.password"><p class="-mt-3 text-xs text-danger-600" x-text="errors.password"></p></template>

                <x-ui.input label="Confirmer le mot de passe" type="password" x-model="password_confirmation" placeholder="••••••••" />

                <label class="flex items-start gap-2 text-sm text-neutral-600">
                    <input type="checkbox" x-model="wants_partner" class="mt-0.5 rounded border-neutral-300">
                    Je souhaite proposer des résidences ou véhicules en tant que partenaire
                </label>

                <div x-show="wants_partner" class="rounded-md bg-primary-50 p-3 text-xs text-primary-800">
                    Votre profil partenaire sera soumis à validation par l'administration avant toute publication
                    d'annonce (voir PRODUCT.md §8.3).
                </div>

                <template x-if="generalError"><p class="text-sm text-danger-600" x-text="generalError"></p></template>

                <x-ui.button type="submit" class="w-full justify-center" x-bind:disabled="loading">
                    <span x-show="!loading">Créer mon compte</span>
                    <span x-show="loading">Création…</span>
                </x-ui.button>
            </form>
        </div>
    </div>
</x-layouts::guest>
