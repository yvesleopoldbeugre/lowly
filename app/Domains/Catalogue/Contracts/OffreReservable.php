<?php

namespace App\Domains\Catalogue\Contracts;

/**
 * Contrat commun aux offres réservables du catalogue — voir ARCHITECTURE.md
 * §13 (extensibilité du catalogue) et UML.md §4.4.
 *
 * Toute nouvelle catégorie d'offre future (hôtel, villa, salle, bureau,
 * excursion, chauffeur...) doit implémenter ce contrat pour bénéficier du
 * moteur de réservation existant (domaines Availability, Reservation,
 * Communication, Administration) sans modification de ces domaines.
 */
interface OffreReservable
{
    /**
     * Tarif journalier de référence du bien, tel que défini par le partenaire.
     */
    public function dailyRate(): string;

    /**
     * Indique si le bien est publié et donc visible/réservable publiquement.
     */
    public function isPublished(): bool;
}
