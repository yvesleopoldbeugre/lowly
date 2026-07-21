# ENGINEERING.md — Vue d'ensemble des pratiques d'ingénierie

## Table des matières

1. [Objectif de ce document](#1-objectif-de-ce-document)
2. [Philosophie de développement](#2-philosophie-de-développement)
3. [Où trouver quoi](#3-où-trouver-quoi)
4. [Principes non négociables](#4-principes-non-négociables)
5. [Cycle de vie d'une fonctionnalité](#5-cycle-de-vie-dune-fonctionnalité)
6. [Qualité et outillage](#6-qualité-et-outillage)
7. [Définition de « terminé » (Definition of Done)](#7-définition-de-terminé-definition-of-done)

---

## 1. Objectif de ce document

`ENGINEERING.md` est le pont entre les documents de référence produit/métier de la racine du dépôt (`PRODUCT.md`, `BUSINESS_RULES.md`, `ARCHITECTURE.md`, `DATABASE.md`, `API_GUIDE.md`) et l'**Engineering Handbook** détaillé situé dans `docs/engineering/`. Il ne duplique aucun contenu détaillé : il oriente vers le bon document.

## 2. Philosophie de développement

Aucun développement n'est entrepris sans conception préalable. Le workflow standard de toute fonctionnalité, du besoin au code livré, est :

```
   Besoin
     │
     ▼
   Analyse métier
     │
     ▼
   Diagrammes UML
     │
     ▼
   Base de données
     │
     ▼
   Architecture
     │
     ▼
   API
     │
     ▼
   UX / UI
     │
     ▼
   Développement
     │
     ▼
   Tests
     │
     ▼
   Validation
```

Chaque étape produit un artefact vérifiable (schéma, ticket, migration, endpoint documenté, maquette) avant de passer à l'étape suivante. Une étape sautée doit être explicitement justifiée et documentée dans la Pull Request correspondante.

## 3. Où trouver quoi

| Besoin d'information sur... | Document |
|---|---|
| La vision produit et le périmètre MVP | [`PRODUCT.md`](./PRODUCT.md) |
| Les règles métier exactes (journées, cycle de réservation) | [`BUSINESS_RULES.md`](./BUSINESS_RULES.md) |
| Les diagrammes UML (cas d'utilisation, classes, séquences, états) | [`UML.md`](./UML.md) |
| L'organisation en domaines, les flux, les événements | [`ARCHITECTURE.md`](./ARCHITECTURE.md) |
| Le schéma de base de données | [`DATABASE.md`](./DATABASE.md) |
| L'implémentation concrète du schéma (migrations, modèles Eloquent, factories, seeders) | `database/migrations/`, `app/Domains/*/Models/`, `database/factories/`, `database/seeders/DatabaseSeeder.php` |
| Les maquettes UX/UI (wireframes et prototypes HTML/Tailwind) | [`UX_UI.md`](./UX_UI.md), `docs/ux/mockups/` |
| Les conventions d'API | [`API_GUIDE.md`](./API_GUIDE.md) |
| L'implémentation concrète de l'API (routes, contrôleurs squelettes, Resources, Requests) | `routes/api.php`, `app/Domains/*/Controllers/Api/`, `app/Domains/*/Resources/`, `app/Domains/*/Requests/` |
| La sécurité applicative | [`SECURITY.md`](./SECURITY.md) et `docs/engineering/10-security-guidelines.md` |
| La stratégie de tests | [`TESTING.md`](./TESTING.md) et `docs/engineering/12-testing-guidelines.md` |
| Le déploiement et l'infrastructure | [`DEPLOYMENT.md`](./DEPLOYMENT.md) et `docs/engineering/15-deployment.md` |
| L'implémentation Docker (images, compose, config Nginx/PostgreSQL) | `docker/`, `docker-compose.yml`, `docker-compose.override.yml` |
| Les phases et jalons du projet | [`ROADMAP.md`](./ROADMAP.md) |
| Les conventions de code Laravel/Blade/JS au quotidien | `docs/engineering/05-` à `07-*.md` |
| Le workflow Git et la revue de code | `docs/engineering/13-git-workflow.md`, `14-code-review.md` |
| Les décisions d'architecture passées | `docs/engineering/18-adr.md` |
| Le vocabulaire métier | `docs/engineering/glossary.md` |

## 4. Principes non négociables

1. **Aucune logique métier dans les contrôleurs.** Voir [`ARCHITECTURE.md`](./ARCHITECTURE.md) §7.
2. **Les domaines communiquent par événements, jamais par appel direct.** Voir [`ARCHITECTURE.md`](./ARCHITECTURE.md) §9 et §14.
3. **Toute règle métier vérifiable en base de données doit être portée par une contrainte SQL**, en complément de la validation applicative. Voir [`DATABASE.md`](./DATABASE.md) §12.
4. **`BUSINESS_RULES.md` prévaut** sur toute interprétation du code en cas de divergence.
5. **Aucune fonctionnalité n'est livrée sans tests couvrant ses cas métier critiques.** Voir [`TESTING.md`](./TESTING.md).
6. **Le MVP reste le MVP.** Toute extension de périmètre doit être validée au regard de [`PRODUCT.md`](./PRODUCT.md) §9-10 avant d'être développée.

## 5. Cycle de vie d'une fonctionnalité

```
1. Ticket créé avec référence au besoin métier (issue produit)
2. Analyse : vérification de conformité avec BUSINESS_RULES.md / PRODUCT.md
3. Conception technique : impact sur ARCHITECTURE.md et DATABASE.md identifié
4. Migration(s) de base de données écrite(s) et revue
5. Endpoints API définis / documentés (API_GUIDE.md si nouveauté durable)
6. Implémentation (Action/Service/Controller/Blade)
7. Tests (unitaires + feature) couvrant les cas métier et cas limites
8. Revue de code selon la checklist docs/engineering/14-code-review.md
9. Merge selon le workflow docs/engineering/13-git-workflow.md
10. Déploiement selon DEPLOYMENT.md
```

## 6. Qualité et outillage

| Aspect | Outil / pratique |
|---|---|
| Style de code PHP | PSR-12, Laravel Pint |
| Analyse statique | PHPStan / Larastan (niveau cible défini dans le handbook) |
| Tests | Pest (préféré) et PHPUnit |
| Style CSS | Tailwind CSS 4, classes utilitaires uniquement, pas de CSS custom sauvage |
| JS | ES6+, Alpine.js, pas de framework SPA |
| CI | Pipeline exécutant lint, analyse statique et tests à chaque Pull Request |

Détails complets dans `docs/engineering/05-` à `12-*.md`.

## 7. Définition de « terminé » (Definition of Done)

Une fonctionnalité n'est considérée comme terminée que si :

- elle respecte les règles métier de [`BUSINESS_RULES.md`](./BUSINESS_RULES.md) ;
- elle respecte l'organisation en domaines de [`ARCHITECTURE.md`](./ARCHITECTURE.md) ;
- les migrations de base de données sont conformes à [`DATABASE.md`](./DATABASE.md) ;
- les endpoints exposés respectent [`API_GUIDE.md`](./API_GUIDE.md) ;
- les tests métier critiques sont écrits et passent (voir [`TESTING.md`](./TESTING.md)) ;
- la revue de code (`docs/engineering/14-code-review.md`) est validée ;
- la checklist avant merge (`docs/engineering/17-checklists.md`) est cochée.
