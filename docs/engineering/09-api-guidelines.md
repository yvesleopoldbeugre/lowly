# 09 — API Guidelines

## Table des matières

1. [Portée du document](#1-portée-du-document)
2. [Structure d'un contrôleur API](#2-structure-dun-contrôleur-api)
3. [Versioning — règles de dépréciation](#3-versioning--règles-de-dépréciation)
4. [Authentification — détail Sanctum](#4-authentification--détail-sanctum)
5. [Pagination — implémentation](#5-pagination--implémentation)
6. [Filtrage et tri](#6-filtrage-et-tri)
7. [Codes d'erreur métier stables](#7-codes-derreur-métier-stables)
8. [Idempotence](#8-idempotence)
9. [Documentation des endpoints](#9-documentation-des-endpoints)

---

## 1. Portée du document

Ce document complète [`API_GUIDE.md`](../../API_GUIDE.md) (conventions générales, liste des endpoints) avec les règles d'implémentation Laravel concrètes.

## 2. Structure d'un contrôleur API

Un contrôleur API suit strictement le même schéma que décrit dans `05-laravel-conventions.md` §2, avec en plus une contrainte de forme de réponse systématique via `JsonResource` :

```php
final class ResidenceController extends Controller
{
    public function index(RechercherResidencesRequest $request): AnonymousResourceCollection
    {
        $residences = app(ResidenceRepository::class)->rechercher($request->toCriteria());

        return ResidenceResource::collection($residences);
    }
}
```

## 3. Versioning — règles de dépréciation

- Une version d'API (`/api/v1`) reste supportée au minimum le temps annoncé publiquement lors de l'introduction de la version suivante (`/api/v2`).
- Toute dépréciation d'endpoint est annoncée via un en-tête de réponse `Deprecation: true` et `Sunset: <date>` avant suppression effective.
- Un changement **rétrocompatible** (ajout de champ optionnel, nouvel endpoint) ne nécessite pas de nouvelle version.
- Un changement **non rétrocompatible** (suppression/renommage de champ, changement de type, changement de code HTTP par défaut) nécessite impérativement une nouvelle version.

## 4. Authentification — détail Sanctum

Pour l'ouverture externe future (application mobile, intégrations partenaires), Laravel Sanctum est le mécanisme cible :

- jetons personnels d'accès (`personal_access_tokens`), scoping par capacité si nécessaire (`reservations:read`, `reservations:write`) ;
- expiration configurable des jetons, révocation possible à tout moment par l'utilisateur depuis son profil.

Pour le MVP interne (Ajax depuis Blade), l'authentification par session reste le mécanisme utilisé, tel que décrit dans [`API_GUIDE.md`](../../API_GUIDE.md) §4 ; Sanctum est préparé mais non activé en mode jeton avant l'ouverture externe.

## 5. Pagination — implémentation

Toute collection paginée utilise le paginator Laravel standard (`paginate()`), transformé par une `ResourceCollection` respectant le format défini dans [`API_GUIDE.md`](../../API_GUIDE.md) §5 :

```php
public function index(Request $request): AnonymousResourceCollection
{
    $perPage = min((int) $request->integer('per_page', 20), 100);

    return ResidenceResource::collection(
        Residence::publiees()->paginate($perPage)
    );
}
```

## 6. Filtrage et tri

- Chaque endpoint de liste documente explicitement ses paramètres de filtre acceptés (voir [`API_GUIDE.md`](../../API_GUIDE.md) §9-12) ; tout paramètre non reconnu est ignoré silencieusement plutôt que de provoquer une erreur, sauf s'il s'agit d'un paramètre requis manquant.
- Le tri, lorsqu'il est proposé, utilise un paramètre unique `sort` avec convention de préfixe `-` pour l'ordre descendant (`?sort=-created_at`).

## 7. Codes d'erreur métier stables

Extrait des codes d'erreur métier stables utilisés par les domaines applicatifs (le format général est défini dans [`API_GUIDE.md`](../../API_GUIDE.md) §7) :

Domaine `Reservation` :

| Code | Situation |
|---|---|
| `reservation_period_unavailable` | La période demandée est déjà bloquée |
| `reservation_invalid_period` | Date de départ non postérieure à la date d'arrivée |
| `reservation_not_owned` | Le partenaire tente d'agir sur une réservation qui ne le concerne pas |
| `counter_offer_expired` | Tentative de réponse à une contre-proposition expirée |
| `counter_offer_already_answered` | Contre-proposition déjà traitée |
| `partner_not_validated` | Un partenaire non validé tente de publier une annonce |

Domaine `Availability` :

| Code | Situation |
|---|---|
| `availability_block_overlap` | La période demandée chevauche un blocage existant sur ce bien (contrainte d'exclusion GiST, voir `DATABASE.md` §7.2) |

Domaine `Administration` :

| Code | Situation |
|---|---|
| `partner_not_pending` | Un administrateur tente de valider ou rejeter un partenaire déjà dans un état terminal différent (ex : rejeter un partenaire déjà validé) |
| `listing_not_pending` | Un administrateur tente de valider ou rejeter une annonce déjà dans un état terminal différent |
| `account_suspended` | Identifiants valides mais compte suspendu par un administrateur (`PATCH /api/v1/admin/users/{id}/suspend`) |

Ces codes sont un contrat stable consommé potentiellement par une interface front ; leur renommage est traité comme un changement non rétrocompatible (§3).

## 8. Idempotence

- Les endpoints `POST` de création (ex : soumission de demande de réservation) acceptent un en-tête `Idempotency-Key` optionnel ; une requête identique rejouée avec la même clé ne crée pas de doublon.
- Les endpoints `PATCH`/`DELETE` sont par nature idempotents et le restent dans leur implémentation (rejouer un `accept` sur une réservation déjà confirmée renvoie l'état actuel sans erreur destructrice).

## 9. Documentation des endpoints

- Toute évolution de la liste d'endpoints listée dans [`API_GUIDE.md`](../../API_GUIDE.md) §9-12 doit être répercutée dans ce document au moment de la Pull Request correspondante, jamais après coup.
- Un endpoint non documenté dans [`API_GUIDE.md`](../../API_GUIDE.md) est considéré comme non stable et ne doit pas être consommé par un client externe.
