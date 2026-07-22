<x-layouts::app :title="$title">
    <div class="mb-4 flex flex-wrap gap-3">
        <select
            onchange="window.location.href = '{{ route('admin.users.index') }}?role=' + this.value + '&status={{ $status }}'"
            class="rounded-md border border-neutral-300 px-3 py-2 text-sm"
        >
            <option value="" @selected(!$role)>Tous les rôles</option>
            <option value="client" @selected($role === 'client')>Client</option>
            <option value="partner" @selected($role === 'partner')>Partenaire</option>
            <option value="admin" @selected($role === 'admin')>Administrateur</option>
        </select>

        <select
            onchange="window.location.href = '{{ route('admin.users.index') }}?role={{ $role }}&status=' + this.value"
            class="rounded-md border border-neutral-300 px-3 py-2 text-sm"
        >
            <option value="" @selected(!$status)>Tous les statuts</option>
            <option value="active" @selected($status === 'active')>Actif</option>
            <option value="suspended" @selected($status === 'suspended')>Suspendu</option>
        </select>
    </div>

    <x-ui.card class="overflow-x-auto p-0">
        <table class="w-full text-left text-sm">
            <thead class="text-xs uppercase text-neutral-400">
                <tr>
                    <th class="px-5 py-3">Nom</th>
                    <th class="px-5 py-3">Email</th>
                    <th class="px-5 py-3">Rôle</th>
                    <th class="px-5 py-3">Statut</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-100">
                @forelse ($users as $user)
                    <tr>
                        <td class="px-5 py-3">{{ $user->full_name }}</td>
                        <td class="px-5 py-3 text-neutral-500">{{ $user->email }}</td>
                        <td class="px-5 py-3">{{ $user->role }}</td>
                        <td class="px-5 py-3">
                            <x-ui.badge :variant="$user->suspended_at ? 'danger' : 'success'">
                                {{ $user->suspended_at ? 'Suspendu' : 'Actif' }}
                            </x-ui.badge>
                        </td>
                        <td class="px-5 py-3">
                            @unless ($user->suspended_at)
                                <span x-data="adminUserSuspend('{{ $user->id }}')">
                                    <button type="button" x-bind:disabled="loading" @click="suspend()" class="text-danger-700 hover:underline">
                                        Suspendre
                                    </button>
                                </span>
                            @endunless
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-6 text-center text-neutral-500">Aucun utilisateur trouvé.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-ui.card>

    <div class="mt-6">{{ $users->links() }}</div>
</x-layouts::app>
