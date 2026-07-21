<?php

namespace App\Domains\Catalogue\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Domaine Catalogue — voir API_GUIDE.md §11 (`POST /api/v1/partner/residences/{id}/photos`)
 * et DATABASE.md §6.2.
 */
class StoreResidencePhotoRequest extends FormRequest
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
            'photo' => ['required', 'image', 'max:5120'],
            'position' => ['sometimes', 'integer', 'min:0'],
        ];
    }
}
