<?php

namespace App\Domains\Administration\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Domaine Administration — voir API_GUIDE.md §12 (`POST /api/v1/admin/partners/{id}/reject`)
 * et UX_UI.md §7.1.
 */
class RejectPartnerRequest extends FormRequest
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
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
