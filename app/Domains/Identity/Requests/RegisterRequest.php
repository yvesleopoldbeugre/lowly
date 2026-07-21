<?php

namespace App\Domains\Identity\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

/**
 * Domaine Identity — voir API_GUIDE.md §9 (`POST /api/v1/auth/register`)
 * et PRODUCT.md §9.1 (création de compte, y compris demande de rôle partenaire).
 */
class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'phone' => ['nullable', 'string', 'max:30'],
            // N'active pas directement le rôle `partner` : déclenche la
            // soumission d'un profil Partner à l'état `en_attente` — voir
            // DATABASE.md §5.1 et UX_UI.md §4.4.
            'wants_partner' => ['sometimes', 'boolean'],
        ];
    }
}
