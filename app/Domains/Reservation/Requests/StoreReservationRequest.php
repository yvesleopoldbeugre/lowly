<?php

namespace App\Domains\Reservation\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Domaine Reservation — voir API_GUIDE.md §10 (`POST /api/v1/reservations`)
 * et BUSINESS_RULES.md §5.1 (étape 1 — Demande).
 *
 * Le calcul de `nights_count` et `total_amount` n'est jamais fourni par le
 * client : il est dérivé côté serveur à partir des dates et du tarif du
 * bien au moment de la demande — voir BUSINESS_RULES.md §3.2 et §9.
 */
class StoreReservationRequest extends FormRequest
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
            'reservable_type' => ['required', 'string', 'in:residence,vehicle'],
            'reservable_id' => ['required', 'uuid'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after:start_date'],
        ];
    }
}
