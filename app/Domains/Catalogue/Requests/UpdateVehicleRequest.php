<?php

namespace App\Domains\Catalogue\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Domaine Catalogue — voir API_GUIDE.md §11 (`PATCH /api/v1/partner/vehicles/{id}`).
 */
class UpdateVehicleRequest extends FormRequest
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
            'brand' => ['sometimes', 'required', 'string', 'max:120'],
            'model' => ['sometimes', 'required', 'string', 'max:120'],
            'year' => ['sometimes', 'nullable', 'integer', 'min:1980', 'max:'.(date('Y') + 1)],
            'plate_number' => ['sometimes', 'nullable', 'string', 'max:30'],
            'daily_rate' => ['sometimes', 'required', 'numeric', 'min:0.01'],
            'attributes' => ['sometimes', 'array'],
            'submit_for_validation' => ['sometimes', 'boolean'],
        ];
    }
}
