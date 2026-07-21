# TESTING.md — Stratégie de Tests LOWLY

## Table des matières

1. [Portée du document](#1-portée-du-document)
2. [Pyramide de tests](#2-pyramide-de-tests)
3. [Outillage](#3-outillage)
4. [Tests unitaires](#4-tests-unitaires)
5. [Tests Feature](#5-tests-feature)
6. [Tests Browser](#6-tests-browser)
7. [Cas métier critiques à couvrir obligatoirement](#7-cas-métier-critiques-à-couvrir-obligatoirement)
8. [Couverture de code](#8-couverture-de-code)
9. [Données de test](#9-données-de-test)
10. [Intégration continue](#10-intégration-continue)

---

## 1. Portée du document

Ce document définit la stratégie de tests de LOWLY. Le détail des conventions d'écriture (nommage, structure des fichiers Pest, helpers de test) est dans `docs/engineering/12-testing-guidelines.md`.

## 2. Pyramide de tests

```
                    ▲
                   ╱ ╲
                  ╱   ╲        Tests Browser (peu nombreux)
                 ╱─────╲       Parcours critiques de bout en bout
                ╱       ╲
               ╱         ╲     Tests Feature (nombreux)
              ╱───────────╲    Comportement HTTP + base de données
             ╱             ╲
            ╱               ╲  Tests Unitaires (très nombreux)
           ╱─────────────────╲ Logique métier isolée (Actions, Services)
```

La majorité de la couverture doit provenir des tests unitaires et Feature. Les tests Browser sont réservés aux parcours utilisateurs les plus critiques, car plus lents et plus coûteux à maintenir.

## 3. Outillage

| Outil | Usage |
|---|---|
| **Pest** | Framework de test principal (syntaxe préférée) |
| **PHPUnit** | Sous-jacent à Pest, utilisable directement si nécessaire |
| **Laravel Dusk** (ou équivalent) | Tests Browser |
| **Factories Eloquent** | Génération de données de test cohérentes |
| **Mocking Laravel** | Isolation des dépendances externes (notifications, stockage) |

## 4. Tests unitaires

Ciblent la logique métier isolée, en particulier :

- les `Actions` du domaine `Reservation` (ex : `ConfirmerReservation`, `CreerContreProposition`) ;
- les calculs du domaine `BUSINESS_RULES.md` (nombre de journées, montant total) ;
- les `Services` transverses (ex : calcul de disponibilité).

Exemple de cas à tester unitairement (calcul des journées) :

```
Étant donné une arrivée le 10 janvier et un départ le 13 janvier
Quand je calcule le nombre de journées facturées
Alors le résultat doit être 3
```

## 5. Tests Feature

Ciblent le comportement HTTP complet (requête → contrôleur → base de données → réponse), avec une base de données de test réelle (PostgreSQL, transaction annulée après chaque test).

Exemples de scénarios Feature :

- un client authentifié peut soumettre une demande de réservation sur un bien disponible ;
- un client ne peut pas soumettre une demande sur un bien dont le calendrier est déjà bloqué sur la période demandée ;
- un partenaire peut accepter une demande, ce qui bloque automatiquement le calendrier ;
- un partenaire ne peut pas accepter une demande sur un bien qui ne lui appartient pas (`403 Forbidden`) ;
- un administrateur peut valider un partenaire en attente ;
- un client non authentifié ne peut pas accéder à son historique de réservations (`401 Unauthorized`).

## 6. Tests Browser

Réservés aux parcours de bout en bout les plus critiques pour l'activité :

- parcours complet client : recherche → détail annonce → demande de réservation → confirmation ;
- parcours complet partenaire : réception d'une demande → refus → contre-proposition → acceptation client.

## 7. Cas métier critiques à couvrir obligatoirement

Cette liste est directement dérivée de [`BUSINESS_RULES.md`](./BUSINESS_RULES.md) et doit être intégralement couverte avant toute mise en production :

| Règle métier | Test requis |
|---|---|
| Calcul de journée 12h-12h | Vérifier l'exemple de référence (10→13 janvier = 3 journées) et des cas limites (1 nuit, longue durée) |
| Blocage automatique à la confirmation | Vérifier qu'une réservation confirmée bloque exactement la période attendue, ni plus ni moins |
| Non-chevauchement de réservations | Vérifier qu'une deuxième demande sur une période chevauchante est rejetée |
| Contre-proposition | Vérifier acceptation, refus, et expiration après délai |
| Blocages manuels véhicules | Vérifier qu'un blocage entretien/maintenance/usage personnel empêche toute demande sur la période |
| Refus sans alternative | Vérifier la clôture définitive du cycle de la demande |
| Validation partenaire | Vérifier qu'un partenaire non validé ne peut pas publier d'annonce |
| Validation annonce | Vérifier qu'une annonce non validée n'apparaît pas dans la recherche publique |

## 8. Couverture de code

- Un seuil minimal de couverture est défini en CI (seuil exact précisé dans `docs/engineering/12-testing-guidelines.md`), avec un focus qualitatif prioritaire sur les domaines `Reservation` et `Availability`, considérés comme critiques.
- La couverture quantitative ne remplace jamais la couverture qualitative des cas métier listés en §7.

## 9. Données de test

- Les tests utilisent exclusivement des données générées par des `Factories`, jamais de données de production copiées.
- Chaque test Feature doit être indépendant et rejouable (transaction de base de données annulée après exécution).

## 10. Intégration continue

- Toute Pull Request déclenche automatiquement : lint (Pint), analyse statique (PHPStan/Larastan), suite de tests complète.
- Aucune Pull Request ne peut être fusionnée si la suite de tests échoue ou si la couverture des cas critiques (§7) régresse.
- Détails du pipeline exact dans `docs/engineering/15-deployment.md` et `docs/engineering/13-git-workflow.md`.
