<?php

namespace App\Domains\Catalogue\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Domaine Catalogue — voir API_GUIDE.md §11 (`PATCH /api/v1/partner/residences/{id}`).
 */
class UpdateResidenceRequest extends FormRequest
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
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'required', 'string'],
            'address' => ['sometimes', 'required', 'string', 'max:255'],
            'city' => ['sometimes', 'required', 'string', 'max:120'],
            'capacity' => ['sometimes', 'required', 'integer', 'min:1'],
            'daily_rate' => ['sometimes', 'required', 'numeric', 'min:0.01'],
            'attributes' => ['sometimes', 'array'],
            // Une édition ne republie pas automatiquement l'annonce : voir
            // le cycle de validation UX_UI.md §6.2 et §7.2.
            'submit_for_validation' => ['sometimes', 'boolean'],
        ];
    }
}
