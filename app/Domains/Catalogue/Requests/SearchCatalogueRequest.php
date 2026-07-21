<?php

namespace App\Domains\Catalogue\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Domaine Catalogue — voir API_GUIDE.md §9 (`GET /api/v1/search`).
 *
 * `start_date`/`end_date` sont volontairement refusés (`prohibited`) : les
 * filtrer nécessiterait de lire `availability_blocks`, ce qui inverserait
 * la dépendance ARCHITECTURE.md §14 (`Availability` dépend de `Catalogue`,
 * jamais l'inverse). À réévaluer quand le domaine Availability existera.
 */
class SearchCatalogueRequest extends FormRequest
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
            'type' => ['required', 'in:residence,vehicle'],
            'city' => ['nullable', 'string', 'max:120'],
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => ['nullable', 'numeric', 'min:0'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1'],
            'start_date' => ['prohibited'],
            'end_date' => ['prohibited'],
        ];
    }
}
