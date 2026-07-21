# 11 — Performance Guidelines

## Table des matières

1. [Portée du document](#1-portée-du-document)
2. [Redis — usages](#2-redis--usages)
3. [Stratégie de cache applicatif](#3-stratégie-de-cache-applicatif)
4. [Pagination — impact performance](#4-pagination--impact-performance)
5. [Prévention des requêtes N+1](#5-prévention-des-requêtes-n1)
6. [Index — rappel orienté performance](#6-index--rappel-orienté-performance)
7. [Files d'attente — bonnes pratiques](#7-files-dattente--bonnes-pratiques)
8. [Optimisation des assets front](#8-optimisation-des-assets-front)
9. [Surveillance continue](#9-surveillance-continue)

---

## 1. Portée du document

Ce document couvre les pratiques de performance transverses. Il s'appuie sur l'infrastructure décrite dans [`ARCHITECTURE.md`](../../ARCHITECTURE.md) §10-11 et [`DEPLOYMENT.md`](../../DEPLOYMENT.md).

## 2. Redis — usages

| Usage | Détail |
|---|---|
| Cache applicatif | Résultats de recherche fréquents, configuration de plateforme (`platform_settings`) |
| Files d'attente | Jobs asynchrones (notifications, traitement photo, tâches planifiées) |
| Sessions (optionnel) | Peut être utilisé comme driver de session pour de meilleures performances en environnement multi-instance |
| Limitation de fréquence | Compteurs des limiteurs décrits dans `10-security-guidelines.md` §6 |

## 3. Stratégie de cache applicatif

- Les résultats de recherche publique (`/api/v1/search`) peuvent être mis en cache pour une courte durée (quelques minutes), avec invalidation explicite lors de la publication ou modification d'une annonce.
- La configuration de plateforme (`platform_settings`) est mise en cache indéfiniment jusqu'à invalidation explicite au moment de sa modification par un administrateur (voir [`DATABASE.md`](../../DATABASE.md) §10.2).
- Le calendrier de disponibilité d'un bien n'est **jamais** mis en cache au-delà de la durée d'une requête : la fraîcheur de cette donnée est critique pour éviter les conflits de réservation (voir [`BUSINESS_RULES.md`](../../BUSINESS_RULES.md) §7).

```php
$results = Cache::remember(
    "search:{$criteria->cacheKey()}",
    now()->addMinutes(5),
    fn () => app(ResidenceRepository::class)->rechercher($criteria)
);
```

## 4. Pagination — impact performance

- Toute liste potentiellement volumineuse (annonces, réservations, utilisateurs) est systématiquement paginée, jamais chargée intégralement (voir [`API_GUIDE.md`](../../API_GUIDE.md) §5 et `09-api-guidelines.md` §5).
- La taille de page maximale autorisée (100) est appliquée côté serveur, indépendamment de ce que le client demande.

## 5. Prévention des requêtes N+1

- Tout affichage de liste avec relation (ex : réservations avec bien associé) utilise systématiquement l'eager loading :

```php
Reservation::with(['reservable', 'client'])->paginate();
```

- Un outil de détection des requêtes N+1 (ex : Laravel Debugbar en développement, ou un package dédié) est activé en environnement `local` et `staging` pour détecter les régressions avant mise en production.
- Toute Pull Request introduisant une boucle avec accès à une relation Eloquent non pré-chargée doit être corrigée en revue de code.

## 6. Index — rappel orienté performance

Voir le détail complet des conventions d'indexation dans `08-database-guidelines.md` §6. Rappel des priorités de performance :

1. Colonnes de clé étrangère très jointes (`partner_id`, `client_id`).
2. Colonnes de filtre de recherche publique (`city`, `status`).
3. Contrainte d'exclusion GiST sur les périodes de blocage (`availability_blocks.period`), qui sert à la fois d'intégrité **et** de performance de recherche de disponibilité.

## 7. Files d'attente — bonnes pratiques

- Les jobs à fort volume (envoi de notifications en masse, traitement d'image) sont répartis sur des files dédiées (voir `04-architecture.md` §9) pour éviter qu'un pic sur l'une ne bloque les autres.
- Chaque job définit un `timeout` explicite, pour éviter qu'un job bloqué ne consomme indéfiniment un worker.
- Le nombre de workers par file est dimensionné selon le volume observé, ajusté en `production` sur la base du monitoring (voir [`DEPLOYMENT.md`](../../DEPLOYMENT.md) §8).

## 8. Optimisation des assets front

- Les assets CSS/JS sont compilés et minifiés via Vite en production (`npm run build`), jamais servis en mode développement non compilé.
- Les images uploadées par les partenaires sont automatiquement redimensionnées en plusieurs formats (vignette liste, détail annonce) au moment de l'upload, via un job asynchrone (`media`, voir `04-architecture.md` §9), pour éviter de servir une image surdimensionnée sur un simple aperçu.
- Les polices et icônes utilisées sont limitées à un jeu restreint pour éviter le poids inutile de chargement.

## 9. Surveillance continue

- Les requêtes de base de données lentes (seuil configurable) sont journalisées et remontées en alerte, voir [`DEPLOYMENT.md`](../../DEPLOYMENT.md) §8.
- Un examen périodique des index inutilisés ou manquants est effectué à intervalle régulier à mesure que le volume de données réel augmente en production — la conception initiale n'est pas figée définitivement.
