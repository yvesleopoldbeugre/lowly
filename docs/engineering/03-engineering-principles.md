# 03 — Principes d'Ingénierie

## Table des matières

1. [Portée du document](#1-portée-du-document)
2. [SOLID](#2-solid)
3. [DRY](#3-dry)
4. [KISS](#4-kiss)
5. [YAGNI](#5-yagni)
6. [DDD (Domain-Driven Design)](#6-ddd-domain-driven-design)
7. [Clean Code](#7-clean-code)
8. [Application à l'architecture LOWLY](#8-application-à-larchitecture-lowly)

---

## 1. Portée du document

Ce document décrit les principes d'ingénierie transverses qui gouvernent toute écriture de code sur LOWLY, indépendamment du domaine métier concerné. Ces principes ne sont pas des idéaux abstraits : chacun a une traduction concrète dans les conventions Laravel détaillées en `05-laravel-conventions.md` et dans la structure en domaines décrite dans [`ARCHITECTURE.md`](../../ARCHITECTURE.md).

## 2. SOLID

| Principe | Application concrète chez LOWLY |
|---|---|
| **S — Single Responsibility** | Une `Action` fait une seule chose (ex : `ConfirmerReservation` ne fait que confirmer ; elle ne notifie pas elle-même, elle émet un événement) |
| **O — Open/Closed** | L'abstraction « Offre réservable » (voir [`ARCHITECTURE.md`](../../ARCHITECTURE.md) §13) permet d'ajouter une catégorie sans modifier le moteur de réservation existant |
| **L — Liskov Substitution** | Toute nouvelle catégorie d'offre implémentant le contrat « Offre réservable » doit pouvoir être utilisée partout où une résidence ou un véhicule l'est aujourd'hui, sans casser le comportement attendu |
| **I — Interface Segregation** | Les contrats (interfaces PHP) exposés par un domaine à un autre restent volontairement étroits — pas d'interface fourre-tout |
| **D — Dependency Inversion** | Les `Services` et `Actions` dépendent d'abstractions (interfaces de `Repositories`), jamais d'implémentations concrètes couplées à un framework externe |

## 3. DRY

« Don't Repeat Yourself » s'applique à la **logique métier**, pas à la structure du code. Deux contrôleurs qui se ressemblent structurellement ne doivent pas être fusionnés artificiellement si leur logique métier diverge. En revanche, une règle de calcul (ex : le calcul du nombre de journées, voir [`BUSINESS_RULES.md`](../../BUSINESS_RULES.md) §3.2) ne doit exister qu'en **un seul endroit** du code, typiquement dans un `Service` partagé entre les domaines `Reservation` et `Availability`.

## 4. KISS

« Keep It Simple, Stupid ». Face à deux solutions techniques répondant également bien à un besoin, LOWLY choisit systématiquement la plus simple à comprendre et à maintenir, même si l'autre paraît plus « élégante » ou plus générique. Un moteur de règles générique pour gérer les deux journées différentes (résidence, véhicule) serait par exemple une sur-ingénierie tant que ces deux règles restent identiques (voir [`BUSINESS_RULES.md`](../../BUSINESS_RULES.md) §3-4).

## 5. YAGNI

« You Aren't Gonna Need It ». Le périmètre du MVP défini dans [`PRODUCT.md`](../../PRODUCT.md) §9 est strict. Aucune fonctionnalité hors périmètre (§10 du même document) ne doit être développée « par anticipation », même si elle semble facile à ajouter en même temps qu'une fonctionnalité en cours. L'extensibilité de l'architecture (voir [`ARCHITECTURE.md`](../../ARCHITECTURE.md) §13) est la réponse structurelle à ce besoin futur — elle ne justifie pas de développer ces fonctionnalités par avance.

## 6. DDD (Domain-Driven Design)

LOWLY applique une version pragmatique du DDD, sans en adopter tout le formalisme (pas de CQRS ni d'event sourcing complet au MVP) :

- le code est organisé par **domaines métier** (`Identity`, `Partners`, `Catalogue`, `Availability`, `Reservation`, `Communication`, `Administration`), pas par couches techniques transverses ;
- chaque domaine possède son propre langage, cohérent avec le glossaire métier (`glossary.md`) ; le mot « réservation » désigne toujours la même chose, dans le code comme dans les documents ;
- les domaines communiquent par événements métier explicites (`ReservationConfirmee`, `PartenaireValide`, etc.), qui sont eux-mêmes une expression du langage métier.

## 7. Clean Code

Règles générales appliquées à tout code LOWLY :

- des noms de classes, méthodes et variables qui expriment l'intention métier (`ConfirmerReservationAction` plutôt que `ReservationHandler`) ;
- des méthodes courtes, avec un seul niveau d'abstraction par méthode ;
- pas de commentaire expliquant *ce que* fait le code (le code doit être suffisamment clair pour cela) mais, si nécessaire, des commentaires expliquant *pourquoi* une décision a été prise, avec renvoi vers l'ADR concerné (`18-adr.md`) le cas échéant ;
- aucune logique métier dupliquée entre un `Controller`, une vue Blade et une Action — la logique vit à un seul endroit (voir principe DRY, §3).

## 8. Application à l'architecture LOWLY

Ces principes ne sont pas déconnectés de la structure du projet : ils justifient directement l'anatomie de domaine décrite dans [`ARCHITECTURE.md`](../../ARCHITECTURE.md) §7 :

```
Controllers   → Single Responsibility : réception HTTP uniquement
Requests      → Interface Segregation : validation isolée de la logique
Policies      → Dependency Inversion : autorisation découplée de l'action
Actions       → Single Responsibility : un cas d'usage = une classe
Services      → DRY : logique métier transverse réutilisable
Repositories  → Dependency Inversion : accès aux données abstrait
Events        → DDD : expression du langage métier entre domaines
```

Toute Pull Request qui viole un de ces principes sans justification documentée doit être bloquée en revue de code (voir `14-code-review.md`).
