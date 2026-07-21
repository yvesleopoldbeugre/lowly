# AGENT.md — Engineering Handbook LOWLY

Ce fichier est le point d'entrée de l'**Engineering Handbook** de LOWLY. Il n'est volontairement pas exhaustif : chaque sujet détaillé vit dans son propre fichier, listé ci-dessous. Ce document explique la mission, la vision, la stack, les principes, le workflow, et indique où trouver chaque règle.

## Mission et vision

LOWLY est une marketplace de mise en relation entre clients et partenaires (résidences meublées, véhicules de location), conçue dès le départ comme un produit extensible. Détail : [`01-mission.md`](./01-mission.md).

## Philosophie produit

Pourquoi une marketplace, pourquoi Laravel, pourquoi un monolithe modulaire, quels principes UX et métier guident chaque décision : [`02-product-philosophy.md`](./02-product-philosophy.md).

## Stack technique

| Couche | Technologie |
|---|---|
| Backend | Laravel 12, PHP 8.4+ |
| Frontend | Blade, Tailwind CSS 4, JavaScript ES6+, Alpine.js |
| Base de données | PostgreSQL 17 |
| Cache / files | Redis |
| Conteneurisation | Docker, Docker Compose |
| Serveur web | Nginx |

Aucun frontend séparé (React, Vue, Next.js) n'est autorisé.

## Principes d'ingénierie

SOLID, DRY, KISS, YAGNI, DDD, Clean Code — voir [`03-engineering-principles.md`](./03-engineering-principles.md).

## Workflow de conception

```
Besoin → Analyse métier → Diagrammes UML → Base de données → Architecture
       → API → UX/UI → Développement → Tests → Validation
```

Aucun développement n'est entrepris sans conception préalable. Détail : `../../ENGINEERING.md` (racine du dépôt).

## Index complet du handbook

| Fichier | Sujet |
|---|---|
| [`01-mission.md`](./01-mission.md) | Mission, vision, valeurs, objectifs, positionnement |
| [`02-product-philosophy.md`](./02-product-philosophy.md) | Philosophie produit |
| [`03-engineering-principles.md`](./03-engineering-principles.md) | SOLID, DRY, KISS, YAGNI, DDD, Clean Code |
| [`04-architecture.md`](./04-architecture.md) | Architecture C4, modules, services, events, storage, queues, notifications |
| [`05-laravel-conventions.md`](./05-laravel-conventions.md) | Conventions Laravel (Controllers, Services, Policies, Requests, Jobs, Events) |
| [`06-blade-tailwind-guidelines.md`](./06-blade-tailwind-guidelines.md) | Design system, layouts, composants, responsive, accessibilité |
| [`07-javascript-guidelines.md`](./07-javascript-guidelines.md) | Organisation JS, Alpine.js, calendrier, notifications, charts |
| [`08-database-guidelines.md`](./08-database-guidelines.md) | Conventions PostgreSQL, index, UUID, JSON, migrations, seeders |
| [`09-api-guidelines.md`](./09-api-guidelines.md) | REST, versioning, pagination, authentification, erreurs |
| [`10-security-guidelines.md`](./10-security-guidelines.md) | OWASP, validation, policies, CSRF, permissions, logs, audit |
| [`11-performance-guidelines.md`](./11-performance-guidelines.md) | Redis, cache, pagination, N+1, index, queues, optimisation |
| [`12-testing-guidelines.md`](./12-testing-guidelines.md) | PHPUnit, Pest, Feature Tests, Browser Tests, couverture |
| [`13-git-workflow.md`](./13-git-workflow.md) | Git Flow, branches, commits, tags, releases |
| [`14-code-review.md`](./14-code-review.md) | Checklist complète avant merge |
| [`15-deployment.md`](./15-deployment.md) | Docker, Nginx, CI/CD, SSL, monitoring, backups |
| [`16-documentation.md`](./16-documentation.md) | Comment documenter le projet, ADR, README, API, diagrammes, PHPDoc |
| [`17-checklists.md`](./17-checklists.md) | Checklists avant commit/PR/merge/release/production |
| [`18-adr.md`](./18-adr.md) | Décisions d'architecture (ADR) |
| [`glossary.md`](./glossary.md) | Glossaire métier |

## Rappel

Ce handbook détaille les **conventions techniques quotidiennes**. Pour la vision produit, les règles métier opposables et l'architecture globale, se référer d'abord aux documents racine du dépôt : `PRODUCT.md`, `BUSINESS_RULES.md`, `ARCHITECTURE.md`, `DATABASE.md`, `API_GUIDE.md`.
