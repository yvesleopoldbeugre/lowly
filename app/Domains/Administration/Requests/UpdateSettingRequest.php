<?php

namespace App\Domains\Administration\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Domaine Administration — voir API_GUIDE.md §12 (`PATCH /api/v1/admin/settings`)
 * et DATABASE.md §10.2 (table `platform_settings`).
 */
class UpdateSettingRequest extends FormRequest
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
            'key' => ['required', 'string', 'max:100', 'exists:platform_settings,key'],
            'value' => ['required'],
        ];
    }
}
