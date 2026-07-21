<?php

namespace App\Domains\Catalogue\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Domaine Catalogue — voir API_GUIDE.md §9 (`GET /api/v1/vehicles`).
 */
class ListVehiclesRequest extends FormRequest
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
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => ['nullable', 'numeric', 'min:0'],
            'per_page' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
