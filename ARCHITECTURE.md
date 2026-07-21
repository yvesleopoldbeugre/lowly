# ARCHITECTURE.md — Architecture LOWLY

## Table des matières

1. [Vue d'ensemble](#1-vue-densemble)
2. [Modèle C4 — Contexte](#2-modèle-c4--contexte)
3. [Modèle C4 — Conteneurs](#3-modèle-c4--conteneurs)
4. [Modèle C4 — Composants](#4-modèle-c4--composants)
5. [Le monolithe modulaire](#5-le-monolithe-modulaire)
6. [Les domaines métier](#6-les-domaines-métier)
7. [Anatomie d'un domaine](#7-anatomie-dun-domaine)
8. [Flux applicatifs clés](#8-flux-applicatifs-clés)
9. [Événements et écoute (Events/Listeners)](#9-événements-et-écoute-eventslisteners)
10. [Files d'attente (Queues)](#10-files-dattente-queues)
11. [Stockage (Storage)](#11-stockage-storage)
12. [Notifications](#12-notifications)
13. [Extensibilité du catalogue](#13-extensibilité-du-catalogue)
14. [Règles de dépendances entre domaines](#14-règles-de-dépendances-entre-domaines)

---

## 1. Vue d'ensemble

LOWLY est un **monolithe modulaire Laravel**. Il n'y a qu'un seul déploiement applicatif (pas de microservices), mais le code est structuré en **domaines métier isolés**, chacun responsable d'une capacité fonctionnelle précise. Ce choix permet de conserver la simplicité opérationnelle d'un monolithe tout en préservant des frontières claires qui faciliteraient une éventuelle extraction future en services séparés si le besoin apparaissait.

## 2. Modèle C4 — Contexte

```
                         ┌───────────────────────┐
                         │       Visiteur          │
                         └───────────┬─────────────┘
                                     │
                         ┌───────────▼─────────────┐
              ┌──────────┤        LOWLY              ├──────────┐
              │          │  (Plateforme Marketplace)  │          │
              │          └───────────┬─────────────┘          │
     ┌────────▼────────┐   ┌─────────▼─────────┐   ┌──────────▼─────────┐
     │      Client       │   │     Partenaire      │   │   Administrateur    │
     └───────────────────┘   └─────────────────────┘   └────────────────────┘

     Systèmes externes (post-MVP) : passerelle de paiement, service d'emailing,
     service SMS, stockage objet externe (S3-compatible)
```

LOWLY est le système central. Les acteurs externes sont le Visiteur, le Client, le Partenaire et l'Administrateur — tous des utilisateurs humains interagissant via le navigateur. Aucun système tiers n'est requis au MVP ; l'architecture prévoit néanmoins des points d'intégration futurs (paiement, emailing transactionnel, SMS).

## 3. Modèle C4 — Conteneurs

```
┌───────────────────────────────────────────────────────────────────┐
│                        Serveur d'application                       │
│  ┌─────────────────────────────────────────────────────────────┐  │
│  │                     Application Laravel                       │  │
│  │  ┌───────────────┐  ┌────────────────┐  ┌──────────────────┐ │  │
│  │  │  Rendu Blade    │  │  API interne     │  │  Jobs / Queues     │ │  │
│  │  │  + Tailwind CSS │  │  (AJAX Alpine)   │  │  (traitements async)│ │  │
│  │  └───────────────┘  └────────────────┘  └──────────────────┘ │  │
│  └─────────────────────────────────────────────────────────────┘  │
└───────────────────────────┬───────────────────────────────────────┘
                            │
        ┌───────────────────┼───────────────────┐
        │                   │                   │
┌───────▼────────┐  ┌───────▼────────┐  ┌───────▼────────┐
│  PostgreSQL 17   │  │     Redis        │  │   Stockage        │
│  (données)       │  │ (cache/queues)   │  │   (photos, docs)  │
└─────────────────┘  └─────────────────┘  └───────────────────┘
                            │
                    ┌───────▼────────┐
                    │      Nginx        │
                    │ (reverse proxy)   │
                    └───────────────────┘
```

L'application Laravel unique sert à la fois le rendu HTML (Blade + Tailwind CSS 4) et les points d'entrée d'API internes consommés en Ajax par Alpine.js. PostgreSQL est le magasin de données transactionnel, Redis assure le cache applicatif et le pilotage des files d'attente, et Nginx sert de reverse proxy devant l'application.

## 4. Modèle C4 — Composants

Exemple détaillé pour le domaine `Reservation` :

```
┌───────────────────────────────────────────────────────────────────┐
│                          Domaine Reservation                        │
│                                                                     │
│  ┌────────────────┐   ┌───────────────────┐   ┌──────────────────┐ │
│  │  Controllers     │──►│    Requests          │   │   Policies         │ │
│  │  (HTTP)          │   │ (validation entrée)  │   │ (autorisation)     │ │
│  └───────┬────────┘   └───────────────────┘   └──────────────────┘ │
│          │                                                          │
│          ▼                                                          │
│  ┌────────────────┐   ┌───────────────────┐                        │
│  │    Actions        │──►│    Services          │                        │
│  │ (cas d'usage      │   │ (logique métier      │                        │
│  │  unitaires)       │   │  transverse)         │                        │
│  └───────┬────────┘   └─────────┬─────────┘                        │
│          │                       │                                   │
│          ▼                       ▼                                   │
│  ┌────────────────┐   ┌───────────────────┐   ┌──────────────────┐ │
│  │  Repositories     │──►│      Models          │──►│    Events          │ │
│  │ (accès données)   │   │ (Eloquent)           │   │ (ex: RéservationConfirmée) │
│  └────────────────┘   └───────────────────┘   └────────┬─────────┘ │
│                                                          │           │
│                                                          ▼           │
│                                                ┌──────────────────┐ │
│                                                │    Listeners        │ │
│                                                │ (réactions : notif,  │ │
│                                                │  blocage calendrier) │ │
│                                                └──────────────────┘ │
└───────────────────────────────────────────────────────────────────┘
```

## 5. Le monolithe modulaire

Le choix d'un monolithe modulaire (plutôt que des microservices) est motivé par :

- une équipe de taille réduite au démarrage, pour laquelle la complexité opérationnelle des microservices (déploiements multiples, orchestration, observabilité distribuée) serait disproportionnée ;
- un besoin de cohérence transactionnelle forte entre les domaines `Reservation` et `Availability` (le blocage calendrier doit être atomique avec la confirmation de réservation) ;
- la volonté de garder une vélocité de développement élevée pendant la phase de croissance du MVP.

Le détail des motivations et alternatives envisagées est consigné dans `docs/engineering/18-adr.md` (ADR — Architecture Decision Records).

## 6. Les domaines métier

```
┌─────────────┐   ┌─────────────┐   ┌─────────────┐   ┌─────────────┐
│   Identity    │   │  Partners     │   │  Catalogue    │   │ Availability  │
└─────────────┘   └─────────────┘   └─────────────┘   └─────────────┘
┌─────────────┐   ┌───────────────┐   ┌──────────────────┐
│ Reservation   │   │ Communication   │   │  Administration    │
└─────────────┘   └───────────────┘   └──────────────────┘
```

| Domaine | Responsabilité |
|---|---|
| **Identity** | Comptes utilisateurs, authentification, rôles, sessions |
| **Partners** | Profils partenaires, validation, informations légales |
| **Catalogue** | Résidences, véhicules, annonces, photos, description |
| **Availability** | Calendriers de disponibilité, blocages automatiques et manuels |
| **Reservation** | Cycle de demande, validation, contre-proposition, confirmation |
| **Communication** | Notifications, messagerie liée aux réservations |
| **Administration** | Back-office, validation, gestion utilisateurs, statistiques, paramètres |

## 7. Anatomie d'un domaine

Chaque domaine, situé sous `app/Domains/<NomDuDomaine>/`, respecte la structure suivante :

```
app/Domains/Reservation/
├── Controllers/
├── Models/
├── Services/
├── Actions/
├── Policies/
├── Requests/
├── Repositories/
├── Resources/
├── Events/
└── Listeners/
```

Responsabilités de chaque sous-dossier :

| Sous-dossier | Rôle |
|---|---|
| `Controllers/` | Point d'entrée HTTP ; délègue immédiatement à une `Action` ou un `Service` |
| `Models/` | Modèles Eloquent, représentation des entités du domaine |
| `Services/` | Logique métier transverse, réutilisable entre plusieurs actions |
| `Actions/` | Cas d'usage unitaires (ex : `ConfirmerReservation`, `RefuserAvecContrePropositionAction`) |
| `Policies/` | Règles d'autorisation liées au domaine |
| `Requests/` | Validation des données entrantes (Form Requests Laravel) |
| `Repositories/` | Abstraction de l'accès aux données pour les requêtes complexes |
| `Resources/` | Transformation des modèles pour l'affichage ou les réponses API |
| `Events/` | Événements métier émis par le domaine |
| `Listeners/` | Réactions à des événements, y compris ceux d'autres domaines |

**Règle absolue** : aucune logique métier ne doit résider dans un `Controller`. Un contrôleur reçoit la requête, la valide via une `Request`, vérifie l'autorisation via une `Policy`, délègue à une `Action` ou un `Service`, puis retourne une réponse formatée via une `Resource` ou une vue Blade.

## 8. Flux applicatifs clés

### 8.1 Flux — Demande de réservation

```
Client → Controller (Reservation) → Request (validation dates/bien)
       → Policy (le bien est-il réservable ?)
       → Action CreerDemandeReservation
           → Repository Availability (vérifie disponibilité)
           → Model Reservation (créée à l'état EN_ATTENTE)
           → Event DemandeReservationCreee
               → Listener NotifierPartenaireNouvelleDemande (Communication)
```

### 8.2 Flux — Confirmation de réservation

```
Partenaire → Controller (Reservation) → Action ConfirmerReservation
    → Model Reservation (état → CONFIRMÉE)
    → Event ReservationConfirmee
        → Listener BloquerCalendrier (Availability)
        → Listener NotifierClientConfirmation (Communication)
        → Listener EnregistrerHistorique (Reservation)
```

### 8.3 Flux — Validation d'un partenaire par l'administration

```
Administrateur → Controller (Administration) → Action ValiderPartenaire
    → Model Partner (statut → VALIDÉ)
    → Event PartenaireValide
        → Listener ActiverAccesTableauDeBord (Partners)
        → Listener NotifierPartenaireValidation (Communication)
```

## 9. Événements et écoute (Events/Listeners)

Les domaines communiquent entre eux **exclusivement via des événements**, jamais par appel direct de service d'un domaine à un autre. Ce principe garantit le découplage et la testabilité.

Événements métier principaux du MVP :

| Événement | Émis par | Écouté par |
|---|---|---|
| `DemandeReservationCreee` | Reservation | Communication |
| `ReservationConfirmee` | Reservation | Availability, Communication |
| `ReservationRefusee` | Reservation | Communication |
| `ContrePropositionSoumise` | Reservation | Communication |
| `ContrePropositionExpiree` | Reservation | Communication |
| `PartenaireValide` | Administration | Partners, Communication |
| `AnnonceValidee` | Administration | Catalogue, Communication |
| `CalendrierBloqueManuel` | Availability | Communication |

## 10. Files d'attente (Queues)

Redis pilote les files d'attente Laravel pour tout traitement asynchrone n'ayant pas besoin d'être exécuté de manière synchrone dans la requête HTTP :

- envoi des notifications (email, in-app) ;
- traitement et redimensionnement des photos uploadées ;
- expiration automatique des contre-propositions au-delà du délai configuré (via jobs planifiés) ;
- calcul périodique des statistiques d'administration.

Les jobs doivent être idempotents et documentés dans `docs/engineering/07-javascript-guidelines.md` (pour la partie front réactive) et `docs/engineering/11-performance-guidelines.md` (pour les bonnes pratiques de queues).

## 11. Stockage (Storage)

| Type de contenu | Emplacement |
|---|---|
| Photos des annonces (résidences, véhicules) | Disque de stockage configuré (local en développement, compatible S3 en production) |
| Documents justificatifs partenaires | Disque de stockage sécurisé, accès restreint |
| Assets compilés (CSS/JS) | Servis via Nginx depuis `public/` |

## 12. Notifications

Le domaine `Communication` centralise l'émission des notifications, qu'elles soient in-app, email, ou (post-MVP) SMS. Chaque notification est déclenchée par un événement métier et suit un template dédié. La liste exhaustive des notifications du MVP :

- nouvelle demande de réservation (au partenaire) ;
- réservation confirmée (au client) ;
- réservation refusée (au client) ;
- contre-proposition reçue (au client) ;
- contre-proposition expirée (au client et au partenaire) ;
- compte partenaire validé (au partenaire) ;
- annonce validée ou rejetée (au partenaire).

## 13. Extensibilité du catalogue

Le domaine `Catalogue` est conçu pour accueillir de nouvelles catégories d'offres sans modification des domaines `Availability`, `Reservation`, `Communication` et `Administration`. Le principe repose sur une abstraction commune `Offre réservable`, dont `Résidence` et `Véhicule` sont deux implémentations actuelles.

```
              ┌────────────────────┐
              │   Offre réservable    │  (interface / contrat commun)
              └─────────┬──────────┘
        ┌────────────────┼────────────────┬──────────────┐
        ▼                ▼                ▼              ▼
   Résidence         Véhicule         Hôtel (futur)   Excursion (futur)
```

Toute nouvelle catégorie doit implémenter le même contrat (disponibilité, tarification journalière, cycle de réservation) pour bénéficier automatiquement de l'ensemble du moteur de réservation existant.

## 14. Règles de dépendances entre domaines

```
Identity  ◄──────────────────────────────┐
   ▲                                      │
   │                                      │
Partners ──► Catalogue ──► Availability ──► Reservation ──► Communication
                                              │
                                              ▼
                                       Administration
```

Règles :

- `Identity` ne dépend d'aucun autre domaine.
- `Partners` dépend de `Identity` (un partenaire est un utilisateur).
- `Catalogue` dépend de `Partners` (une annonce appartient à un partenaire).
- `Availability` dépend de `Catalogue` (un calendrier est attaché à un bien).
- `Reservation` dépend de `Availability`, `Catalogue` et `Identity`.
- `Communication` ne dépend que des événements émis par les autres domaines (jamais de leurs modèles directement).
- `Administration` peut lire tous les domaines mais aucun domaine ne dépend d'`Administration`.

Toute dépendance à contre-sens de ce schéma doit être considérée comme une violation architecturale et faire l'objet d'une revue immédiate (voir `docs/engineering/14-code-review.md`).
