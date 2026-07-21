# Glossaire Métier LOWLY

Ce glossaire fixe le vocabulaire métier officiel de LOWLY. Chaque terme doit être utilisé de façon identique dans le code, la documentation et les échanges d'équipe — aucun synonyme informel ne doit se substituer à ces termes dans le code (voir principe DDD, `03-engineering-principles.md` §6).

## A

**Administrateur** — Utilisateur interne à LOWLY chargé de valider les partenaires et les annonces, gérer les utilisateurs et superviser la plateforme. Voir [`PRODUCT.md`](../../PRODUCT.md) §6.4.

**Annonce** — Représentation publique d'une résidence ou d'un véhicule, incluant photos, description et tarif, soumise à validation avant publication.

## B

**Blocage** — Période d'indisponibilité d'un bien dans le calendrier, d'origine automatique (réservation confirmée) ou manuelle (entretien, maintenance, usage personnel). Voir [`BUSINESS_RULES.md`](../../BUSINESS_RULES.md) §7.

## C

**Calendrier de disponibilité** — Représentation, pour un bien donné, des périodes disponibles et bloquées. Porté par le domaine `Availability`.

**Client** — Utilisateur authentifié cherchant à réserver une résidence ou un véhicule. Voir [`PRODUCT.md`](../../PRODUCT.md) §6.2.

**Confirmation** — Étape du cycle de réservation où la demande devient définitive et bloque automatiquement le calendrier. Voir [`BUSINESS_RULES.md`](../../BUSINESS_RULES.md) §5.3.

**Contre-proposition** — Alternative (autre bien, éventuellement autres dates) soumise par le partenaire après refus d'une demande initiale. Voir [`BUSINESS_RULES.md`](../../BUSINESS_RULES.md) §6.

## D

**Demande (de réservation)** — Requête soumise par un client pour réserver un bien sur une période donnée, non encore validée par le partenaire. Voir [`BUSINESS_RULES.md`](../../BUSINESS_RULES.md) §5.1.

## E

**État (de réservation)** — Valeur du cycle de vie d'une réservation : `EN_ATTENTE`, `CONFIRMÉE`, `REFUSÉE`, `CONTRE-PROPOSÉE`, `EXPIRÉE`. Voir [`BUSINESS_RULES.md`](../../BUSINESS_RULES.md) §8.

## J

**Journée** — Unité de facturation d'une réservation, correspondant à un cycle fixe de 12h00 à 12h00 le lendemain. Voir [`BUSINESS_RULES.md`](../../BUSINESS_RULES.md) §3.1.

## M

**Marketplace** — Modèle économique et produit de LOWLY : mise en relation entre offre (partenaires) et demande (clients), sans exploitation directe des biens par la plateforme. Voir [`PRODUCT.md`](../../PRODUCT.md) §2.

## O

**Offre réservable** — Abstraction technique commune à toute catégorie de bien pouvant être réservée (résidence, véhicule, et futures catégories). Voir [`ARCHITECTURE.md`](../../ARCHITECTURE.md) §13.

## P

**Partenaire** — Utilisateur authentifié et validé par l'administration, proposant une ou plusieurs résidences et/ou véhicules à la réservation. Voir [`PRODUCT.md`](../../PRODUCT.md) §6.3.

**PMS (Property Management System)** — Système de gestion opérationnelle interne d'un bien (ménage, maintenance, comptabilité). LOWLY n'est explicitement pas un PMS. Voir [`PRODUCT.md`](../../PRODUCT.md) §3.

## R

**Réservation** — Engagement, une fois confirmé, entre un client et un partenaire portant sur un bien et une période déterminée.

**Résidence (meublée)** — Logement complet ou partiel, meublé et équipé, proposé à la réservation par un partenaire. Catégorie d'offre du MVP.

## S

**Statut (d'annonce)** — Valeur du cycle de vie d'une annonce : `brouillon`, `en_validation`, `publiee`, `rejetee`, `suspendue`. Voir [`DATABASE.md`](../../DATABASE.md) §6.

**Statut (de partenaire)** — Valeur du cycle de validation d'un compte partenaire : `en_attente`, `valide`, `rejete`, `suspendu`. Voir [`DATABASE.md`](../../DATABASE.md) §5.

## V

**Validation** — Étape du cycle où le partenaire examine une demande et l'accepte, la refuse, ou propose une alternative. Également utilisé pour la validation d'un partenaire ou d'une annonce par l'administration. Voir [`BUSINESS_RULES.md`](../../BUSINESS_RULES.md) §5.2 et [`PRODUCT.md`](../../PRODUCT.md) §8.3.

**Véhicule (de location)** — Véhicule particulier proposé à la réservation journalière par un partenaire. Catégorie d'offre du MVP.

**Visiteur** — Utilisateur non authentifié, pouvant consulter le catalogue et créer un compte. Voir [`PRODUCT.md`](../../PRODUCT.md) §6.1.
