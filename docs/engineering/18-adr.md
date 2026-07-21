# 18 — Architecture Decision Records (ADR)

## Table des matières

1. [Format d'un ADR](#1-format-dun-adr)
2. [ADR-001 — Pourquoi Laravel](#2-adr-001--pourquoi-laravel)
3. [ADR-002 — Pourquoi Blade plutôt qu'un frontend séparé](#3-adr-002--pourquoi-blade-plutôt-quun-frontend-séparé)
4. [ADR-003 — Pourquoi PostgreSQL](#4-adr-003--pourquoi-postgresql)
5. [ADR-004 — Pourquoi un monolithe modulaire](#5-adr-004--pourquoi-un-monolithe-modulaire)
6. [ADR-005 — Pourquoi Tailwind CSS](#6-adr-005--pourquoi-tailwind-css)
7. [ADR-006 — Pourquoi Alpine.js plutôt qu'un framework de composants](#7-adr-006--pourquoi-alpinejs-plutôt-quun-framework-de-composants)
8. [ADR-007 — Pourquoi un cycle de validation humaine plutôt qu'une réservation instantanée](#8-adr-007--pourquoi-un-cycle-de-validation-humaine-plutôt-quune-réservation-instantanée)
9. [ADR-008 — Pourquoi des UUID plutôt que des identifiants séquentiels](#9-adr-008--pourquoi-des-uuid-plutôt-que-des-identifiants-séquentiels)

---

## 1. Format d'un ADR

Chaque décision est consignée selon le format suivant :

```
## ADR-XXX — Titre de la décision

Statut : Adopté / Remplacé par ADR-YYY / En discussion
Date : AAAA-MM-JJ

### Contexte
Quel problème ou quelle question a motivé cette décision ?

### Options considérées
Quelles alternatives ont été évaluées ?

### Décision
Quelle option a été retenue ?

### Conséquences
Quels compromis ou contraintes découlent de ce choix ?
```

---

## 2. ADR-001 — Pourquoi Laravel

**Statut** : Adopté

### Contexte

LOWLY nécessite un framework backend capable de couvrir rapidement les besoins d'un MVP marketplace (authentification, autorisation, ORM, files d'attente, notifications) avec une équipe de taille réduite.

### Options considérées

- Laravel (PHP)
- Un framework Node.js (ex : NestJS)
- Un framework Python (ex : Django)

### Décision

Laravel a été retenu pour la maturité de son écosystème natif (Eloquent, Policies, Queues, Notifications), sa productivité pour une équipe réduite, et la cohérence qu'il permet entre rendu serveur (Blade) et logique métier dans une seule base de code.

### Conséquences

L'équipe doit maîtriser PHP 8.4+ et les conventions Laravel. En contrepartie, le time-to-market du MVP est significativement réduit par rapport à un assemblage de briques plus bas niveau.

## 3. ADR-002 — Pourquoi Blade plutôt qu'un frontend séparé

**Statut** : Adopté

### Contexte

Un frontend séparé (React/Vue/Next.js) aurait permis une expérience plus riche côté client, au prix d'une complexité de synchronisation entre deux bases de code (backend Laravel + frontend séparé) et d'une duplication potentielle de la logique de validation.

### Options considérées

- Frontend séparé (SPA) consommant une API Laravel
- Rendu serveur Blade avec interactivité légère (Alpine.js)

### Décision

Rendu serveur Blade + Tailwind + Alpine.js, sans frontend séparé.

### Conséquences

Le time-to-market est plus rapide (une seule base de code, pas de synchronisation API/frontend à maintenir). Le coût est une interactivité client plus limitée que ne le permettrait un framework de composants — jugé acceptable pour le périmètre du MVP (voir [`PRODUCT.md`](../../PRODUCT.md)).

## 4. ADR-003 — Pourquoi PostgreSQL

**Statut** : Adopté

### Contexte

LOWLY a besoin d'un SGBD capable de garantir nativement l'absence de chevauchement de périodes de réservation (contrainte critique du domaine `Availability`), en plus des besoins transactionnels classiques.

### Options considérées

- MySQL/MariaDB
- PostgreSQL

### Décision

PostgreSQL 17, pour son support natif des types `daterange` et des contraintes d'exclusion GiST (`EXCLUDE USING gist`), qui permettent de garantir au niveau base de données l'absence de double réservation — voir [`DATABASE.md`](../../DATABASE.md) §7.2 et `08-database-guidelines.md` §5.

### Conséquences

L'équipe doit maîtriser les spécificités PostgreSQL (types avancés, extensions comme `btree_gist`). En contrepartie, une classe entière de bugs de concurrence sur les réservations est prévenue structurellement plutôt que gérée uniquement en code applicatif.

## 5. ADR-004 — Pourquoi un monolithe modulaire

**Statut** : Adopté

### Contexte

Le choix entre microservices et monolithe conditionne fortement la complexité opérationnelle et la vélocité de développement du MVP.

### Options considérées

- Microservices (un service par domaine métier)
- Monolithe classique (sans séparation de domaines)
- Monolithe modulaire (domaines isolés dans une seule application)

### Décision

Monolithe modulaire, détaillé dans [`ARCHITECTURE.md`](../../ARCHITECTURE.md) §5-7 et `04-architecture.md`.

### Conséquences

L'équipe bénéficie de la simplicité opérationnelle d'un déploiement unique et de la cohérence transactionnelle forte entre domaines (essentielle pour le blocage calendrier atomique). La discipline architecturale (respect strict des frontières de domaine) devient une responsabilité d'équipe permanente, faute de quoi les bénéfices de la modularité disparaissent progressivement.

## 6. ADR-005 — Pourquoi Tailwind CSS

**Statut** : Adopté

### Contexte

Le projet a besoin d'un système de style cohérent, rapide à mettre en œuvre, sans dépendre d'un designer dédié à plein temps dès le MVP.

### Options considérées

- CSS personnalisé complet
- Un framework de composants pré-stylés (ex : Bootstrap)
- Tailwind CSS (utilitaires)

### Décision

Tailwind CSS 4, pour sa rapidité de mise en œuvre directement dans les templates Blade et l'absence de nom de classe sémantique à inventer pour chaque élément.

### Conséquences

Le HTML/Blade est plus verbeux en classes, mais la cohérence visuelle est facilitée par les tokens centralisés (voir `06-blade-tailwind-guidelines.md` §3).

## 7. ADR-006 — Pourquoi Alpine.js plutôt qu'un framework de composants

**Statut** : Adopté

### Contexte

Certaines interactions (calendrier de disponibilité, formulaire de réservation dynamique) nécessitent de la réactivité côté client, sans justifier l'introduction d'un framework JS complet.

### Options considérées

- React/Vue en îlots dans les pages Blade
- Alpine.js

### Décision

Alpine.js exclusivement, conformément à la règle fondamentale d'absence de frontend séparé (voir [`AGENT.md`](../../AGENT.md)).

### Conséquences

La complexité d'interaction possible est plus limitée qu'avec un framework de composants complet, mais suffisante pour le périmètre du MVP (voir `07-javascript-guidelines.md`), sans les coûts de build et de synchronisation d'un framework JS séparé.

## 8. ADR-007 — Pourquoi un cycle de validation humaine plutôt qu'une réservation instantanée

**Statut** : Adopté

### Contexte

Une réservation instantanée (à la manière de Booking) améliorerait potentiellement la conversion côté client, mais retirerait au partenaire le contrôle final sur qui séjourne dans son bien ou utilise son véhicule.

### Options considérées

- Réservation instantanée automatique
- Cycle Demande → Validation → Confirmation avec contrôle partenaire

### Décision

Cycle de validation humaine systématique, positionnement fondateur documenté dans [`PRODUCT.md`](../../PRODUCT.md) §2 et [`BUSINESS_RULES.md`](../../BUSINESS_RULES.md) §2.

### Conséquences

La conversion immédiate peut être légèrement pénalisée par rapport à une réservation instantanée, mais la confiance des partenaires — condition de la croissance de l'offre — est jugée prioritaire pour un marché encore en construction.

## 9. ADR-008 — Pourquoi des UUID plutôt que des identifiants séquentiels

**Statut** : Adopté

### Contexte

Les identifiants de ressources sont potentiellement exposés dans des URLs publiques (détail d'annonce) ou des API futures.

### Options considérées

- Identifiants séquentiels auto-incrémentés
- UUID v4

### Décision

UUID v4 comme clé primaire de toutes les tables, voir [`DATABASE.md`](../../DATABASE.md) §1 et `08-database-guidelines.md` §3.

### Conséquences

Léger surcoût de stockage et d'indexation par rapport à un entier séquentiel, compensé par l'absence de fuite d'information sur le volume d'activité (nombre total d'annonces, de réservations) et une meilleure compatibilité avec une éventuelle distribution future des données.
