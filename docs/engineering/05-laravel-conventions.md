# 05 — Conventions Laravel

## Table des matières

1. [Portée du document](#1-portée-du-document)
2. [Controllers](#2-controllers)
3. [Form Requests](#3-form-requests)
4. [Policies](#4-policies)
5. [Actions](#5-actions)
6. [Services](#6-services)
7. [Repositories](#7-repositories)
8. [Resources](#8-resources)
9. [Models Eloquent](#9-models-eloquent)
10. [Blade Components](#10-blade-components)
11. [Jobs](#11-jobs)
12. [Events et Listeners](#12-events-et-listeners)
13. [Notifications](#13-notifications)
14. [Conventions de nommage générales](#14-conventions-de-nommage-générales)

---

## 1. Portée du document

Ce document fixe les conventions Laravel concrètes que tout code LOWLY doit respecter, classe par classe. Il s'appuie sur les principes posés dans `03-engineering-principles.md` et l'architecture décrite dans `04-architecture.md`.

## 2. Controllers

- Un contrôleur par ressource (`ResidenceController`, `ReservationController`), suivant les conventions REST Laravel (`index`, `show`, `store`, `update`, `destroy`).
- Une méthode de contrôleur ne dépasse jamais ~15 lignes. Si elle grossit, la logique doit être extraite dans une `Action` ou un `Service`.
- Un contrôleur n'accède **jamais** directement à Eloquent : il passe par une `Action`, un `Service` ou un `Repository`.

```php
final class ReservationController extends Controller
{
    public function store(CreerReservationRequest $request, CreerDemandeReservation $action): JsonResponse
    {
        $this->authorize('create', Reservation::class);

        $reservation = $action->executer($request->toDto());

        return ReservationResource::make($reservation)
            ->response()
            ->setStatusCode(201);
    }
}
```

## 3. Form Requests

- Toute entrée utilisateur passe par une `FormRequest` dédiée, nommée selon l'action (`CreerReservationRequest`, `ValiderPartenaireRequest`).
- La méthode `rules()` reflète strictement les contraintes définies dans [`DATABASE.md`](../../DATABASE.md) et [`BUSINESS_RULES.md`](../../BUSINESS_RULES.md) (ex : date de départ postérieure à la date d'arrivée).
- La méthode `authorize()` d'une `FormRequest` ne fait qu'une vérification de premier niveau (utilisateur authentifié) ; l'autorisation métier fine reste dans une `Policy`.

## 4. Policies

- Une `Policy` par modèle principal (`ResidencePolicy`, `ReservationPolicy`, `PartnerPolicy`).
- Chaque méthode de policy correspond à une action métier explicite, pas seulement aux verbes CRUD génériques : `accepter()`, `refuser()`, `proposerAlternative()` en plus de `view()`, `update()`, `delete()`.
- Aucune vérification de policy n'est dupliquée dans une vue Blade sans passer par la directive `@can` qui invoque la même policy.

## 5. Actions

- Une `Action` par cas d'usage métier unitaire, nommée à l'infinitif ou en substantif d'action : `ConfirmerReservation`, `CreerContreProposition`, `ValiderPartenaire`.
- Une `Action` expose une seule méthode publique, `executer()` (ou `handle()`), qui prend un DTO ou des paramètres explicites en entrée et retourne le résultat (modèle, DTO).
- Une `Action` peut orchestrer plusieurs `Services` et `Repositories`, mais ne contient pas elle-même de requête SQL directe.

```php
final class ConfirmerReservation
{
    public function __construct(
        private DisponibiliteVerifiable $disponibilite,
        private ReservationRepository $reservations,
    ) {}

    public function executer(Reservation $reservation): Reservation
    {
        // logique métier de confirmation, émission d'événement
    }
}
```

## 6. Services

- Un `Service` porte une logique métier **transverse**, réutilisée par plusieurs `Actions` (ex : `CalculateurJournees`, voir `04-architecture.md` §6).
- Un `Service` est sans état et ne connaît jamais le contexte HTTP (pas de `Request` injectée dans un `Service`).

## 7. Repositories

- Un `Repository` par agrégat principal (`ReservationRepository`, `ResidenceRepository`), qui encapsule les requêtes Eloquent complexes (jointures, filtres de recherche).
- Les requêtes Eloquent simples (`Model::find()`, relations directes) peuvent être appelées directement depuis une `Action` sans passer par un `Repository` dédié — ne pas créer de `Repository` pour le seul plaisir de la couche d'abstraction (principe KISS, `03-engineering-principles.md` §4).

## 8. Resources

- Toute donnée exposée en réponse API passe par une `JsonResource` Laravel dédiée (`ReservationResource`, `ResidenceResource`), conforme au format décrit dans [`API_GUIDE.md`](../../API_GUIDE.md) §6.
- Une `Resource` ne contient aucune logique métier : uniquement du formatage de données déjà calculées.

## 9. Models Eloquent

- Un modèle par table, situé dans `Models/` du domaine correspondant.
- Les relations Eloquent expriment le vocabulaire métier du glossaire (`glossary.md`), pas une terminologie technique générique.
- Les `Models` peuvent porter des scopes de requête simples (`scopePubliees()`, `scopeDisponibles()`) mais aucune logique d'orchestration multi-étapes (réservée aux `Actions`).
- Tout modèle représentant une entité soumise à soft delete déclare explicitement le trait `SoftDeletes`, conformément à [`DATABASE.md`](../../DATABASE.md) §1.

## 10. Blade Components

Voir le détail complet dans `06-blade-tailwind-guidelines.md`. Règle de convention rapide : un composant Blade par élément d'interface réutilisable, nommé en `kebab-case` (`<x-reservation-status-badge>`).

## 11. Jobs

- Un `Job` par tâche asynchrone unitaire (`EnvoyerNotificationReservationConfirmee`, `RedimensionnerPhotoAnnonce`).
- Chaque `Job` implémente `ShouldQueue`, définit un nombre de tentatives (`$tries`) et une politique de nouvel essai (`backoff()`) explicites.
- Un `Job` est idempotent : son exécution répétée (en cas de nouvel essai après échec partiel) ne doit jamais produire un effet de bord dupliqué (ex : double notification).

## 12. Events et Listeners

- Un `Event` par changement d'état métier significatif, nommé au passé (`ReservationConfirmee`, `PartenaireValide`), conformément à `04-architecture.md` §7.
- Un `Listener` par réaction unitaire à un événement (`BloquerCalendrier`, `NotifierClientConfirmation`) — pas de listener « fourre-tout » qui réagit à plusieurs préoccupations différentes.
- Les abonnements Event → Listener sont déclarés explicitement dans `EventServiceProvider`, jamais découverts implicitement par convention de nommage seule.

## 13. Notifications

- Une classe `Notification` Laravel par type de notification métier (`ReservationConfirmeeNotification`), dans `Domains/Communication/Notifications/`.
- Chaque notification définit ses canaux (`database`, `mail`) de façon explicite dans `via()`.

## 14. Conventions de nommage générales

| Élément | Convention |
|---|---|
| Classe | `PascalCase`, nom métier explicite |
| Méthode | `camelCase`, verbe d'action |
| Variable | `camelCase` |
| Table de base de données | `snake_case`, pluriel |
| Route nommée | `snake_case` avec points (`reservations.store`) |
| Fichier de vue Blade | `kebab-case` (`reservation-detail.blade.php`) |
| Fichier de test | `NomDeLaClasseTest.php` ou `nom_du_scenario.php` (Pest) |

Le respect strict de ces conventions est vérifié en revue de code (`14-code-review.md`) et, pour les aspects automatisables, par le linter (Laravel Pint) en CI (`15-deployment.md`).
