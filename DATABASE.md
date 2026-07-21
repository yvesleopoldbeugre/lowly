# DATABASE.md — Modèle de Données LOWLY

## Table des matières

1. [Principes généraux](#1-principes-généraux)
2. [Conventions PostgreSQL](#2-conventions-postgresql)
3. [Schéma logique global](#3-schéma-logique-global)
4. [Domaine Identity](#4-domaine-identity)
5. [Domaine Partners](#5-domaine-partners)
6. [Domaine Catalogue](#6-domaine-catalogue)
7. [Domaine Availability](#7-domaine-availability)
8. [Domaine Reservation](#8-domaine-reservation)
9. [Domaine Communication](#9-domaine-communication)
10. [Domaine Administration](#10-domaine-administration)
11. [Index et performance](#11-index-et-performance)
12. [Contraintes d'intégrité critiques](#12-contraintes-dintégrité-critiques)
13. [Migrations](#13-migrations)
14. [Seeders et Factories](#14-seeders-et-factories)

---

## 1. Principes généraux

- Le SGBD de référence est **PostgreSQL 17**. Aucune fonctionnalité spécifique à un autre SGBD ne doit être utilisée.
- Chaque table possède une **clé primaire UUID** (`uuid`, générée applicativement ou via `gen_random_uuid()`), afin de faciliter la distribution future et éviter l'exposition d'identifiants séquentiels dans les URLs publiques.
- Les horodatages `created_at`, `updated_at` sont systématiques. `deleted_at` (soft delete) est utilisé pour les entités dont la suppression logique a un sens métier (annonces, réservations).
- Les colonnes de données semi-structurées (ex : caractéristiques d'une résidence) utilisent le type `jsonb` de PostgreSQL, jamais `json` simple, pour bénéficier de l'indexation.
- Toute contrainte métier vérifiable en base (unicité, non-chevauchement de périodes) **doit** être portée par une contrainte SQL, pas uniquement par la couche applicative.

## 2. Conventions PostgreSQL

| Élément | Convention |
|---|---|
| Nom de table | `snake_case`, pluriel (`residences`, `reservations`) |
| Clé primaire | `id` de type `uuid`, défaut `gen_random_uuid()` |
| Clé étrangère | `<entite_singulier>_id` (ex : `partner_id`) |
| Enum métier | Type PostgreSQL natif `CREATE TYPE ... AS ENUM (...)` ou colonne `varchar` contrainte par `CHECK`, selon volatilité de la liste de valeurs |
| Champs monétaires | `numeric(10,2)`, jamais `float`/`double` |
| Champs de période | `daterange` PostgreSQL pour les périodes de réservation et de blocage |
| Index | Préfixe `idx_<table>_<colonnes>` |
| Contrainte unique | Préfixe `uniq_<table>_<colonnes>` |
| Contrainte de vérification | Préfixe `chk_<table>_<règle>` |

Le détail complet des conventions de nommage, migrations et factories est dans `docs/engineering/08-database-guidelines.md`.

## 3. Schéma logique global

```
 users ──┬──► partners ──► residences ──┬──► residence_photos
         │                              ├──► residence_availabilities
         │                              └──► reservations
         │
         ├──► partners ──► vehicles ──┬──► vehicle_photos
         │                            ├──► vehicle_availabilities
         │                            └──► reservations
         │
         ├──► clients (rôle) ──► reservations
         │
         └──► roles / permissions

 reservations ──► reservation_status_history
 reservations ──► counter_offers
 reservations ──► notifications

 admin_actions ──► partners / listings (validation)
```

## 4. Domaine Identity

### 4.1 Table `users`

| Colonne | Type | Contrainte |
|---|---|---|
| `id` | uuid | PK |
| `full_name` | varchar(255) | NOT NULL |
| `email` | varchar(255) | UNIQUE, NOT NULL |
| `password` | varchar(255) | NOT NULL (haché) |
| `phone` | varchar(30) | NULL |
| `role` | enum(`client`,`partner`,`admin`) | NOT NULL |
| `email_verified_at` | timestamp | NULL |
| `created_at` / `updated_at` | timestamp | NOT NULL |
| `deleted_at` | timestamp | NULL |

### 4.2 Table `roles` / `permissions` (si RBAC fin nécessaire)

Pour le MVP, un champ `role` simple sur `users` suffit (client / partner / admin). Une table `permissions` pivot pourra être introduite si le besoin de permissions granulaires apparaît en post-MVP (voir `docs/engineering/10-security-guidelines.md`).

## 5. Domaine Partners

### 5.1 Table `partners`

| Colonne | Type | Contrainte |
|---|---|---|
| `id` | uuid | PK |
| `user_id` | uuid | FK → `users.id`, UNIQUE |
| `company_name` | varchar(255) | NULL |
| `legal_document_path` | varchar(255) | NULL |
| `status` | enum(`en_attente`,`valide`,`rejete`,`suspendu`) | NOT NULL, défaut `en_attente` |
| `validated_at` | timestamp | NULL |
| `validated_by` | uuid | FK → `users.id`, NULL |
| `created_at` / `updated_at` | timestamp | NOT NULL |

## 6. Domaine Catalogue

### 6.1 Table `residences`

| Colonne | Type | Contrainte |
|---|---|---|
| `id` | uuid | PK |
| `partner_id` | uuid | FK → `partners.id`, NOT NULL |
| `title` | varchar(255) | NOT NULL |
| `description` | text | NOT NULL |
| `address` | varchar(255) | NOT NULL |
| `city` | varchar(120) | NOT NULL |
| `capacity` | smallint | NOT NULL |
| `daily_rate` | numeric(10,2) | NOT NULL |
| `attributes` | jsonb | NULL (équipements, caractéristiques libres) |
| `status` | enum(`brouillon`,`en_validation`,`publiee`,`rejetee`,`suspendue`) | NOT NULL, défaut `brouillon` |
| `created_at` / `updated_at` / `deleted_at` | timestamp | |

### 6.2 Table `residence_photos`

| Colonne | Type | Contrainte |
|---|---|---|
| `id` | uuid | PK |
| `residence_id` | uuid | FK → `residences.id`, NOT NULL |
| `path` | varchar(255) | NOT NULL |
| `position` | smallint | NOT NULL, défaut 0 |
| `created_at` | timestamp | |

### 6.3 Table `vehicles`

| Colonne | Type | Contrainte |
|---|---|---|
| `id` | uuid | PK |
| `partner_id` | uuid | FK → `partners.id`, NOT NULL |
| `brand` | varchar(120) | NOT NULL |
| `model` | varchar(120) | NOT NULL |
| `year` | smallint | NULL |
| `plate_number` | varchar(30) | NULL |
| `daily_rate` | numeric(10,2) | NOT NULL |
| `attributes` | jsonb | NULL (climatisation, boîte, places, etc.) |
| `status` | enum(`brouillon`,`en_validation`,`publie`,`rejete`,`suspendu`) | NOT NULL, défaut `brouillon` |
| `created_at` / `updated_at` / `deleted_at` | timestamp | |

### 6.4 Table `vehicle_photos`

Structure identique à `residence_photos`, avec `vehicle_id` en clé étrangère.

## 7. Domaine Availability

### 7.1 Principe de modélisation

Chaque bien (résidence ou véhicule) possède une table de blocages représentant les périodes indisponibles, qu'elles soient d'origine automatique (réservation confirmée) ou manuelle (entretien, usage personnel, etc.).

### 7.2 Table `availability_blocks`

Table générique et polymorphique, réutilisable pour toute nouvelle catégorie d'offre future.

| Colonne | Type | Contrainte |
|---|---|---|
| `id` | uuid | PK |
| `blockable_type` | varchar(50) | NOT NULL (`residence`, `vehicle`, ...) |
| `blockable_id` | uuid | NOT NULL |
| `period` | daterange | NOT NULL |
| `origin` | enum(`reservation`,`entretien`,`maintenance`,`usage_personnel`,`autre`) | NOT NULL |
| `reservation_id` | uuid | FK → `reservations.id`, NULL (rempli si `origin = reservation`) |
| `created_by` | uuid | FK → `users.id`, NOT NULL |
| `created_at` / `updated_at` | timestamp | |

**Contrainte d'exclusion PostgreSQL** (empêche tout chevauchement de période pour un même bien) :

```sql
ALTER TABLE availability_blocks
  ADD CONSTRAINT excl_availability_no_overlap
  EXCLUDE USING gist (
    blockable_type WITH =,
    blockable_id WITH =,
    period WITH &&
  );
```

Cette contrainte est la garantie technique ultime, en plus de la logique applicative, qu'aucun bien ne peut être doublement bloqué sur une période qui se chevauche.

## 8. Domaine Reservation

### 8.1 Table `reservations`

| Colonne | Type | Contrainte |
|---|---|---|
| `id` | uuid | PK |
| `client_id` | uuid | FK → `users.id`, NOT NULL |
| `reservable_type` | varchar(50) | NOT NULL (`residence`, `vehicle`) |
| `reservable_id` | uuid | NOT NULL |
| `period` | daterange | NOT NULL |
| `nights_count` | smallint | NOT NULL (dérivé, voir `BUSINESS_RULES.md` §3.2) |
| `total_amount` | numeric(10,2) | NOT NULL |
| `status` | enum(`en_attente`,`confirmee`,`refusee`,`contre_proposee`,`expiree`) | NOT NULL, défaut `en_attente` |
| `parent_reservation_id` | uuid | FK → `reservations.id`, NULL (référence la demande initiale si contre-proposition) |
| `created_at` / `updated_at` | timestamp | |

### 8.2 Table `reservation_status_history`

| Colonne | Type | Contrainte |
|---|---|---|
| `id` | uuid | PK |
| `reservation_id` | uuid | FK → `reservations.id`, NOT NULL |
| `previous_status` | varchar(30) | NULL |
| `new_status` | varchar(30) | NOT NULL |
| `changed_by` | uuid | FK → `users.id`, NOT NULL |
| `changed_at` | timestamp | NOT NULL |

### 8.3 Table `counter_offers`

| Colonne | Type | Contrainte |
|---|---|---|
| `id` | uuid | PK |
| `original_reservation_id` | uuid | FK → `reservations.id`, NOT NULL |
| `proposed_reservable_type` | varchar(50) | NOT NULL |
| `proposed_reservable_id` | uuid | NOT NULL |
| `proposed_period` | daterange | NOT NULL |
| `status` | enum(`en_attente`,`acceptee`,`refusee`,`expiree`) | NOT NULL, défaut `en_attente` |
| `expires_at` | timestamp | NOT NULL |
| `created_at` / `updated_at` | timestamp | |

## 9. Domaine Communication

### 9.1 Table `notifications`

| Colonne | Type | Contrainte |
|---|---|---|
| `id` | uuid | PK |
| `user_id` | uuid | FK → `users.id`, NOT NULL |
| `type` | varchar(80) | NOT NULL (ex : `reservation_confirmee`) |
| `payload` | jsonb | NOT NULL |
| `read_at` | timestamp | NULL |
| `created_at` | timestamp | NOT NULL |

## 10. Domaine Administration

### 10.1 Table `admin_actions`

| Colonne | Type | Contrainte |
|---|---|---|
| `id` | uuid | PK |
| `admin_id` | uuid | FK → `users.id`, NOT NULL |
| `action_type` | varchar(80) | NOT NULL (ex : `validation_partenaire`, `rejet_annonce`) |
| `target_type` | varchar(50) | NOT NULL |
| `target_id` | uuid | NOT NULL |
| `notes` | text | NULL |
| `created_at` | timestamp | NOT NULL |

### 10.2 Table `platform_settings`

| Colonne | Type | Contrainte |
|---|---|---|
| `id` | uuid | PK |
| `key` | varchar(100) | UNIQUE, NOT NULL |
| `value` | jsonb | NOT NULL |
| `updated_at` | timestamp | |

## 11. Index et performance

| Table | Index recommandé | Justification |
|---|---|---|
| `residences` | `idx_residences_city_status` sur (`city`, `status`) | Recherche publique filtrée |
| `vehicles` | `idx_vehicles_status` sur (`status`) | Liste des véhicules publiés |
| `availability_blocks` | index GiST sur `period` (via la contrainte d'exclusion) | Recherche de disponibilité par plage |
| `reservations` | `idx_reservations_client_id`, `idx_reservations_status` | Historique client, file partenaire |
| `notifications` | `idx_notifications_user_id_read_at` | Liste des notifications non lues |

Le détail des bonnes pratiques d'indexation et de prévention des requêtes N+1 est dans `docs/engineering/11-performance-guidelines.md`.

## 12. Contraintes d'intégrité critiques

1. **Non-chevauchement des blocages de calendrier** (voir contrainte d'exclusion §7.2) — garantit qu'aucune double réservation n'est possible en base, indépendamment de la couche applicative.
2. **Unicité du profil partenaire par utilisateur** — un `user` ne peut avoir qu'un seul enregistrement `partners` (`UNIQUE` sur `user_id`).
3. **Cohérence des montants** — `total_amount` doit toujours être strictement positif (`CHECK (total_amount > 0)`).
4. **Cohérence des périodes** — la borne haute d'un `daterange` doit toujours être strictement supérieure à la borne basse (`CHECK (upper(period) > lower(period))`).

## 13. Migrations

- Chaque migration est atomique et réversible (`up` / `down` complets).
- Aucune migration ne doit modifier directement des données de production sans passer par une migration de données dédiée et documentée.
- Les migrations créant des contraintes d'exclusion GiST doivent activer l'extension PostgreSQL requise en amont :

```sql
CREATE EXTENSION IF NOT EXISTS btree_gist;
```

- Convention de nommage des fichiers de migration : `YYYY_MM_DD_HHMMSS_verbe_au_present_sujet.php` (convention standard Laravel).

## 14. Seeders et Factories

- Chaque domaine dispose de ses propres `Factories` Eloquent, situées dans `database/factories/`, permettant de générer des jeux de données cohérents (ex : un partenaire avec plusieurs résidences publiées et un historique de réservations).
- Un `DatabaseSeeder` central orchestre les seeders de démonstration pour l'environnement `local`, incluant au minimum : un jeu d'administrateurs, de partenaires validés avec annonces publiées, de clients, et de réservations dans différents états (`en_attente`, `confirmee`, `refusee`, `contre_proposee`).
- Aucun seeder de démonstration ne doit être exécuté en environnement `production`.
