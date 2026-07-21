# ROADMAP.md — Feuille de Route LOWLY

## Table des matières

1. [Principe de construction de la roadmap](#1-principe-de-construction-de-la-roadmap)
2. [Phase 0 — Documentation et conception](#2-phase-0--documentation-et-conception)
3. [Phase 1 — MVP](#3-phase-1--mvp)
4. [Phase 2 — Consolidation post-lancement](#4-phase-2--consolidation-post-lancement)
5. [Phase 3 — Extension du catalogue](#5-phase-3--extension-du-catalogue)
6. [Phase 4 — Monétisation et paiement](#6-phase-4--monétisation-et-paiement)
7. [Phase 5 — Extension internationale](#7-phase-5--extension-internationale)
8. [Priorisation des extensions de catalogue](#8-priorisation-des-extensions-de-catalogue)
9. [Ce qui ne change jamais](#9-ce-qui-ne-change-jamais)

---

## 1. Principe de construction de la roadmap

Cette roadmap suit le principe directeur du projet : **aucun développement sans conception préalable**. Chaque phase ci-dessous ne démarre que lorsque la documentation et la conception de son périmètre sont finalisées et validées.

## 2. Phase 0 — Documentation et conception

**Statut : en cours.**

- Rédaction des documents de référence racine (`README.md`, `PRODUCT.md`, `BUSINESS_RULES.md`, `ARCHITECTURE.md`, `DATABASE.md`, `API_GUIDE.md`, `ENGINEERING.md`, `SECURITY.md`, `TESTING.md`, `DEPLOYMENT.md`, `ROADMAP.md`) — fait.
- Rédaction de l'Engineering Handbook (`docs/engineering/`) — fait.
- Modélisation UML (cas d'utilisation, classes, séquences, états) — fait, voir [`UML.md`](./UML.md).
- Modélisation concrète de la base de données (migrations Laravel, modèles Eloquent, factories, seeders) — fait, voir `database/migrations/`, `app/Domains/*/Models/`, `database/factories/` et `database/seeders/DatabaseSeeder.php`.
- Maquettes UX/UI des écrans MVP — fait, voir [`UX_UI.md`](./UX_UI.md) et `docs/ux/mockups/`.
- Implémentation concrète de l'API interne (`routes/api.php`, contrôleurs squelettes, Form Requests, API Resources) — fait, conforme aux 42 endpoints de [`API_GUIDE.md`](./API_GUIDE.md) §9-12 ; logique métier volontairement absente (réservée à la Phase 1).
- Validation de l'ensemble de la documentation avant tout développement.

## 3. Phase 1 — MVP

Périmètre détaillé dans [`PRODUCT.md`](./PRODUCT.md) §9. Résumé :

- **Public** : consultation, recherche, détail d'annonce, création de compte, connexion.
- **Client** : recherche, réservation, historique, profil, notifications.
- **Partenaire** : tableau de bord, gestion résidences/véhicules, disponibilités, réservations, tarifs, photos.
- **Administration** : validation partenaires/annonces, gestion utilisateurs, statistiques, paramètres.

Catégories d'offres couvertes : résidences meublées et véhicules de location uniquement.

Ce périmètre est **fermé** : toute demande d'ajout de fonctionnalité pendant cette phase doit être explicitement reportée à une phase ultérieure, sauf décision documentée contraire.

## 4. Phase 2 — Consolidation post-lancement

Objectif : stabiliser le MVP en conditions réelles avant toute extension de périmètre.

- Correction des anomalies détectées en production.
- Ajustement des seuils opérationnels (délais de réponse partenaire, délai d'expiration des contre-propositions) sur la base de l'usage réel.
- Mise en place d'un système d'avis et de notation (identifié comme hors périmètre MVP dans [`PRODUCT.md`](./PRODUCT.md) §10).
- Amélioration continue de la recherche (filtres avancés, tri par pertinence).

## 5. Phase 3 — Extension du catalogue

Ajout progressif de nouvelles catégories d'offres, en s'appuyant sur l'abstraction « Offre réservable » définie dans [`ARCHITECTURE.md`](./ARCHITECTURE.md) §13 :

```
Résidences, Véhicules (MVP)
        │
        ▼
   Villas, Appartements (extension naturelle du logement)
        │
        ▼
   Hôtels (nécessite gestion de chambres multiples — étude d'impact requise)
        │
        ▼
   Salles, Bureaux (nouveaux cas d'usage professionnels)
        │
        ▼
   Excursions, Chauffeurs (services non liés à un bien physique fixe)
```

Chaque nouvelle catégorie fait l'objet d'un cycle de conception complet (voir [`ENGINEERING.md`](./ENGINEERING.md) §2) avant tout développement, incluant une réévaluation des règles métier de [`BUSINESS_RULES.md`](./BUSINESS_RULES.md) (le principe de journée à 12h-12h peut ne pas s'appliquer tel quel à une excursion ou un chauffeur, par exemple).

## 6. Phase 4 — Monétisation et paiement

- Intégration d'une passerelle de paiement en ligne (actuellement hors périmètre MVP, voir [`PRODUCT.md`](./PRODUCT.md) §10).
- Mise en place d'un modèle de commission ou d'abonnement partenaire (à définir).
- Facturation automatisée.

Cette phase impose une mise à jour majeure de [`SECURITY.md`](./SECURITY.md) (conformité PCI-DSS via prestataire tiers) et de [`DATABASE.md`](./DATABASE.md) (modélisation des transactions financières).

## 7. Phase 5 — Extension internationale

- Support multilingue de l'interface.
- Support multi-devises pour les tarifs et paiements.
- Adaptation des règles métier aux spécificités locales éventuelles (fuseaux horaires pour le calcul des journées, notamment).

## 8. Priorisation des extensions de catalogue

| Extension | Complexité estimée | Justification de priorité |
|---|---|---|
| Villas | Faible | Très proche du modèle « résidence » existant |
| Appartements | Faible | Variante de résidence, quasi sans impact structurel |
| Bureaux | Moyenne | Nouveau cas d'usage (durée courte, usage professionnel) mais structure similaire |
| Salles | Moyenne | Réservation possiblement à l'heure plutôt qu'à la journée — impact sur `BUSINESS_RULES.md` |
| Hôtels | Élevée | Nécessite une gestion multi-unités (chambres) par annonce |
| Excursions | Élevée | Pas de « bien » réservé mais un « créneau » — remet en cause la notion de calendrier de blocage |
| Chauffeurs | Élevée | Service à la personne, logique de disponibilité différente (créneaux, zones géographiques) |

## 9. Ce qui ne change jamais

Indépendamment des phases futures, les principes suivants restent invariants :

- LOWLY reste une **marketplace de mise en relation**, jamais un exploitant direct (voir [`PRODUCT.md`](./PRODUCT.md) §2-3).
- Le cycle **Demande → Validation → Confirmation** reste le mécanisme central de toute réservation, quelle que soit la catégorie d'offre.
- Aucune extension de périmètre n'est développée sans mise à jour préalable de la documentation de référence correspondante.
