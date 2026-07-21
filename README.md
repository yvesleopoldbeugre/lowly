# LOWLY

**Marketplace de mise en relation entre clients et partenaires proposant des résidences meublées et des véhicules de location.**

LOWLY n'est pas une agence de location, ni un PMS (Property Management System), ni un concurrent direct de Booking ou Airbnb. LOWLY est l'intermédiaire technique et de confiance entre un **client** qui cherche un bien ou un véhicule, et un **partenaire** qui le propose. Le cœur du produit repose sur un cycle simple : **Demande → Validation → Confirmation**.

---

## Table des matières

1. [À propos](#à-propos)
2. [Documentation du projet](#documentation-du-projet)
3. [Stack technique](#stack-technique)
4. [Architecture en un coup d'œil](#architecture-en-un-coup-dœil)
5. [Structure du dépôt](#structure-du-dépôt)
6. [Démarrage rapide](#démarrage-rapide)
7. [Commandes utiles](#commandes-utiles)
8. [Environnements](#environnements)
9. [Contribuer](#contribuer)
10. [Support et contacts](#support-et-contacts)

---

## À propos

LOWLY met en relation deux catégories d'utilisateurs :

- des **clients** qui recherchent une résidence meublée ou un véhicule de location pour une période donnée ;
- des **partenaires** qui proposent leurs biens (résidences, véhicules) et gèrent leurs disponibilités, tarifs et réservations.

Une équipe d'**administrateurs** valide les partenaires et les annonces, supervise la plateforme et pilote les statistiques globales.

Le produit est conçu dès sa conception comme une base **extensible** : au-delà des résidences et véhicules du MVP, LOWLY est destiné à accueillir à terme des hôtels, villas, appartements, salles, bureaux, excursions, chauffeurs et autres services à la demande.

Pour la vision produit complète, voir [`PRODUCT.md`](./PRODUCT.md).

## Documentation du projet

La documentation de LOWLY est organisée en deux niveaux :

### Documents de référence (racine du dépôt)

| Fichier | Contenu |
|---|---|
| [`README.md`](./README.md) | Ce document — point d'entrée général |
| [`PRODUCT.md`](./PRODUCT.md) | Vision produit, personas, périmètre MVP, parcours utilisateurs |
| [`BUSINESS_RULES.md`](./BUSINESS_RULES.md) | Règles métier : calcul des journées, cycle de réservation, blocages calendrier |
| [`ARCHITECTURE.md`](./ARCHITECTURE.md) | Architecture applicative, modules, flux, événements |
| [`DATABASE.md`](./DATABASE.md) | Modèle de données PostgreSQL, schémas, conventions |
| [`API_GUIDE.md`](./API_GUIDE.md) | Conventions API REST, authentification, endpoints |
| [`ENGINEERING.md`](./ENGINEERING.md) | Vue d'ensemble des pratiques d'ingénierie |
| [`SECURITY.md`](./SECURITY.md) | Politique de sécurité, OWASP, permissions, audit |
| [`TESTING.md`](./TESTING.md) | Stratégie de tests, couverture, cas critiques |
| [`DEPLOYMENT.md`](./DEPLOYMENT.md) | Infrastructure, CI/CD, procédures de mise en production |
| [`ROADMAP.md`](./ROADMAP.md) | Phases, jalons, extensions futures |

### Engineering Handbook (`docs/engineering/`)

Le handbook détaille les conventions techniques quotidiennes de l'équipe. Point d'entrée : [`docs/engineering/AGENT.md`](./docs/engineering/AGENT.md).

| Fichier | Sujet |
|---|---|
| `01-mission.md` | Mission, vision, valeurs |
| `02-product-philosophy.md` | Philosophie produit |
| `03-engineering-principles.md` | SOLID, DRY, KISS, YAGNI, DDD |
| `04-architecture.md` | Architecture détaillée (C4, modules) |
| `05-laravel-conventions.md` | Conventions Laravel |
| `06-blade-tailwind-guidelines.md` | Design system, Blade, Tailwind |
| `07-javascript-guidelines.md` | JS, Alpine.js |
| `08-database-guidelines.md` | Conventions PostgreSQL |
| `09-api-guidelines.md` | Conventions API |
| `10-security-guidelines.md` | Sécurité applicative |
| `11-performance-guidelines.md` | Performance, cache, Redis |
| `12-testing-guidelines.md` | Tests |
| `13-git-workflow.md` | Git Flow |
| `14-code-review.md` | Revue de code |
| `15-deployment.md` | Déploiement |
| `16-documentation.md` | Documentation, ADR |
| `17-checklists.md` | Checklists |
| `18-adr.md` | Décisions d'architecture |
| `glossary.md` | Glossaire métier |

> Le handbook est produit dans une phase distincte de ce dépôt de documentation.

## Stack technique

LOWLY est une application **Laravel monolithique**, sans frontend JavaScript séparé.

| Couche | Technologie |
|---|---|
| Backend | Laravel 12, PHP 8.4+ |
| Frontend | Blade, Tailwind CSS 4, Alpine.js, JavaScript ES6+ |
| Base de données | PostgreSQL 17 |
| Cache / files | Redis |
| Conteneurisation | Docker, Docker Compose |
| Serveur web | Nginx |

Il n'y a pas de framework JavaScript (React, Vue, Next.js). Le rendu est fait côté serveur (Blade), avec Alpine.js pour l'interactivité légère côté client.

## Architecture en un coup d'œil

LOWLY est un **monolithe modulaire**, organisé par domaines métier plutôt que par couches techniques :

```
Identity        → comptes, authentification, rôles
Partners        → partenaires, validation, profils
Catalogue       → résidences, véhicules, annonces
Availability    → calendriers, disponibilités, blocages
Reservation     → demandes, validation, confirmation, contre-propositions
Communication   → notifications, messagerie
Administration  → back-office, statistiques, paramètres
```

Chaque domaine encapsule ses propres `Controllers`, `Models`, `Services`, `Actions`, `Policies`, `Requests`, `Repositories`, `Resources`, `Events` et `Listeners`. Aucune logique métier ne réside dans les contrôleurs. Détails complets : [`ARCHITECTURE.md`](./ARCHITECTURE.md).

## Structure du dépôt

```
lowly/
├── app/
│   ├── Domains/
│   │   ├── Identity/
│   │   ├── Partners/
│   │   ├── Catalogue/
│   │   ├── Availability/
│   │   ├── Reservation/
│   │   ├── Communication/
│   │   └── Administration/
│   └── Support/
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── factories/
├── resources/
│   ├── views/
│   ├── css/
│   └── js/
├── routes/
├── docs/
│   └── engineering/
├── docker/
├── tests/
├── README.md
├── PRODUCT.md
├── BUSINESS_RULES.md
├── ARCHITECTURE.md
├── DATABASE.md
├── API_GUIDE.md
├── ENGINEERING.md
├── SECURITY.md
├── TESTING.md
├── DEPLOYMENT.md
└── ROADMAP.md
```

## Démarrage rapide

Prérequis : Docker et Docker Compose installés.

```bash
# 1. Cloner le dépôt
git clone git@github.com:lowly/lowly.git
cd lowly

# 2. Copier le fichier d'environnement
cp .env.example .env

# 3. Construire et démarrer les conteneurs
docker compose up -d --build

# 4. Installer les dépendances PHP
docker compose exec app composer install

# 5. Générer la clé d'application
docker compose exec app php artisan key:generate

# 6. Exécuter les migrations et les seeders
docker compose exec app php artisan migrate --seed

# 7. Installer les dépendances front et compiler les assets
docker compose exec app npm install
docker compose exec app npm run dev
```

L'application est alors accessible sur `http://localhost:8000`.

## Commandes utiles

```bash
# Lancer les tests
docker compose exec app php artisan test

# Lancer Pest
docker compose exec app ./vendor/bin/pest

# Vider les caches applicatifs
docker compose exec app php artisan optimize:clear

# Accéder à Tinker
docker compose exec app php artisan tinker

# Suivre les logs de l'application
docker compose logs -f app
```

Détails complets des procédures de déploiement : [`DEPLOYMENT.md`](./DEPLOYMENT.md).

## Environnements

| Environnement | Usage |
|---|---|
| `local` | Développement sur poste, Docker Compose |
| `staging` | Pré-production, validation fonctionnelle |
| `production` | Environnement client final |

## Contribuer

Toute contribution suit le workflow Git décrit dans `docs/engineering/13-git-workflow.md` et la checklist de revue de code décrite dans `docs/engineering/14-code-review.md`. Aucun développement ne doit être entamé sans conception préalable : besoin → analyse métier → diagrammes UML → base de données → architecture → API → UX/UI → développement → tests → validation.

## Support et contacts

Pour toute question relative au produit ou à l'architecture, se référer d'abord à la documentation de ce dépôt. Pour toute question non couverte, contacter l'équipe technique du projet LOWLY.
