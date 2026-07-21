<?php

namespace App\Domains\Administration\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Domaine Administration — voir API_GUIDE.md §12
 * (`POST /api/v1/admin/listings/{type}/{id}/reject`) et UX_UI.md §7.2
 * (motif de rejet obligatoire).
 */
class RejectListingRequest extends FormRequest
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
            'reason' => ['required', 'string', 'max:1000'],
        ];
    }
}
