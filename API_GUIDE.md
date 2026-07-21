# API_GUIDE.md — Guide API LOWLY

## Table des matières

1. [Portée de l'API](#1-portée-de-lapi)
2. [Conventions générales](#2-conventions-générales)
3. [Versioning](#3-versioning)
4. [Authentification](#4-authentification)
5. [Pagination](#5-pagination)
6. [Format des réponses](#6-format-des-réponses)
7. [Gestion des erreurs](#7-gestion-des-erreurs)
8. [Codes HTTP utilisés](#8-codes-http-utilisés)
9. [Endpoints — Public](#9-endpoints--public)
10. [Endpoints — Client](#10-endpoints--client)
11. [Endpoints — Partenaire](#11-endpoints--partenaire)
12. [Endpoints — Administration](#12-endpoints--administration)
13. [Rate limiting](#13-rate-limiting)

---

## 1. Portée de l'API

LOWLY n'expose pas d'API publique tierce au MVP. L'« API » décrite ici est l'**API interne** consommée par les vues Blade et les composants Alpine.js (recherche dynamique, calendrier de disponibilité, upload de photos, actions du tableau de bord partenaire). Elle suit néanmoins des conventions REST strictes, afin de pouvoir être ouverte à des consommateurs externes (application mobile, intégrations partenaires) sans refonte majeure en post-MVP.

## 2. Conventions générales

- Toutes les routes API sont préfixées par `/api`.
- Les ressources sont nommées au pluriel et en anglais dans les URLs (`/api/v1/residences`, `/api/v1/reservations`), pour rester cohérent avec les standards REST, même si le reste de l'application (Blade, contenu) est en français.
- Les corps de requête et de réponse sont en `application/json`.
- Les dates sont systématiquement au format ISO 8601 (`2026-01-10`).
- Les identifiants exposés sont des UUID, jamais des identifiants séquentiels internes.

## 3. Versioning

L'API est versionnée dans l'URL : `/api/v1/...`. Toute évolution non rétrocompatible (suppression de champ, changement de type, changement de comportement) impose une nouvelle version (`/api/v2/...`). Les règles précises de dépréciation sont détaillées dans `docs/engineering/09-api-guidelines.md`.

## 4. Authentification

- L'authentification interne (session Blade) utilise le système de sessions Laravel standard (cookies signés, CSRF).
- Les appels API internes en Ajax (Alpine.js) réutilisent la session active et le jeton CSRF injecté dans la page.
- Pour une future ouverture externe (application mobile, intégrations), l'authentification par jeton (Laravel Sanctum) est prévue comme mécanisme cible.

```
Header requis pour les requêtes Ajax internes :
  X-CSRF-TOKEN: <token de session>
  Accept: application/json
```

## 5. Pagination

Toute collection retourne une pagination standardisée :

```json
{
  "data": [ ... ],
  "meta": {
    "current_page": 1,
    "per_page": 20,
    "total": 134,
    "last_page": 7
  },
  "links": {
    "first": "/api/v1/residences?page=1",
    "last": "/api/v1/residences?page=7",
    "prev": null,
    "next": "/api/v1/residences?page=2"
  }
}
```

Paramètres de requête standard : `page`, `per_page` (défaut 20, maximum 100).

## 6. Format des réponses

### 6.1 Réponse ressource unique

```json
{
  "data": {
    "id": "b3f1...",
    "type": "residence",
    "attributes": {
      "title": "Appartement 2 pièces centre-ville",
      "daily_rate": "45.00",
      "city": "Abidjan",
      "status": "publiee"
    }
  }
}
```

### 6.2 Réponse ressource liée (exemple réservation avec bien)

```json
{
  "data": {
    "id": "e91a...",
    "type": "reservation",
    "attributes": {
      "period": { "start": "2026-01-10", "end": "2026-01-13" },
      "nights_count": 3,
      "total_amount": "135.00",
      "status": "confirmee"
    },
    "relationships": {
      "reservable": { "id": "b3f1...", "type": "residence" }
    }
  }
}
```

## 7. Gestion des erreurs

Toute erreur suit un format unique :

```json
{
  "error": {
    "code": "reservation_period_unavailable",
    "message": "La période demandée n'est plus disponible pour ce bien.",
    "details": {
      "reservable_id": "b3f1...",
      "period": { "start": "2026-01-10", "end": "2026-01-13" }
    }
  }
}
```

Les codes d'erreur métier sont stables et documentés (ne changent pas entre versions mineures), contrairement aux messages, qui peuvent être reformulés.

## 8. Codes HTTP utilisés

| Code | Usage |
|---|---|
| `200 OK` | Succès en lecture ou action sans création |
| `201 Created` | Création réussie (ex : nouvelle demande de réservation) |
| `204 No Content` | Action réussie sans contenu à retourner |
| `400 Bad Request` | Requête mal formée |
| `401 Unauthorized` | Utilisateur non authentifié |
| `403 Forbidden` | Utilisateur authentifié mais non autorisé (Policy) |
| `404 Not Found` | Ressource inexistante |
| `409 Conflict` | Conflit métier (ex : période déjà bloquée) |
| `422 Unprocessable Entity` | Erreur de validation des données |
| `429 Too Many Requests` | Limite de fréquence dépassée |
| `500 Internal Server Error` | Erreur serveur non gérée |

## 9. Endpoints — Public

| Méthode | Route | Description |
|---|---|---|
| `GET` | `/api/v1/residences` | Liste des résidences publiées, filtrable |
| `GET` | `/api/v1/residences/{id}` | Détail d'une résidence |
| `GET` | `/api/v1/vehicles` | Liste des véhicules publiés, filtrable |
| `GET` | `/api/v1/vehicles/{id}` | Détail d'un véhicule |
| `GET` | `/api/v1/search` | Recherche transverse (résidences + véhicules) |
| `POST` | `/api/v1/auth/register` | Création de compte (connecte la session immédiatement) |
| `POST` | `/api/v1/auth/login` | Connexion |
| `POST` | `/api/v1/auth/logout` | Déconnexion (tout rôle authentifié) |

Paramètres de filtrage courants sur `/api/v1/search` : `type` (`residence`\|`vehicle`), `city`, `start_date`, `end_date`, `min_price`, `max_price`, `capacity`.

`/api/v1/auth/register` et `/api/v1/auth/login` sont limités à 5 tentatives par minute et
par adresse IP (voir §13). `/api/v1/auth/logout` n'est accessible qu'authentifié, quel que
soit le rôle (client, partenaire ou administrateur).

## 10. Endpoints — Client

| Méthode | Route | Description |
|---|---|---|
| `POST` | `/api/v1/reservations` | Soumettre une demande de réservation |
| `GET` | `/api/v1/reservations` | Historique des réservations du client connecté |
| `GET` | `/api/v1/reservations/{id}` | Détail d'une réservation |
| `POST` | `/api/v1/reservations/{id}/counter-offers/{offerId}/accept` | Accepter une contre-proposition |
| `POST` | `/api/v1/reservations/{id}/counter-offers/{offerId}/reject` | Refuser une contre-proposition |
| `GET` | `/api/v1/me` | Profil de l'utilisateur connecté |
| `PATCH` | `/api/v1/me` | Mise à jour du profil |
| `GET` | `/api/v1/notifications` | Liste des notifications |
| `PATCH` | `/api/v1/notifications/{id}/read` | Marquer une notification comme lue |

`GET`/`PATCH /api/v1/me` sont documentés ici par proximité fonctionnelle, mais sont
communs aux trois rôles authentifiés (client, partenaire, administrateur), contrairement
au reste des endpoints Client ci-dessus.

## 11. Endpoints — Partenaire

| Méthode | Route | Description |
|---|---|---|
| `GET` | `/api/v1/partner/dashboard` | Données de synthèse du tableau de bord |
| `GET` | `/api/v1/partner/residences` | Résidences du partenaire connecté |
| `POST` | `/api/v1/partner/residences` | Créer une résidence |
| `PATCH` | `/api/v1/partner/residences/{id}` | Mettre à jour une résidence |
| `POST` | `/api/v1/partner/residences/{id}/photos` | Ajouter une photo |
| `DELETE` | `/api/v1/partner/residences/{id}/photos/{photoId}` | Supprimer une photo |
| `GET` | `/api/v1/partner/vehicles` | Véhicules du partenaire connecté |
| `POST` | `/api/v1/partner/vehicles` | Créer un véhicule |
| `PATCH` | `/api/v1/partner/vehicles/{id}` | Mettre à jour un véhicule |
| `POST` | `/api/v1/partner/availability-blocks` | Créer un blocage manuel (entretien, maintenance, usage personnel) |
| `DELETE` | `/api/v1/partner/availability-blocks/{id}` | Lever un blocage manuel |
| `GET` | `/api/v1/partner/reservations` | Demandes et réservations reçues |
| `POST` | `/api/v1/partner/reservations/{id}/accept` | Accepter une demande |
| `POST` | `/api/v1/partner/reservations/{id}/reject` | Refuser une demande |
| `POST` | `/api/v1/partner/reservations/{id}/counter-offer` | Soumettre une contre-proposition |

## 12. Endpoints — Administration

| Méthode | Route | Description |
|---|---|---|
| `GET` | `/api/v1/admin/partners/pending` | Partenaires en attente de validation |
| `POST` | `/api/v1/admin/partners/{id}/validate` | Valider un partenaire |
| `POST` | `/api/v1/admin/partners/{id}/reject` | Rejeter un partenaire |
| `GET` | `/api/v1/admin/listings/pending` | Annonces en attente de validation |
| `POST` | `/api/v1/admin/listings/{type}/{id}/validate` | Valider une annonce |
| `POST` | `/api/v1/admin/listings/{type}/{id}/reject` | Rejeter une annonce |
| `GET` | `/api/v1/admin/users` | Liste des utilisateurs |
| `PATCH` | `/api/v1/admin/users/{id}/suspend` | Suspendre un utilisateur |
| `GET` | `/api/v1/admin/statistics` | Statistiques globales de la plateforme |
| `GET` | `/api/v1/admin/settings` | Paramètres de la plateforme |
| `PATCH` | `/api/v1/admin/settings` | Mettre à jour un paramètre |

## 13. Rate limiting

- Les endpoints d'authentification (`/api/v1/auth/*`) sont limités à un nombre restreint de tentatives par minute et par adresse IP (protection anti brute-force).
- Les endpoints de recherche publique sont soumis à une limite plus généreuse mais non nulle pour éviter le scraping massif.
- Toute limite dépassée retourne `429 Too Many Requests` avec un en-tête `Retry-After`.

Le détail des seuils exacts et de leur configuration est dans `docs/engineering/10-security-guidelines.md`.
