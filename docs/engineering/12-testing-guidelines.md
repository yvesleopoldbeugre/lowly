# 12 — Testing Guidelines

## Table des matières

1. [Portée du document](#1-portée-du-document)
2. [Structure des fichiers de test](#2-structure-des-fichiers-de-test)
3. [Conventions Pest](#3-conventions-pest)
4. [Tests unitaires — conventions](#4-tests-unitaires--conventions)
5. [Tests Feature — conventions](#5-tests-feature--conventions)
6. [Tests Browser — conventions](#6-tests-browser--conventions)
7. [Fixtures et Factories dans les tests](#7-fixtures-et-factories-dans-les-tests)
8. [Seuils de couverture](#8-seuils-de-couverture)
9. [Exécution locale et en CI](#9-exécution-locale-et-en-ci)

---

## 1. Portée du document

Ce document complète [`TESTING.md`](../../TESTING.md) avec les conventions d'écriture concrètes des tests Pest/PHPUnit sur LOWLY.

## 2. Structure des fichiers de test

```
tests/
├── Unit/
│   └── Domains/
│       ├── Reservation/
│       │   ├── CalculateurJourneesTest.php
│       │   └── ConfirmerReservationTest.php
│       └── Availability/
│           └── VerificateurDisponibiliteTest.php
├── Feature/
│   └── Domains/
│       ├── Reservation/
│       │   ├── CreerDemandeReservationTest.php
│       │   └── AccepterReservationTest.php
│       └── Administration/
│           └── ValiderPartenaireTest.php
└── Browser/
    └── ParcoursReservationCompletTest.php
```

La structure des tests reflète l'arborescence des domaines décrite dans `04-architecture.md` §3, pour retrouver immédiatement le test correspondant à un domaine.

## 3. Conventions Pest

- Syntaxe Pest préférée à la syntaxe PHPUnit classique pour toute nouvelle suite de tests.
- Chaque fichier de test décrit un comportement en langage clair dans son premier argument :

```php
it('calcule 3 journées pour une arrivée le 10 janvier et un départ le 13 janvier', function () {
    $calculateur = new CalculateurJournees();

    $resultat = $calculateur->calculer(
        Carbon::parse('2026-01-10'),
        Carbon::parse('2026-01-13'),
    );

    expect($resultat)->toBe(3);
});
```

- Regroupement par `describe()` lorsque plusieurs cas concernent la même unité testée.

## 4. Tests unitaires — conventions

- Aucun accès base de données dans un test unitaire pur (`Unit/`) — toute dépendance externe (`Repository`, `Service` tiers) est mockée.
- Un test unitaire par cas métier distinct, y compris les cas limites listés dans [`BUSINESS_RULES.md`](../../BUSINESS_RULES.md) §10 :

```php
it('rejette une réservation avec arrivée et départ le même jour', function () {
    // ...
});

it('calcule correctement une réservation d\'une seule nuit', function () {
    // ...
});
```

## 5. Tests Feature — conventions

- Chaque test Feature utilise `RefreshDatabase` (ou une transaction annulée) pour garantir l'indépendance entre tests.
- Un test Feature vérifie à la fois le code HTTP retourné **et** l'état réel en base de données après l'action :

```php
it('bloque automatiquement le calendrier à la confirmation d\'une réservation', function () {
    $reservation = Reservation::factory()->enAttente()->for($residence, 'reservable')->create();

    $this->actingAs($partenaireUser)
        ->postJson("/api/v1/partner/reservations/{$reservation->id}/accept")
        ->assertOk();

    expect($reservation->refresh()->status)->toBe('confirmee');
    expect(AvailabilityBlock::where('reservation_id', $reservation->id)->exists())->toBeTrue();
});
```

## 6. Tests Browser — conventions

- Réservés aux parcours listés dans [`TESTING.md`](../../TESTING.md) §6, exécutés sur un environnement proche de la production (assets compilés, base de données de test dédiée).
- Chaque test Browser reste résilient aux temps d'attente réseau/Ajax (utilisation des attentes explicites du framework de test Browser, jamais de `sleep()` en dur).

## 7. Fixtures et Factories dans les tests

- Aucune donnée de test créée par insertion SQL brute : toujours via les `Factories` (voir `08-database-guidelines.md` §9).
- Les scénarios impliquant plusieurs entités liées (partenaire validé + résidence publiée + réservation confirmée) sont construits par composition de `Factories`, pas par duplication de code de setup entre fichiers de test — extraire un helper de test partagé si le même scénario apparaît dans plus de deux fichiers.

## 8. Seuils de couverture

- Seuil global minimal en CI : 80 % de couverture de lignes (ajustable, mais ne doit jamais régresser d'une Pull Request à l'autre).
- Seuil renforcé pour les domaines `Reservation` et `Availability` : 95 %, ces domaines portant les règles métier les plus critiques de [`BUSINESS_RULES.md`](../../BUSINESS_RULES.md).
- La couverture quantitative est un filet de sécurité, pas un objectif en soi : la présence effective des cas listés dans [`TESTING.md`](../../TESTING.md) §7 est vérifiée manuellement en revue de code (`14-code-review.md`).

## 9. Exécution locale et en CI

```bash
# Suite complète
./vendor/bin/pest

# Un domaine spécifique
./vendor/bin/pest tests/Feature/Domains/Reservation

# Avec couverture
./vendor/bin/pest --coverage --min=80
```

En CI, la suite complète s'exécute à chaque Pull Request (voir [`DEPLOYMENT.md`](../../DEPLOYMENT.md) §6) ; aucun merge n'est possible si un test échoue ou si le seuil de couverture régresse.
