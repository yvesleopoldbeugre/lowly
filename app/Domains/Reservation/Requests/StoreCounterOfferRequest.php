<?php

namespace App\Domains\Reservation\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Domaine Reservation — voir API_GUIDE.md §11
 * (`POST /api/v1/partner/reservations/{id}/counter-offer`) et
 * BUSINESS_RULES.md §6 (contre-propositions).
 *
 * Les dates sont optionnelles : à défaut, la contre-proposition reprend
 * les dates de la demande initiale (voir BUSINESS_RULES.md §6.1).
 */
class StoreCounterOfferRequest extends FormRequest
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
            'proposed_reservable_type' => ['required', 'string', 'in:residence,vehicle'],
            'proposed_reservable_id' => ['required', 'uuid'],
            'start_date' => ['sometimes', 'date', 'after_or_equal:today'],
            'end_date' => ['sometimes', 'date', 'after:start_date'],
        ];
    }
}
