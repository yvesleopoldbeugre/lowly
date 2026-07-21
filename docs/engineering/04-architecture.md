# 04 — Architecture (détail technique)

## Table des matières

1. [Portée du document](#1-portée-du-document)
2. [Rappel du modèle C4](#2-rappel-du-modèle-c4)
3. [Arborescence applicative complète](#3-arborescence-applicative-complète)
4. [Cycle de vie d'une requête HTTP](#4-cycle-de-vie-dune-requête-http)
5. [Contrats entre domaines (interfaces)](#5-contrats-entre-domaines-interfaces)
6. [Services applicatifs transverses](#6-services-applicatifs-transverses)
7. [Détail des Events et Listeners](#7-détail-des-events-et-listeners)
8. [Détail du stockage](#8-détail-du-stockage)
9. [Détail des files d'attente](#9-détail-des-files-dattente)
10. [Détail des notifications](#10-détail-des-notifications)
11. [Service Providers](#11-service-providers)
12. [Configuration par environnement](#12-configuration-par-environnement)

---

## 1. Portée du document

Ce document complète [`ARCHITECTURE.md`](../../ARCHITECTURE.md) (racine du dépôt) avec le niveau de détail nécessaire à l'implémentation quotidienne : arborescence exacte des dossiers, cycle de vie d'une requête, contrats d'interface entre domaines, et configuration technique des Service Providers Laravel. Toute décision structurelle de plus haut niveau (pourquoi ces domaines, pourquoi ces dépendances) reste dans le document racine.

## 2. Rappel du modèle C4

Les diagrammes de contexte, conteneurs et composants sont définis dans [`ARCHITECTURE.md`](../../ARCHITECTURE.md) §2-4. Ce document ne les reproduit pas ; il en détaille l'implémentation Laravel.

## 3. Arborescence applicative complète

```
app/
├── Domains/
│   ├── Identity/
│   │   ├── Controllers/
│   │   ├── Models/
│   │   ├── Services/
│   │   ├── Actions/
│   │   ├── Policies/
│   │   ├── Requests/
│   │   ├── Repositories/
│   │   ├── Resources/
│   │   ├── Events/
│   │   └── Listeners/
│   ├── Partners/            (même structure)
│   ├── Catalogue/           (même structure)
│   ├── Availability/        (même structure)
│   ├── Reservation/         (même structure)
│   ├── Communication/       (même structure)
│   └── Administration/      (même structure)
├── Support/
│   ├── Contracts/           (interfaces partagées entre domaines)
│   ├── Concerns/            (traits transverses)
│   └── ValueObjects/        (ex : PeriodeReservation, MontantMonetaire)
└── Providers/
```

Chaque sous-dossier d'un domaine ne contient **que** des classes relatives à ce domaine. Une classe qui semble nécessaire à deux domaines à la fois doit être remontée dans `app/Support/` (avec justification en revue de code) plutôt que dupliquée ou placée arbitrairement dans l'un des deux domaines.

## 4. Cycle de vie d'une requête HTTP

```
1. Route (routes/web.php ou routes/api.php)
      │
2. Middleware (auth, throttle, vérification de rôle)
      │
3. Controller::methode()
      │
4. FormRequest (validation + autorisation de premier niveau via rules())
      │
5. Policy::methode() (autorisation métier fine)
      │
6. Action ou Service (logique métier)
      │
7. Repository (accès aux données si nécessaire)
      │
8. Model Eloquent (persistance)
      │
9. Event (émis si changement d'état significatif)
      │
10. Resource ou vue Blade (formatage de la réponse)
```

Un contrôleur ne doit jamais dépasser l'orchestration des étapes 3, 4 (délégué à Laravel), 5 et 6. Toute logique conditionnelle métier trouvée dans un contrôleur en revue de code doit être déplacée.

## 5. Contrats entre domaines (interfaces)

Pour préserver le découplage décrit dans [`ARCHITECTURE.md`](../../ARCHITECTURE.md) §14, tout accès d'un domaine à une capacité d'un autre domaine passe par une **interface** définie dans `app/Support/Contracts/`, jamais par un import direct d'une classe interne d'un autre domaine.

Exemple — le domaine `Reservation` a besoin de savoir si un bien est disponible, capacité portée par `Availability` :

```php
namespace App\Support\Contracts;

interface DisponibiliteVerifiable
{
    public function estDisponible(string $blockableType, string $blockableId, Periode $periode): bool;
}
```

Le domaine `Availability` fournit l'implémentation concrète ; le domaine `Reservation` ne dépend que de l'interface, injectée via le conteneur de services Laravel.

## 6. Services applicatifs transverses

| Service | Domaine porteur | Rôle |
|---|---|---|
| `CalculateurJournees` | Reservation (partagé) | Calcule le nombre de journées facturées selon la règle 12h-12h ([`BUSINESS_RULES.md`](../../BUSINESS_RULES.md) §3.2) |
| `VerificateurDisponibilite` | Availability | Vérifie l'absence de chevauchement avant toute création de blocage ou de réservation |
| `NotificateurEvenementMetier` | Communication | Point d'entrée unique pour la création de notifications, quel que soit le canal (in-app, email) |
| `ValidateurPartenaire` | Administration | Encapsule les règles de validation d'un partenaire ou d'une annonce |

Ces services sont **sans état** (stateless) et injectables ; ils ne doivent jamais porter de logique de présentation.

## 7. Détail des Events et Listeners

Tableau complet des abonnements (complète [`ARCHITECTURE.md`](../../ARCHITECTURE.md) §9) :

| Event | Payload principal | Listener(s) | Synchrone / Asynchrone |
|---|---|---|---|
| `DemandeReservationCreee` | `reservation_id` | `NotifierPartenaireNouvelleDemande` | Asynchrone (queue) |
| `ReservationConfirmee` | `reservation_id` | `BloquerCalendrier`, `NotifierClientConfirmation`, `EnregistrerHistorique` | `BloquerCalendrier` synchrone (garantie transactionnelle), le reste asynchrone |
| `ReservationRefusee` | `reservation_id` | `NotifierClientRefus` | Asynchrone |
| `ContrePropositionSoumise` | `counter_offer_id` | `NotifierClientContreProposition` | Asynchrone |
| `ContrePropositionExpiree` | `counter_offer_id` | `NotifierExpirationContreProposition` | Asynchrone |
| `PartenaireValide` | `partner_id` | `ActiverAccesTableauDeBord`, `NotifierPartenaireValidation` | Synchrone pour l'activation, asynchrone pour la notification |
| `AnnonceValidee` | `listing_type`, `listing_id` | `PublierAnnonce`, `NotifierPartenaireValidationAnnonce` | Synchrone pour la publication, asynchrone pour la notification |
| `CalendrierBloqueManuel` | `availability_block_id` | `NotifierBlocageManuel` (le cas échéant) | Asynchrone |

**Règle** : tout listener qui modifie un état métier critique (blocage calendrier, publication d'annonce) doit être exécuté de manière **synchrone** dans la même transaction que l'événement déclencheur. Seuls les listeners de notification pure sont mis en file d'attente.

## 8. Détail du stockage

| Disque Laravel | Usage | Visibilité |
|---|---|---|
| `public` | Photos d'annonces (résidences, véhicules) | Publique (servi via CDN/Nginx) |
| `partners_documents` | Documents justificatifs partenaires | Privée, accès via contrôleur avec vérification de policy |
| `local` | Fichiers temporaires (traitement d'image en cours) | Privée, jamais exposée |

Le disque `partners_documents` ne doit jamais être configuré avec une visibilité publique, y compris par erreur de configuration d'environnement — un test automatisé doit vérifier cette configuration (voir [`SECURITY.md`](../../SECURITY.md) §10).

## 9. Détail des files d'attente

| File (queue) | Contenu | Priorité |
|---|---|---|
| `notifications` | Envoi des notifications email/in-app | Normale |
| `media` | Redimensionnement et optimisation des photos uploadées | Normale |
| `maintenance` | Jobs planifiés (expiration des contre-propositions, calcul des statistiques) | Basse |

Chaque job est nommé de façon explicite (`EnvoyerNotificationReservationConfirmee`, jamais `Job1`), et implémente `ShouldQueue` avec un nombre de tentatives (`tries`) et un backoff explicites.

## 10. Détail des notifications

Chaque notification Laravel (`App\Domains\Communication\Notifications\*`) définit :

- ses canaux (`via()`) — `database` (in-app) systématiquement, `mail` si le type de notification le requiert ;
- son contenu formaté indépendamment du canal, réutilisant les données de l'événement source ;
- une entrée correspondante dans la table `notifications` (voir [`DATABASE.md`](../../DATABASE.md) §9.1).

## 11. Service Providers

| Provider | Rôle |
|---|---|
| `DomainServiceProvider` (un par domaine, ou un global avec bindings groupés) | Lie chaque interface de `Support/Contracts` à son implémentation concrète du domaine |
| `EventServiceProvider` | Déclare l'ensemble des abonnements Event → Listener listés en §7 |
| `AuthServiceProvider` | Déclare l'ensemble des `Policies` par domaine |

## 12. Configuration par environnement

Le comportement architectural ne diverge jamais entre `local`, `staging` et `production` — seules les valeurs de configuration (identifiants de connexion, disque de stockage, driver de queue) changent, jamais la logique elle-même. Toute divergence de comportement entre environnements est considérée comme un défaut à corriger.
