<?php

namespace App\Domains\Identity\Actions;

use App\Domains\Identity\Models\User;
use App\Domains\Partners\Models\Partner;
use Illuminate\Support\Facades\Auth;

/**
 * Domaine Identity — voir API_GUIDE.md §9 (`POST /api/v1/auth/register`) et
 * PRODUCT.md §9.1. Ouvre la session immédiatement après la création du
 * compte (pas de connexion séparée requise).
 */
final class RegisterUser
{
    /**
     * @param  array{full_name: string, email: string, password: string, phone?: ?string, wants_partner?: bool}  $data
     */
    public function executer(array $data): User
    {
        $user = User::create([
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            // Le cast `hashed` du modèle User se charge du hachage.
            'password' => $data['password'],
            'phone' => $data['phone'] ?? null,
            // Le rôle n'est jamais déduit de l'input client : l'inscription
            // publique ne crée que des comptes `client` (RegisterRequest ne
            // collecte volontairement aucun champ `role`).
            'role' => 'client',
        ]);

        if ($data['wants_partner'] ?? false) {
            // Profil Partner en attente de validation — aucun document ni
            // raison sociale collecté à ce stade, voir DATABASE.md §5.1.
            Partner::create([
                'user_id' => $user->id,
                'status' => 'en_attente',
            ]);
        }

        Auth::login($user);
        request()->session()->regenerate();

        return $user;
    }
}
