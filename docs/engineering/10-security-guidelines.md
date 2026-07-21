# 10 — Security Guidelines

## Table des matières

1. [Portée du document](#1-portée-du-document)
2. [Checklist OWASP appliquée en revue de code](#2-checklist-owasp-appliquée-en-revue-de-code)
3. [Écriture des Form Requests](#3-écriture-des-form-requests)
4. [Écriture des Policies](#4-écriture-des-policies)
5. [Configuration CSRF](#5-configuration-csrf)
6. [Limitation de fréquence (rate limiting)](#6-limitation-de-fréquence-rate-limiting)
7. [Journalisation](#7-journalisation)
8. [Gestion des uploads](#8-gestion-des-uploads)
9. [Secrets et configuration](#9-secrets-et-configuration)
10. [En-têtes de sécurité HTTP](#10-en-têtes-de-sécurité-http)

---

## 1. Portée du document

Ce document traduit les principes de [`SECURITY.md`](../../SECURITY.md) en pratiques d'implémentation Laravel concrètes, vérifiables en revue de code.

## 2. Checklist OWASP appliquée en revue de code

Toute Pull Request touchant à l'authentification, aux permissions, ou à une entrée utilisateur doit répondre positivement à :

- [ ] Toute nouvelle route mutante (`POST`/`PATCH`/`DELETE`) est protégée par une `Policy` explicite.
- [ ] Toute entrée utilisateur passe par une `FormRequest` avec règles de validation complètes.
- [ ] Aucune requête SQL brute non paramétrée n'est introduite.
- [ ] Aucun secret n'est committé (vérifié également par un hook pre-commit / scanner CI).
- [ ] Toute nouvelle dépendance Composer/npm est vérifiée (pas de vulnérabilité connue).

## 3. Écriture des Form Requests

```php
final class CreerReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'reservable_type' => ['required', Rule::in(['residence', 'vehicle'])],
            'reservable_id' => ['required', 'uuid'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after:start_date'],
        ];
    }
}
```

La règle `after:start_date` sur `end_date` traduit directement la contrainte métier de [`BUSINESS_RULES.md`](../../BUSINESS_RULES.md) §10 (pas de réservation sur un seul jour calendaire identique pour l'arrivée et le départ).

## 4. Écriture des Policies

```php
final class ReservationPolicy
{
    public function accepter(User $user, Reservation $reservation): bool
    {
        return $user->id === $reservation->reservable->partner->user_id
            && $reservation->status === StatutReservation::EN_ATTENTE;
    }
}
```

Chaque méthode de `Policy` encode à la fois la vérification de propriété **et** la vérification d'état métier valide (on ne peut pas « accepter » une réservation déjà refusée) — les deux vérifications doivent systématiquement être présentes ensemble.

## 5. Configuration CSRF

- Le middleware `VerifyCsrfToken` reste actif sur toutes les routes web par défaut.
- Toute exception (`except` dans le middleware) doit être justifiée explicitement en commentaire et revue par un pair, jamais ajoutée par commodité de test.

## 6. Limitation de fréquence (rate limiting)

Configuration type des limiteurs nommés (`RouteServiceProvider` ou middleware `throttle:<nom>`) :

| Limiteur | Seuil indicatif | Routes concernées |
|---|---|---|
| `auth` | 5 tentatives / minute / IP | `/auth/login`, `/auth/register` |
| `search` | 60 requêtes / minute / IP | `/api/v1/search`, listes publiques |
| `reservation-write` | 20 requêtes / minute / utilisateur | Création/réponse de réservation |

Les seuils exacts sont ajustables en configuration et doivent être révisés à la lumière du trafic réel observé (voir [`DEPLOYMENT.md`](../../DEPLOYMENT.md) §8, monitoring).

## 7. Journalisation

- Canal de log dédié `security` pour les événements sensibles (échec de connexion répété, tentative d'accès non autorisé détectée par une `Policy`).
- Aucune donnée sensible (mot de passe, jeton, contenu de document justificatif) n'est jamais écrite dans un log, y compris en niveau `debug`.
- Les actions administratives sensibles sont journalisées en base (`admin_actions`, voir [`DATABASE.md`](../../DATABASE.md) §10.1), pas seulement dans les fichiers de log applicatifs, pour garantir leur conservation et leur interrogation.

## 8. Gestion des uploads

- Validation de type MIME réel (`mimes:` combiné à une vérification de contenu, pas seulement l'extension déclarée).
- Taille maximale explicite par type de fichier (photo d'annonce vs document justificatif).
- Renommage systématique à l'upload (UUID généré), le nom de fichier original n'étant conservé que comme métadonnée d'affichage si nécessaire, jamais comme chemin de stockage.
- Les documents justificatifs partenaires (`partners_documents`, voir `04-architecture.md` §8) sont servis exclusivement via un contrôleur qui vérifie la `Policy` d'accès avant de streamer le fichier — jamais via une URL de disque public.

## 9. Secrets et configuration

- Aucun secret (clé API, identifiant de base de données, clé d'application) n'est committé dans le dépôt Git, y compris dans un fichier d'exemple qui contiendrait une vraie valeur.
- `.env.example` contient uniquement des valeurs factices ou vides.
- La rotation des secrets (clé d'application, identifiants de base de données) est documentée dans `15-deployment.md`.

## 10. En-têtes de sécurité HTTP

Configuration Nginx/middleware appliquée en `staging` et `production` :

| En-tête | Valeur indicative |
|---|---|
| `Strict-Transport-Security` | `max-age=31536000; includeSubDomains` |
| `X-Content-Type-Options` | `nosniff` |
| `X-Frame-Options` | `DENY` |
| `Content-Security-Policy` | Politique restrictive limitant les sources de script au domaine applicatif |
| `Referrer-Policy` | `strict-origin-when-cross-origin` |
