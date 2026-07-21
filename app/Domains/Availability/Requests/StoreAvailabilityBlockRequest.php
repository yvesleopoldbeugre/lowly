<?php

namespace App\Domains\Availability\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Domaine Availability — voir API_GUIDE.md §11 (`POST /api/v1/partner/availability-blocks`)
 * et BUSINESS_RULES.md §4.2 (blocages manuels véhicule : entretien, maintenance,
 * usage personnel) et §7.1 (blocage manuel résidence : indisponibilité générique,
 * `autre`).
 *
 * Ne couvre que les blocages d'origine manuelle : un blocage d'origine
 * `reservation` est créé exclusivement par le système lors de la
 * confirmation d'une réservation (voir ARCHITECTURE.md §8.2), jamais via
 * cet endpoint.
 */
class StoreAvailabilityBlockRequest extends FormRequest
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
            'blockable_type' => ['required', 'string', 'in:residence,vehicle'],
            'blockable_id' => ['required', 'uuid'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'origin' => ['required', 'string', 'in:entretien,maintenance,usage_personnel,autre'],
        ];
    }
}
