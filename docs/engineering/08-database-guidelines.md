# 08 — Database Guidelines (PostgreSQL)

## Table des matières

1. [Portée du document](#1-portée-du-document)
2. [Conventions de nommage détaillées](#2-conventions-de-nommage-détaillées)
3. [UUID](#3-uuid)
4. [Colonnes JSON/JSONB](#4-colonnes-jsonjsonb)
5. [Périodes (daterange) et contraintes d'exclusion](#5-périodes-daterange-et-contraintes-dexclusion)
6. [Index](#6-index)
7. [Migrations — règles d'écriture](#7-migrations--règles-décriture)
8. [Seeders](#8-seeders)
9. [Factories](#9-factories)
10. [Requêtes — bonnes pratiques Eloquent](#10-requêtes--bonnes-pratiques-eloquent)

---

## 1. Portée du document

Ce document complète [`DATABASE.md`](../../DATABASE.md) (schéma de référence) avec les règles d'écriture concrètes : comment nommer, migrer, indexer et peupler les tables au quotidien.

## 2. Conventions de nommage détaillées

| Élément | Convention | Exemple |
|---|---|---|
| Table | `snake_case` pluriel | `residence_photos` |
| Colonne booléenne | préfixe `is_` ou `has_` | `is_published`, `has_documents` |
| Colonne de clé étrangère | `<entite_singulier>_id` | `partner_id` |
| Colonne enum | nom au singulier, valeurs en `snake_case` | `status` avec `en_attente`, `confirmee` |
| Table pivot | noms des deux entités au singulier, ordre alphabétique | `partner_user` (si pivot nécessaire) |
| Migration | `YYYY_MM_DD_HHMMSS_verbe_sujet.php` | `2026_02_10_093000_create_reservations_table.php` |

## 3. UUID

- Toute clé primaire est un UUID v4, généré par défaut via `gen_random_uuid()` côté PostgreSQL (extension `pgcrypto` ou fonction native selon version) ou via `Str::uuid()` côté Laravel si la génération applicative est préférée pour un cas précis.
- Migration type :

```php
Schema::create('residences', function (Blueprint $table) {
    $table->uuid('id')->primary()->default(DB::raw('gen_random_uuid()'));
    $table->foreignUuid('partner_id')->references('id')->on('partners');
    // ...
    $table->timestamps();
    $table->softDeletes();
});
```

- Aucun identifiant séquentiel (`bigIncrements`) n'est utilisé comme clé primaire exposée, même en interne, pour éviter toute divergence future entre modèle interne et modèle exposé.

## 4. Colonnes JSON/JSONB

- Toujours `jsonb`, jamais `json` simple (indexation et performance de requêtes bien supérieures sous PostgreSQL).
- Utilisées pour des données réellement semi-structurées et non interrogées finement (ex : `attributes` d'une résidence — équipements libres). Toute donnée nécessitant un filtrage fréquent et structuré (ex : `city`, `status`) reste en colonne dédiée, jamais imbriquée dans un `jsonb`.
- Exemple de migration :

```php
$table->jsonb('attributes')->nullable();
```

- Accès Eloquent typé via un cast `array` ou un Value Object dédié (`app/Support/ValueObjects/ResidenceAttributes.php`), jamais manipulation de tableau brute dispersée dans le code métier.

## 5. Périodes (daterange) et contraintes d'exclusion

- Toute période (réservation, blocage) est stockée en `daterange` PostgreSQL plutôt qu'en deux colonnes `start_date`/`end_date` séparées, pour bénéficier des opérateurs natifs de chevauchement (`&&`) et des contraintes d'exclusion GiST.
- Contrainte systématique de non-chevauchement pour toute table de blocage, reprise de [`DATABASE.md`](../../DATABASE.md) §7.2 :

```sql
CREATE EXTENSION IF NOT EXISTS btree_gist;

ALTER TABLE availability_blocks
  ADD CONSTRAINT excl_availability_no_overlap
  EXCLUDE USING gist (
    blockable_type WITH =,
    blockable_id WITH =,
    period WITH &&
  );
```

- Toute nouvelle table introduisant une notion de période réservable doit reproduire ce même schéma de contrainte, jamais une vérification uniquement applicative.

## 6. Index

Règles générales d'indexation (voir aussi `11-performance-guidelines.md` pour l'angle performance) :

- indexer systématiquement toute colonne de clé étrangère utilisée dans une jointure fréquente ;
- indexer les colonnes utilisées dans les clauses `WHERE` de recherche publique (`city`, `status`) ;
- utiliser un index composite plutôt que plusieurs index simples lorsque les colonnes sont systématiquement filtrées ensemble (`(city, status)` plutôt que deux index séparés) ;
- ne jamais indexer une colonne à faible cardinalité isolément si elle n'est filtrée qu'en combinaison avec une autre (l'index composite suffit et coûte moins cher en écriture).

## 7. Migrations — règles d'écriture

- Chaque migration est **atomique** : elle crée ou modifie un seul concept cohérent (une table, ou un ajout de contrainte lié).
- Chaque migration est **réversible** : la méthode `down()` doit annuler exactement ce que fait `up()`.
- Aucune migration ne modifie des données existantes en production sans être explicitement nommée comme migration de données (`..._backfill_...`) et validée séparément en revue de code.
- Toute modification de colonne sur une table volumineuse en production doit être testée en `staging` avec un volume représentatif avant d'être planifiée (voir [`DEPLOYMENT.md`](../../DEPLOYMENT.md) §5).
- Une clé étrangère **auto-référencée** (ex : `parent_reservation_id` sur `reservations`, voir [`DATABASE.md`](../../DATABASE.md) §8.1) ne doit jamais être déclarée avec `->constrained()` à l'intérieur du `Schema::create()` qui crée la table. Le Schema Builder de Laravel exécute les commandes implicites (l'index de clé primaire posé par `->primary()` sur une colonne) après les commandes explicites (`->constrained()`) au sein d'un même `Schema::create()`, ce qui fait échouer la contrainte sur PostgreSQL (`there is no unique constraint matching given keys`) puisque la clé primaire n'existe pas encore au moment où la FK est posée. La colonne doit être déclarée sans contrainte dans `Schema::create()`, puis la clé étrangère ajoutée dans un `Schema::table()` séparé, une fois la table (et sa clé primaire) pleinement créée — voir `2026_07_21_090300_create_reservations_table.php`.

## 8. Seeders

- `DatabaseSeeder` orchestre un jeu de données de démonstration complet et cohérent pour l'environnement `local` uniquement (voir [`DATABASE.md`](../../DATABASE.md) §14).
- Chaque domaine peut avoir son propre seeder dédié (`ReservationSeeder`, `PartnerSeeder`), appelé depuis `DatabaseSeeder`.
- Interdiction absolue d'exécuter un seeder de démonstration en `production` — une vérification d'environnement doit bloquer cette exécution par défaut.

## 9. Factories

- Une `Factory` par modèle Eloquent principal, produisant des données réalistes et variées (pas uniquement des valeurs par défaut identiques).
- Les états de `Factory` (`->state()`) reflètent les états métier réels : `Reservation::factory()->confirmee()`, `Reservation::factory()->enAttente()`, permettant d'écrire des tests lisibles.

```php
Reservation::factory()->confirmee()->for($client)->create([
    'reservable_id' => $residence->id,
    'reservable_type' => 'residence',
]);
```

## 10. Requêtes — bonnes pratiques Eloquent

- Toujours utiliser l'eager loading (`with()`) pour toute relation affichée en liste, afin d'éviter les requêtes N+1 (détail dans `11-performance-guidelines.md`).
- Toute requête complexe (filtrage multi-critères de recherche) est encapsulée dans un `Repository` ou un objet de requête dédié (`ResidenceSearchQuery`), jamais construite en ligne dans un contrôleur.
- Aucune requête SQL brute (`DB::raw`, `DB::statement`) sauf nécessité justifiée (ex : contrainte d'exclusion en migration) ; toute requête métier applicative passe par le query builder ou Eloquent.
