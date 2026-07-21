<?php

namespace App\Domains\Catalogue\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Domaine Catalogue — voir API_GUIDE.md §11 (`POST /api/v1/partner/vehicles`)
 * et DATABASE.md §6.3.
 */
class StoreVehicleRequest extends FormRequest
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
            'brand' => ['required', 'string', 'max:120'],
            'model' => ['required', 'string', 'max:120'],
            'year' => ['nullable', 'integer', 'min:1980', 'max:'.(date('Y') + 1)],
            'plate_number' => ['nullable', 'string', 'max:30'],
            'daily_rate' => ['required', 'numeric', 'min:0.01'],
            'attributes' => ['sometimes', 'array'],
        ];
    }
}
