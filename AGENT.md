# AGENT.md — LOWLY

Ce fichier est le **point d'entrée** de toute personne (ou tout agent) travaillant sur le dépôt LOWLY. Il ne contient aucune règle détaillée : il oriente vers le bon document. Ne jamais coder une règle métier, une convention technique ou une décision d'architecture directement ici — elles vivent dans les documents listés ci-dessous, qui font foi.

## Présentation

LOWLY est une **marketplace web** qui met en relation des clients avec des partenaires proposant des résidences meublées et des véhicules de location. LOWLY ne possède, n'exploite et ne gère aucun bien : c'est un intermédiaire de confiance entre l'offre et la demande, jamais un exploitant (pas un Booking, pas un Airbnb, pas un PMS, pas une agence). Le cycle central est **Demande → Validation → Confirmation**.

Détails complets : [`PRODUCT.md`](./PRODUCT.md).

## Mission, vision, positionnement

Voir [`docs/engineering/01-mission.md`](./docs/engineering/01-mission.md) et [`docs/engineering/02-product-philosophy.md`](./docs/engineering/02-product-philosophy.md).

## Périmètre du MVP

- Résidences meublées
- Véhicules de location
- Recherche et réservation (cycle demande/validation/confirmation)
- Calendrier de disponibilité et blocages
- Notifications

Détail complet par acteur (Public/Client/Partenaire/Administration) : [`PRODUCT.md`](./PRODUCT.md) §9.

## Stack officielle

| Couche | Technologie |
|---|---|
| Backend | Laravel 13, PHP 8.4+ |
| Frontend | Blade, Tailwind CSS 4, JavaScript ES6+, Alpine.js |
| Base de données | PostgreSQL 17 |
| Cache / files | Redis |
| Stockage | Local (développement) / S3-compatible (production) |
| Serveur web | Nginx |
| Déploiement | Docker, Docker Compose |

## Règle fondamentale

LOWLY est une **application Laravel monolithique modulaire**. Il est interdit de créer un frontend séparé (React, Vue, Next.js). Le frontend est développé exclusivement avec Blade, Tailwind CSS, JavaScript et Alpine.js. Toute nouvelle fonctionnalité doit respecter cette architecture — voir [`ARCHITECTURE.md`](./ARCHITECTURE.md).

## Principes non négociables

1. Aucune logique métier dans les contrôleurs.
2. Les domaines communiquent par événements, jamais par appel direct.
3. Toute règle métier vérifiable en base doit être portée par une contrainte SQL.
4. [`BUSINESS_RULES.md`](./BUSINESS_RULES.md) prévaut en cas de divergence avec le code.
5. Aucune fonctionnalité n'est livrée sans tests couvrant ses cas métier critiques.
6. Aucun développement n'est entrepris sans conception préalable (besoin → analyse métier → UML → base de données → architecture → API → UX/UI → développement → tests → validation).

Détail : [`ENGINEERING.md`](./ENGINEERING.md) et [`docs/engineering/03-engineering-principles.md`](./docs/engineering/03-engineering-principles.md).

## Où trouver chaque règle

### Documents de référence (racine)

| Document | Contenu |
|---|---|
| [`PRODUCT.md`](./PRODUCT.md) | Vision produit, personas, périmètre MVP |
| [`BUSINESS_RULES.md`](./BUSINESS_RULES.md) | Règles métier (journées, cycle de réservation, blocages) |
| [`ARCHITECTURE.md`](./ARCHITECTURE.md) | Architecture, domaines, flux, événements |
| [`DATABASE.md`](./DATABASE.md) | Modèle de données PostgreSQL |
| [`API_GUIDE.md`](./API_GUIDE.md) | Conventions API |
| [`ENGINEERING.md`](./ENGINEERING.md) | Vue d'ensemble des pratiques d'ingénierie |
| [`SECURITY.md`](./SECURITY.md) | Politique de sécurité |
| [`TESTING.md`](./TESTING.md) | Stratégie de tests |
| [`DEPLOYMENT.md`](./DEPLOYMENT.md) | Infrastructure et déploiement |
| [`ROADMAP.md`](./ROADMAP.md) | Phases et jalons |

### Engineering Handbook (`docs/engineering/`)

Point d'entrée détaillé : [`docs/engineering/AGENT.md`](./docs/engineering/AGENT.md), qui indexe les 18 fichiers de convention (`01-mission.md` à `18-adr.md`) et le glossaire métier.

## Documentation

Voir le dossier `/docs/engineering` pour les conventions détaillées, et les fichiers listés ci-dessus à la racine pour la référence produit/métier/technique.
