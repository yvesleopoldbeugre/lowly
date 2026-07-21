<?php

namespace App\Domains\Catalogue\Actions;

use App\Domains\Catalogue\Exceptions\PartnerNotValidatedException;
use App\Domains\Catalogue\Models\Residence;

/**
 * Domaine Catalogue — voir API_GUIDE.md §11 (`PATCH /api/v1/partner/residences/{id}`).
 *
 * Porte la machine à états de l'annonce (voir UML.md §9) : une correction
 * d'une annonce rejetée la repasse en brouillon ; `submit_for_validation`
 * ne fait avancer que depuis brouillon, et nécessite un partenaire validé
 * (sinon PartnerNotValidatedException, 409).
 */
final class UpdateResidence
{
    /**
     * @param  array{title?: string, description?: string, address?: string, city?: string, capacity?: int, daily_rate?: float, attributes?: array<string, mixed>, submit_for_validation?: bool}  $data
     */
    public function executer(Residence $residence, array $data): Residence
    {
        $submitForValidation = $data['submit_for_validation'] ?? false;
        unset($data['submit_for_validation']);

        $residence->fill($data);

        if ($residence->status === 'rejetee') {
            $residence->status = 'brouillon';
        }

        if ($submitForValidation && $residence->status === 'brouillon') {
            if (! $residence->partner->isValidated()) {
                throw new PartnerNotValidatedException;
            }

            $residence->status = 'en_validation';
        }

        $residence->save();

        return $residence;
    }
}
