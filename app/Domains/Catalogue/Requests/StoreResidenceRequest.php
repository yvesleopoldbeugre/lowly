<?php

namespace App\Domains\Catalogue\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Domaine Catalogue — voir API_GUIDE.md §11 (`POST /api/v1/partner/residences`)
 * et DATABASE.md §6.1.
 */
class StoreResidenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Vérification fine (le partenaire est-il validé ?) déléguée à
        // ResidencePolicy en phase Développement — voir ARCHITECTURE.md §7.
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:120'],
            'capacity' => ['required', 'integer', 'min:1'],
            'daily_rate' => ['required', 'numeric', 'min:0.01'],
            'attributes' => ['sometimes', 'array'],
        ];
    }
}
