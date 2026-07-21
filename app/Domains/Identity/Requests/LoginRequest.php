<?php

namespace App\Domains\Identity\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Domaine Identity — voir API_GUIDE.md §9 (`POST /api/v1/auth/login`).
 */
class LoginRequest extends FormRequest
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
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['sometimes', 'boolean'],
        ];
    }
}
