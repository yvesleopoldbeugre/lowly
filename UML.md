# UML.md — Diagrammes UML LOWLY

## Table des matières

1. [Objectif de ce document](#1-objectif-de-ce-document)
2. [Convention de notation](#2-convention-de-notation)
3. [Diagramme de cas d'utilisation](#3-diagramme-de-cas-dutilisation)
4. [Diagrammes de classes](#4-diagrammes-de-classes)
   - 4.1 [Vue d'ensemble inter-domaines](#41-vue-densemble-inter-domaines)
   - 4.2 [Domaine Identity](#42-domaine-identity)
   - 4.3 [Domaine Partners](#43-domaine-partners)
   - 4.4 [Domaine Catalogue](#44-domaine-catalogue)
   - 4.5 [Domaine Availability](#45-domaine-availability)
   - 4.6 [Domaine Reservation](#46-domaine-reservation)
   - 4.7 [Domaine Communication](#47-domaine-communication)
   - 4.8 [Domaine Administration](#48-domaine-administration)
5. [Diagrammes de séquence](#5-diagrammes-de-séquence)
   - 5.1 [Demande de réservation](#51-demande-de-réservation)
   - 5.2 [Acceptation directe d'une demande](#52-acceptation-directe-dune-demande)
   - 5.3 [Refus avec contre-proposition — acceptation](#53-refus-avec-contre-proposition--acceptation)
   - 5.4 [Refus avec contre-proposition — expiration](#54-refus-avec-contre-proposition--expiration)
   - 5.5 [Refus simple](#55-refus-simple)
   - 5.6 [Blocage manuel de calendrier (véhicule)](#56-blocage-manuel-de-calendrier-véhicule)
   - 5.7 [Validation d'un partenaire par l'administration](#57-validation-dun-partenaire-par-ladministration)
   - 5.8 [Validation d'une annonce par l'administration](#58-validation-dune-annonce-par-ladministration)
6. [Diagramme d'états — Réservation](#6-diagramme-détats--réservation)
7. [Diagramme d'états — Contre-proposition](#7-diagramme-détats--contre-proposition)
8. [Diagramme d'états — Partenaire](#8-diagramme-détats--partenaire)
9. [Diagramme d'états — Annonce (Résidence / Véhicule)](#9-diagramme-détats--annonce-résidence--véhicule)
10. [Traçabilité avec la documentation existante](#10-traçabilité-avec-la-documentation-existante)

---

## 1. Objectif de ce document

`UML.md` matérialise l'étape **Diagrammes UML** du cycle de conception défini dans [`ENGINEERING.md`](./ENGINEERING.md) §2 (Besoin → Analyse métier → **Diagrammes UML** → Base de données → Architecture → API → UX/UI → Développement → Tests → Validation).

Il traduit en notation UML standard ce qui est déjà décrit en prose et en schémas ASCII dans [`PRODUCT.md`](./PRODUCT.md) (acteurs, parcours), [`BUSINESS_RULES.md`](./BUSINESS_RULES.md) (cycle de réservation, états), [`ARCHITECTURE.md`](./ARCHITECTURE.md) (domaines, flux, événements) et [`DATABASE.md`](./DATABASE.md) (entités, colonnes, relations). Aucune règle nouvelle n'est introduite ici : ce document est une **vue dérivée**, pas une source de vérité. En cas de divergence, les documents cités prévalent.

Ce document sert de base à la prochaine étape de conception : les migrations de base de données concrètes et les maquettes UX/UI.

## 2. Convention de notation

Les diagrammes sont écrits en syntaxe [Mermaid](https://mermaid.js.org/), rendue nativement par GitHub, GitLab et la plupart des éditeurs (VS Code avec extension). Cette approche « UML as code » permet de versionner les diagrammes avec le code et de les faire évoluer par Pull Request comme n'importe quel artefact de conception.

- **Cas d'utilisation** : représentés en `flowchart` (Mermaid ne propose pas de type natif `usecaseDiagram`), acteurs en rectangles, cas d'usage en formes arrondies.
- **Classes** : `classDiagram`, un diagramme par domaine métier (cohérent avec le découpage de [`ARCHITECTURE.md`](./ARCHITECTURE.md) §6) plus une vue d'ensemble inter-domaines.
- **Séquence** : `sequenceDiagram`, alignés sur l'anatomie de domaine décrite dans [`ARCHITECTURE.md`](./ARCHITECTURE.md) §7 (Controller → Request → Policy → Action → Repository/Service → Model → Event → Listener).
- **États** : `stateDiagram-v2`, alignés sur les machines à états décrites dans [`BUSINESS_RULES.md`](./BUSINESS_RULES.md).

Les noms de classes et d'attributs suivent la casse Laravel/Eloquent habituelle (`PascalCase` pour les classes, `camelCase` pour les attributs applicatifs), tandis que les schémas de séquence utilisent les noms métier en français utilisés dans `BUSINESS_RULES.md` et `ARCHITECTURE.md` pour rester immédiatement lisibles par toute l'équipe.

## 3. Diagramme de cas d'utilisation

Cas d'usage du MVP par acteur, tels que définis dans [`PRODUCT.md`](./PRODUCT.md) §9. Le Client et le Partenaire héritent des capacités du Visiteur (un compte est d'abord un visiteur qui s'authentifie).

```mermaid
flowchart LR
    Visiteur((Visiteur))
    Client((Client))
    Partenaire((Partenaire))
    Admin((Administrateur))

    Visiteur --- UC1(Consulter les annonces)
    Visiteur --- UC2(Rechercher)
    Visiteur --- UC3(Voir le détail d'une annonce)
    Visiteur --- UC4(Créer un compte)
    Visiteur --- UC5(Se connecter)

    Client --- UC6(Soumettre une demande de réservation)
    Client --- UC7(Suivre le statut d'une demande)
    Client --- UC8(Accepter / refuser une contre-proposition)
    Client --- UC9(Consulter l'historique de réservations)
    Client --- UC10(Gérer son profil)
    Client --- UC11(Recevoir des notifications)

    Partenaire --- UC12(Gérer le tableau de bord)
    Partenaire --- UC13(Créer / éditer une résidence)
    Partenaire --- UC14(Créer / éditer un véhicule)
    Partenaire --- UC15(Gérer les disponibilités et blocages manuels)
    Partenaire --- UC16(Accepter une demande)
    Partenaire --- UC17(Refuser une demande)
    Partenaire --- UC18(Proposer une contre-proposition)
    Partenaire --- UC19(Gérer les tarifs)
    Partenaire --- UC20(Gérer les photos)

    Admin --- UC21(Valider / rejeter un partenaire)
    Admin --- UC22(Valider / rejeter une annonce)
    Admin --- UC23(Gérer les utilisateurs)
    Admin --- UC24(Consulter les statistiques)
    Admin --- UC25(Configurer les paramètres plateforme)

    Client -.hérite de.-> Visiteur
    Partenaire -.hérite de.-> Visiteur
```

**Lecture** : UC6 (« Soumettre une demande ») et UC16-UC18 (traitement partenaire) sont les cas d'usage centraux du MVP — ils matérialisent le cycle Demande → Validation → Confirmation décrit dans [`PRODUCT.md`](./PRODUCT.md) §4. UC15 (disponibilités) est un prérequis technique à UC6 : une demande ne peut être soumise que sur un bien disponible.

## 4. Diagrammes de classes

### 4.1 Vue d'ensemble inter-domaines

Vue simplifiée montrant uniquement les classes principales et leurs relations à travers les sept domaines de [`ARCHITECTURE.md`](./ARCHITECTURE.md) §6. Le détail complet des attributs est donné dans les sous-sections 4.2 à 4.8, aligné sur [`DATABASE.md`](./DATABASE.md).

```mermaid
classDiagram
    class User
    class Partner
    class Residence
    class Vehicle
    class AvailabilityBlock
    class Reservation
    class CounterOffer
    class Notification
    class AdminAction

    User "1" --> "0..1" Partner : est un partenaire
    Partner "1" --> "*" Residence : possède
    Partner "1" --> "*" Vehicle : possède
    Residence "1" --> "*" AvailabilityBlock : blockable
    Vehicle "1" --> "*" AvailabilityBlock : blockable
    User "1" --> "*" Reservation : client_id
    Residence "1" --> "*" Reservation : reservable
    Vehicle "1" --> "*" Reservation : reservable
    Reservation "1" --> "0..1" CounterOffer : original_reservation_id
    Reservation "1" --> "*" AvailabilityBlock : reservation_id
    User "1" --> "*" Notification : user_id
    User "1" --> "*" AdminAction : admin_id
```

### 4.2 Domaine Identity

```mermaid
classDiagram
    class User {
        +UUID id
        +string fullName
        +string email
        +string password
        +string phone
        +Role role
        +DateTime emailVerifiedAt
        +DateTime createdAt
        +DateTime updatedAt
        +DateTime deletedAt
        +isPartner() bool
        +isAdmin() bool
    }
    class Role {
        <<enumeration>>
        CLIENT
        PARTNER
        ADMIN
    }
    User --> Role
```

Correspond à la table `users` de [`DATABASE.md`](./DATABASE.md) §4.1. Pour le MVP, `role` est un simple champ énuméré sur `User` (pas de table `permissions` séparée — voir §4.2 de `DATABASE.md`).

### 4.3 Domaine Partners

```mermaid
classDiagram
    class Partner {
        +UUID id
        +UUID userId
        +string companyName
        +string legalDocumentPath
        +PartnerStatus status
        +DateTime validatedAt
        +UUID validatedBy
        +DateTime createdAt
        +DateTime updatedAt
    }
    class PartnerStatus {
        <<enumeration>>
        EN_ATTENTE
        VALIDE
        REJETE
        SUSPENDU
    }
    class User
    Partner "1" --> "1" User : user_id
    Partner "0..1" --> "1" User : validated_by (admin)
    Partner --> PartnerStatus
```

Correspond à la table `partners` de [`DATABASE.md`](./DATABASE.md) §5.1. Contrainte d'unicité : un `User` ne peut avoir qu'un seul `Partner` associé (`UNIQUE` sur `user_id`, voir `DATABASE.md` §12.2).

### 4.4 Domaine Catalogue

```mermaid
classDiagram
    class OffreReservable {
        <<interface>>
        +dailyRate() decimal
        +isAvailable(period) bool
        +status ListingStatus
    }
    class Residence {
        +UUID id
        +UUID partnerId
        +string title
        +string description
        +string address
        +string city
        +int capacity
        +decimal dailyRate
        +jsonb attributes
        +ListingStatus status
        +DateTime createdAt
        +DateTime updatedAt
        +DateTime deletedAt
    }
    class Vehicle {
        +UUID id
        +UUID partnerId
        +string brand
        +string model
        +int year
        +string plateNumber
        +decimal dailyRate
        +jsonb attributes
        +ListingStatus status
        +DateTime createdAt
        +DateTime updatedAt
        +DateTime deletedAt
    }
    class ResidencePhoto {
        +UUID id
        +UUID residenceId
        +string path
        +int position
        +DateTime createdAt
    }
    class VehiclePhoto {
        +UUID id
        +UUID vehicleId
        +string path
        +int position
        +DateTime createdAt
    }
    class ListingStatus {
        <<enumeration>>
        BROUILLON
        EN_VALIDATION
        PUBLIEE
        REJETEE
        SUSPENDUE
    }
    class Partner

    OffreReservable <|.. Residence : implémente
    OffreReservable <|.. Vehicle : implémente
    Partner "1" --> "*" Residence : partner_id
    Partner "1" --> "*" Vehicle : partner_id
    Residence "1" --> "*" ResidencePhoto : residence_id
    Vehicle "1" --> "*" VehiclePhoto : vehicle_id
    Residence --> ListingStatus
    Vehicle --> ListingStatus
```

Correspond aux tables `residences`, `residence_photos`, `vehicles`, `vehicle_photos` de [`DATABASE.md`](./DATABASE.md) §6. L'interface `OffreReservable` matérialise l'abstraction d'extensibilité décrite dans [`ARCHITECTURE.md`](./ARCHITECTURE.md) §13 : toute catégorie future (hôtel, villa, salle, bureau, excursion, chauffeur) devra l'implémenter pour bénéficier du moteur de réservation existant sans modification des domaines `Availability`, `Reservation`, `Communication` et `Administration`.

### 4.5 Domaine Availability

```mermaid
classDiagram
    class AvailabilityBlock {
        +UUID id
        +string blockableType
        +UUID blockableId
        +DateRange period
        +BlockOrigin origin
        +UUID reservationId
        +UUID createdBy
        +DateTime createdAt
        +DateTime updatedAt
    }
    class BlockOrigin {
        <<enumeration>>
        RESERVATION
        ENTRETIEN
        MAINTENANCE
        USAGE_PERSONNEL
        AUTRE
    }
    class Residence
    class Vehicle
    class Reservation
    class User

    AvailabilityBlock --> BlockOrigin
    AvailabilityBlock "*" --> "1" Residence : blockable (si blockable_type=residence)
    AvailabilityBlock "*" --> "1" Vehicle : blockable (si blockable_type=vehicle)
    AvailabilityBlock "0..*" --> "0..1" Reservation : reservation_id
    AvailabilityBlock "*" --> "1" User : created_by
```

Correspond à la table polymorphique `availability_blocks` de [`DATABASE.md`](./DATABASE.md) §7. La relation vers `Residence`/`Vehicle` est polymorphe (`blockable_type` + `blockable_id`), conformément au principe d'extensibilité — voir aussi §7.3 pour la règle de non-libération automatique d'un blocage manuel.

**Invariant métier critique** (porté par une contrainte d'exclusion PostgreSQL, voir `DATABASE.md` §7.2 et §12.1) : pour un même `blockableType` + `blockableId`, deux `period` ne peuvent jamais se chevaucher. C'est la traduction technique directe de la règle d'exclusivité de [`BUSINESS_RULES.md`](./BUSINESS_RULES.md) §7.2.

### 4.6 Domaine Reservation

```mermaid
classDiagram
    class Reservation {
        +UUID id
        +UUID clientId
        +string reservableType
        +UUID reservableId
        +DateRange period
        +int nightsCount
        +decimal totalAmount
        +ReservationStatus status
        +UUID parentReservationId
        +DateTime createdAt
        +DateTime updatedAt
    }
    class ReservationStatus {
        <<enumeration>>
        EN_ATTENTE
        CONFIRMEE
        REFUSEE
        CONTRE_PROPOSEE
        EXPIREE
    }
    class ReservationStatusHistory {
        +UUID id
        +UUID reservationId
        +string previousStatus
        +string newStatus
        +UUID changedBy
        +DateTime changedAt
    }
    class CounterOffer {
        +UUID id
        +UUID originalReservationId
        +string proposedReservableType
        +UUID proposedReservableId
        +DateRange proposedPeriod
        +CounterOfferStatus status
        +DateTime expiresAt
        +DateTime createdAt
        +DateTime updatedAt
    }
    class CounterOfferStatus {
        <<enumeration>>
        EN_ATTENTE
        ACCEPTEE
        REFUSEE
        EXPIREE
    }
    class User
    class Residence
    class Vehicle

    Reservation --> ReservationStatus
    Reservation "1" --> "*" ReservationStatusHistory : reservation_id
    Reservation "1" --> "0..1" CounterOffer : original_reservation_id
    Reservation "0..1" --> "0..1" Reservation : parent_reservation_id (demande initiale)
    Reservation "*" --> "1" User : client_id
    Reservation "*" --> "1" Residence : reservable (si type=residence)
    Reservation "*" --> "1" Vehicle : reservable (si type=vehicle)
    CounterOffer --> CounterOfferStatus
```

Correspond aux tables `reservations`, `reservation_status_history`, `counter_offers` de [`DATABASE.md`](./DATABASE.md) §8. `parentReservationId` matérialise le lien entre une contre-proposition acceptée et sa demande initiale, conformément à [`BUSINESS_RULES.md`](./BUSINESS_RULES.md) §6.

### 4.7 Domaine Communication

```mermaid
classDiagram
    class Notification {
        +UUID id
        +UUID userId
        +string type
        +jsonb payload
        +DateTime readAt
        +DateTime createdAt
    }
    class User
    User "1" --> "*" Notification : user_id
```

Correspond à la table `notifications` de [`DATABASE.md`](./DATABASE.md) §9. Les types de notification du MVP sont listés dans [`ARCHITECTURE.md`](./ARCHITECTURE.md) §12.

### 4.8 Domaine Administration

```mermaid
classDiagram
    class AdminAction {
        +UUID id
        +UUID adminId
        +string actionType
        +string targetType
        +UUID targetId
        +string notes
        +DateTime createdAt
    }
    class PlatformSetting {
        +UUID id
        +string key
        +jsonb value
        +DateTime updatedAt
    }
    class User
    User "1" --> "*" AdminAction : admin_id
```

Correspond aux tables `admin_actions` et `platform_settings` de [`DATABASE.md`](./DATABASE.md) §10. `targetType` + `targetId` forment une référence polymorphe vers l'entité concernée (`partner`, `residence`, `vehicle`, `user`, etc.).

## 5. Diagrammes de séquence

Chaque séquence suit l'anatomie de domaine décrite dans [`ARCHITECTURE.md`](./ARCHITECTURE.md) §7 et reprend les flux déjà esquissés en §8 de ce même document, en les détaillant au niveau composant.

### 5.1 Demande de réservation

```mermaid
sequenceDiagram
    actor Client
    participant Controller as ReservationController
    participant Request as StoreReservationRequest
    participant Policy as ReservationPolicy
    participant Action as CreerDemandeReservationAction
    participant Repo as AvailabilityRepository
    participant Model as Reservation (Eloquent)
    participant Event as DemandeReservationCreee
    participant Listener as NotifierPartenaireNouvelleDemande

    Client->>Controller: POST /reservations (bien, période)
    Controller->>Request: valider les données
    Request-->>Controller: OK
    Controller->>Policy: le bien est-il réservable ?
    Policy-->>Controller: autorisé
    Controller->>Action: exécuter(bien, période, client)
    Action->>Repo: vérifier disponibilité(bien, période)
    Repo-->>Action: disponible
    Action->>Model: créer Reservation(status=EN_ATTENTE)
    Model-->>Action: Reservation créée
    Action->>Event: émettre DemandeReservationCreee
    Event->>Listener: notifier
    Listener-->>Client: (aucun effet direct)
    Action-->>Controller: Reservation
    Controller-->>Client: 201 Created
```

Conforme à [`ARCHITECTURE.md`](./ARCHITECTURE.md) §8.1 et [`BUSINESS_RULES.md`](./BUSINESS_RULES.md) §5.1 : aucun blocage calendrier n'est créé à cette étape.

### 5.2 Acceptation directe d'une demande

```mermaid
sequenceDiagram
    actor Partenaire
    participant Controller as ReservationController
    participant Policy as ReservationPolicy
    participant Action as ConfirmerReservationAction
    participant Model as Reservation
    participant Event as ReservationConfirmee
    participant L1 as Listener BloquerCalendrier
    participant L2 as Listener NotifierClientConfirmation
    participant L3 as Listener EnregistrerHistorique
    participant Block as AvailabilityBlock

    Partenaire->>Controller: POST /reservations/{id}/accepter
    Controller->>Policy: le partenaire possède-t-il le bien ?
    Policy-->>Controller: autorisé
    Controller->>Action: exécuter(reservation)
    Action->>Model: status = CONFIRMEE
    Model-->>Action: sauvegardée
    Action->>Event: émettre ReservationConfirmee
    Event->>L1: bloquer le calendrier
    L1->>Block: créer AvailabilityBlock(origin=RESERVATION)
    Event->>L2: notifier le client
    Event->>L3: écrire ReservationStatusHistory
    Action-->>Controller: Reservation confirmée
    Controller-->>Partenaire: 200 OK
```

Conforme à [`ARCHITECTURE.md`](./ARCHITECTURE.md) §8.2 et [`BUSINESS_RULES.md`](./BUSINESS_RULES.md) §5.3 : blocage automatique, notification des deux parties, historisation — dans le même cycle d'événement.

### 5.3 Refus avec contre-proposition — acceptation

```mermaid
sequenceDiagram
    actor Partenaire
    actor Client
    participant Controller as ReservationController
    participant Action1 as RefuserAvecContrePropositionAction
    participant Model as Reservation
    participant CO as CounterOffer
    participant Event1 as ContrePropositionSoumise
    participant Action2 as AccepterContrePropositionAction
    participant Event2 as ReservationConfirmee

    Partenaire->>Controller: POST /reservations/{id}/contre-proposer (bien alternatif, période)
    Controller->>Action1: exécuter(reservation, bien alternatif)
    Action1->>Model: vérifier disponibilité du bien alternatif
    Action1->>Model: status = CONTRE_PROPOSEE
    Action1->>CO: créer CounterOffer(status=EN_ATTENTE, expiresAt)
    Action1->>Event1: émettre ContrePropositionSoumise
    Event1-->>Client: notification (Communication)

    Client->>Controller: POST /counter-offers/{id}/accepter
    Controller->>Action2: exécuter(counterOffer)
    Action2->>CO: status = ACCEPTEE
    Action2->>Model: nouvelle Reservation(status=CONFIRMEE, parentReservationId=originale)
    Action2->>Event2: émettre ReservationConfirmee
    Note over Event2: déclenche le même cycle que §5.2 (blocage, notifications, historique)
```

Conforme à [`BUSINESS_RULES.md`](./BUSINESS_RULES.md) §6 : une seule contre-proposition par demande refusée, portant obligatoirement sur un bien disponible au moment de la soumission.

### 5.4 Refus avec contre-proposition — expiration

```mermaid
sequenceDiagram
    participant Scheduler as Job planifié (Queue)
    participant CO as CounterOffer
    participant Event as ContrePropositionExpiree
    participant Listener as NotifierClientEtPartenaire
    actor Client
    actor Partenaire

    Scheduler->>CO: vérifier expiresAt < maintenant
    CO-->>Scheduler: contre-proposition expirée
    Scheduler->>CO: status = EXPIREE
    Scheduler->>Event: émettre ContrePropositionExpiree
    Event->>Listener: notifier
    Listener-->>Client: notification
    Listener-->>Partenaire: notification
    Note over CO: La Reservation initiale associée passe également à EXPIREE
```

Conforme à [`BUSINESS_RULES.md`](./BUSINESS_RULES.md) §6.2 et [`ARCHITECTURE.md`](./ARCHITECTURE.md) §10 (expiration pilotée par job planifié via Redis/Queues).

### 5.5 Refus simple

```mermaid
sequenceDiagram
    actor Partenaire
    participant Controller as ReservationController
    participant Action as RefuserReservationAction
    participant Model as Reservation
    participant Event as ReservationRefusee
    participant Listener as NotifierClientRefus
    actor Client

    Partenaire->>Controller: POST /reservations/{id}/refuser
    Controller->>Action: exécuter(reservation)
    Action->>Model: status = REFUSEE
    Action->>Event: émettre ReservationRefusee
    Event->>Listener: notifier
    Listener-->>Client: notification (cycle clos)
```

Conforme à [`BUSINESS_RULES.md`](./BUSINESS_RULES.md) §5.2 (option 2) : aucun blocage calendrier, cycle définitivement clos.

### 5.6 Blocage manuel de calendrier (véhicule)

```mermaid
sequenceDiagram
    actor Partenaire
    participant Controller as AvailabilityController
    participant Policy as AvailabilityPolicy
    participant Action as CreerBlocageManuel
    participant Repo as AvailabilityRepository
    participant Model as AvailabilityBlock
    participant Event as CalendrierBloqueManuel
    participant Listener as NotifierCommunication

    Partenaire->>Controller: POST /vehicles/{id}/blocages (période, motif)
    Controller->>Policy: le partenaire possède-t-il le véhicule ?
    Policy-->>Controller: autorisé
    Controller->>Action: exécuter(véhicule, période, motif)
    Action->>Repo: vérifier non-chevauchement (contrainte GiST)
    Repo-->>Action: aucune période conflictuelle
    Action->>Model: créer AvailabilityBlock(origin=motif)
    Model-->>Action: créé
    Action->>Event: émettre CalendrierBloqueManuel
    Event->>Listener: notifier (le cas échéant)
    Action-->>Controller: AvailabilityBlock
    Controller-->>Partenaire: 201 Created
```

Conforme à [`BUSINESS_RULES.md`](./BUSINESS_RULES.md) §4.2 : motifs `entretien`, `maintenance`, `usage_personnel` — même priorité qu'un blocage automatique de réservation.

### 5.7 Validation d'un partenaire par l'administration

```mermaid
sequenceDiagram
    actor Admin as Administrateur
    participant Controller as AdminPartnerController
    participant Policy as AdminPolicy
    participant Action as ValiderPartenaireAction
    participant Model as Partner
    participant Event as PartenaireValide
    participant L1 as Listener ActiverAccesTableauDeBord
    participant L2 as Listener NotifierPartenaireValidation
    actor Partenaire

    Admin->>Controller: POST /admin/partners/{id}/valider
    Controller->>Policy: l'utilisateur est-il administrateur ?
    Policy-->>Controller: autorisé
    Controller->>Action: exécuter(partner)
    Action->>Model: status = VALIDE, validatedAt, validatedBy
    Action->>Event: émettre PartenaireValide
    Event->>L1: activer l'accès au tableau de bord (Partners)
    Event->>L2: notifier le partenaire (Communication)
    L2-->>Partenaire: notification « compte validé »
    Action-->>Controller: Partner validé
    Controller-->>Admin: 200 OK
```

Conforme à [`ARCHITECTURE.md`](./ARCHITECTURE.md) §8.3 et [`PRODUCT.md`](./PRODUCT.md) §8.3.

### 5.8 Validation d'une annonce par l'administration

```mermaid
sequenceDiagram
    actor Admin as Administrateur
    participant Controller as AdminListingController
    participant Policy as AdminPolicy
    participant Action as ValiderAnnonceAction
    participant Model as Residence ou Vehicle
    participant Event as AnnonceValidee
    participant L1 as Listener PublierAnnonce
    participant L2 as Listener NotifierPartenaireValidationAnnonce
    actor Partenaire

    Admin->>Controller: POST /admin/listings/{id}/valider
    Controller->>Policy: l'utilisateur est-il administrateur ?
    Policy-->>Controller: autorisé
    Controller->>Action: exécuter(annonce)
    Action->>Model: status = PUBLIEE
    Action->>Event: émettre AnnonceValidee
    Event->>L1: publier l'annonce (Catalogue)
    Event->>L2: notifier le partenaire (Communication)
    L2-->>Partenaire: notification « annonce publiée »
    Action-->>Controller: annonce publiée
    Controller-->>Admin: 200 OK
```

Conforme à l'événement `AnnonceValidee` listé dans [`ARCHITECTURE.md`](./ARCHITECTURE.md) §9 et au parcours de validation décrit dans [`PRODUCT.md`](./PRODUCT.md) §8.3 (« chaque annonce soumise suit également un cycle de validation avant publication »).

## 6. Diagramme d'états — Réservation

```mermaid
stateDiagram-v2
    [*] --> EN_ATTENTE : demande soumise
    EN_ATTENTE --> CONFIRMEE : acceptation directe
    EN_ATTENTE --> REFUSEE : refus sans alternative
    EN_ATTENTE --> CONTRE_PROPOSEE : refus avec alternative
    CONTRE_PROPOSEE --> CONFIRMEE : proposition acceptée (sur bien alternatif)
    CONTRE_PROPOSEE --> REFUSEE : proposition refusée
    CONTRE_PROPOSEE --> EXPIREE : délai dépassé
    CONFIRMEE --> [*]
    REFUSEE --> [*]
    EXPIREE --> [*]
```

Transcription directe de la machine à états de [`BUSINESS_RULES.md`](./BUSINESS_RULES.md) §8. Chaque transition correspond à une entrée dans `reservation_status_history` (voir `DATABASE.md` §8.2).

## 7. Diagramme d'états — Contre-proposition

```mermaid
stateDiagram-v2
    [*] --> EN_ATTENTE : soumise par le partenaire
    EN_ATTENTE --> ACCEPTEE : client accepte
    EN_ATTENTE --> REFUSEE : client refuse
    EN_ATTENTE --> EXPIREE : délai dépassé sans réponse
    ACCEPTEE --> [*]
    REFUSEE --> [*]
    EXPIREE --> [*]
```

Correspond au champ `status` de la table `counter_offers` ([`DATABASE.md`](./DATABASE.md) §8.3) et aux règles de [`BUSINESS_RULES.md`](./BUSINESS_RULES.md) §6.2, y compris le cas d'invalidation automatique si le bien alternatif devient indisponible avant la réponse du client ([`BUSINESS_RULES.md`](./BUSINESS_RULES.md) §10).

## 8. Diagramme d'états — Partenaire

```mermaid
stateDiagram-v2
    [*] --> EN_ATTENTE : inscription soumise
    EN_ATTENTE --> VALIDE : validation admin
    EN_ATTENTE --> REJETE : rejet admin
    VALIDE --> SUSPENDU : suspension admin
    SUSPENDU --> VALIDE : réactivation admin
    REJETE --> [*]
```

Correspond au champ `status` de la table `partners` ([`DATABASE.md`](./DATABASE.md) §5.1). La transition `SUSPENDU → VALIDE` n'est pas explicitement détaillée dans `BUSINESS_RULES.md` ; elle est déduite de la liste des valeurs d'énumération et doit être confirmée lors de la conception des règles d'administration si elle n'est pas déjà couverte.

## 9. Diagramme d'états — Annonce (Résidence / Véhicule)

```mermaid
stateDiagram-v2
    [*] --> BROUILLON : création par le partenaire
    BROUILLON --> EN_VALIDATION : soumission pour publication
    EN_VALIDATION --> PUBLIEE : validation admin
    EN_VALIDATION --> REJETEE : rejet admin
    PUBLIEE --> SUSPENDUE : suspension admin
    SUSPENDUE --> PUBLIEE : réactivation admin
    REJETEE --> BROUILLON : correction par le partenaire
```

Correspond au champ `status` des tables `residences` et `vehicles` ([`DATABASE.md`](./DATABASE.md) §6.1 et §6.3), commun aux deux catégories d'offres du MVP conformément au principe d'alignement des règles décrit dans [`BUSINESS_RULES.md`](./BUSINESS_RULES.md) §1.

## 10. Traçabilité avec la documentation existante

| Diagramme | Source(s) métier | Source(s) technique(s) |
|---|---|---|
| Cas d'utilisation | [`PRODUCT.md`](./PRODUCT.md) §6, §9 | — |
| Classes — toutes sections | — | [`DATABASE.md`](./DATABASE.md) §4-10 |
| Classes — `OffreReservable` | [`PRODUCT.md`](./PRODUCT.md) §12 | [`ARCHITECTURE.md`](./ARCHITECTURE.md) §13 |
| Séquences — cycle de réservation (§5.1-5.6) | [`BUSINESS_RULES.md`](./BUSINESS_RULES.md) §2, §5, §6 | [`ARCHITECTURE.md`](./ARCHITECTURE.md) §7, §8, §9 |
| Séquences — validation admin (§5.7-5.8) | [`PRODUCT.md`](./PRODUCT.md) §8.3 | [`ARCHITECTURE.md`](./ARCHITECTURE.md) §8.3, §9 |
| États — Réservation | [`BUSINESS_RULES.md`](./BUSINESS_RULES.md) §8 | [`DATABASE.md`](./DATABASE.md) §8.1 |
| États — Contre-proposition | [`BUSINESS_RULES.md`](./BUSINESS_RULES.md) §6 | [`DATABASE.md`](./DATABASE.md) §8.3 |
| États — Partenaire | — | [`DATABASE.md`](./DATABASE.md) §5.1 |
| États — Annonce | [`BUSINESS_RULES.md`](./BUSINESS_RULES.md) §1 | [`DATABASE.md`](./DATABASE.md) §6.1, §6.3 |

Toute modification d'une règle métier ou d'un schéma de données doit être répercutée dans ce document avant d'être considérée comme conçue, conformément au principe « aucun développement sans conception préalable » ([`ENGINEERING.md`](./ENGINEERING.md) §2).
