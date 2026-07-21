<?php

namespace App\Domains\Identity\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Domaine Identity — voir API_GUIDE.md §10 (`PATCH /api/v1/me`)
 * et UX_UI.md §5.4 (écran Profil).
 */
class UpdateProfileRequest extends FormRequest
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
            'full_name' => ['sometimes', 'required', 'string', 'max:255'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:30'],
            'email' => [
                'sometimes',
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->user()?->id),
            ],
        ];
    }
}
