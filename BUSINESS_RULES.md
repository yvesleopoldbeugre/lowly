# BUSINESS_RULES.md — Règles Métier LOWLY

## Table des matières

1. [Portée du document](#1-portée-du-document)
2. [Principe général du cycle de réservation](#2-principe-général-du-cycle-de-réservation)
3. [Règles métier — Résidences](#3-règles-métier--résidences)
4. [Règles métier — Véhicules](#4-règles-métier--véhicules)
5. [Cycle de réservation détaillé](#5-cycle-de-réservation-détaillé)
6. [Contre-propositions](#6-contre-propositions)
7. [Blocages de calendrier](#7-blocages-de-calendrier)
8. [États d'une réservation](#8-états-dune-réservation)
9. [Règles de tarification](#9-règles-de-tarification)
10. [Cas limites et litiges](#10-cas-limites-et-litiges)
11. [Glossaire métier rapide](#11-glossaire-métier-rapide)

---

## 1. Portée du document

Ce document est la **référence unique et opposable** pour toutes les règles métier de LOWLY. En cas de doute ou de contradiction entre le code, une spécification technique et ce document, **ce document prévaut**. Toute évolution de règle métier doit être d'abord actée ici avant d'être implémentée.

Les règles métier couvrent deux domaines d'offres au MVP : **résidences meublées** et **véhicules de location**. Les principes sont volontairement alignés entre les deux catégories pour faciliter l'extension future à d'autres types d'offres (hôtels, villas, salles, bureaux, excursions, chauffeurs, etc.).

## 2. Principe général du cycle de réservation

Toute réservation, quelle que soit la catégorie de bien, suit le même cycle :

```
        CLIENT                                    PARTENAIRE
          │                                            │
          │ 1. Demande de réservation                  │
          ├───────────────────────────────────────────►│
          │                                             │
          │                              2. Examen      │
          │                                             │
          │        3a. Acceptation                      │
          │◄───────────────────────────────────────────┤
          │        → Réservation CONFIRMÉE               │
          │        → Calendrier bloqué automatiquement   │
          │                                             │
          │        3b. Refus simple                     │
          │◄───────────────────────────────────────────┤
          │        → Demande CLOSE                       │
          │                                             │
          │        3c. Refus + contre-proposition        │
          │◄───────────────────────────────────────────┤
          │ 4. Acceptation ou refus de la proposition    │
          ├───────────────────────────────────────────►│
```

Ce cycle s'applique **à l'identique** pour les résidences et les véhicules. Seules les modalités de calcul de la durée et les types de blocages diffèrent.

## 3. Règles métier — Résidences

### 3.1 Principe de la journée résidence

Une réservation de résidence est **facturée à la journée**. Une journée correspond à un cycle fixe de **12h00 à 12h00** le lendemain :

```
   Jour J                          Jour J+1
   ────────────────────────────────────────────►
      12h00                          12h00
        │◄──────── 1 journée ────────►│
```

L'heure de référence de 12h00 est une convention métier fixe de la plateforme, appliquée uniformément à toutes les résidences, indépendamment de l'heure réelle d'arrivée ou de départ négociée localement entre le client et le partenaire.

### 3.2 Règle de calcul du nombre de journées facturées

Le nombre de journées facturées est égal au **nombre de nuits comprises entre la date d'arrivée et la date de départ**, c'est-à-dire :

```
nombre_de_journées = date_départ − date_arrivée   (en jours calendaires)
```

### 3.3 Exemple de référence

```
Arrivée  : 10 janvier
Départ   : 13 janvier

Journées facturées : 10, 11, 12  →  3 journées
```

Détail du raisonnement :

```
10 janvier 12h00 ───► 11 janvier 12h00   = journée du 10
11 janvier 12h00 ───► 12 janvier 12h00   = journée du 11
12 janvier 12h00 ───► 13 janvier 12h00   = journée du 12
                                            ─────────────
                                            3 journées
```

Le jour de départ (13 janvier) **n'est jamais facturé** comme une journée pleine : il marque la fin du dernier cycle de 12h00 démarré le 12 janvier.

### 3.4 Règle de blocage calendrier automatique

Dès qu'une réservation résidence passe à l'état **CONFIRMÉE**, le système bloque **automatiquement** toutes les journées comprises entre la date d'arrivée (incluse) et la date de départ (exclue) dans le calendrier de disponibilité du bien concerné. Aucune autre demande ne peut être acceptée sur une période chevauchant un blocage existant.

### 3.5 Refus et proposition alternative

Si le partenaire refuse une demande de résidence, il a la possibilité de proposer **un autre bien de son portefeuille**, disponible sur les mêmes dates ou des dates ajustées. Le client peut :

- **accepter** la proposition → la réservation est confirmée sur le nouveau bien et les nouvelles dates éventuelles, et le calendrier du bien alternatif est bloqué ;
- **refuser** la proposition → la demande initiale est définitivement close.

Il n'y a **jamais** de confirmation automatique sans validation explicite du client sur une proposition alternative.

## 4. Règles métier — Véhicules

### 4.1 Principe de la journée véhicule

Le même principe de journée à 12h00-12h00 s'applique aux véhicules de location. Le calcul du nombre de journées facturées suit exactement la même règle que pour les résidences (voir §3.2 et §3.3).

### 4.2 Blocages spécifiques aux véhicules

En complément des blocages générés automatiquement par les réservations confirmées, le partenaire véhicule peut créer des **blocages manuels** sur son calendrier, pour les motifs suivants :

| Motif de blocage | Description |
|---|---|
| Entretien | Révision, réparation planifiée |
| Maintenance | Immobilisation technique non planifiée |
| Utilisation personnelle | Le partenaire utilise lui-même le véhicule |

Ces blocages manuels ont la **même priorité** qu'un blocage généré par une réservation confirmée : aucune demande ne peut être acceptée sur une période bloquée manuellement.

### 4.3 Refus et proposition alternative

Le même mécanisme de contre-proposition que pour les résidences s'applique aux véhicules : en cas de refus, le partenaire peut proposer un autre véhicule disponible de son portefeuille, que le client accepte ou refuse.

## 5. Cycle de réservation détaillé

### 5.1 Étape 1 — Demande

Le client sélectionne une annonce (résidence ou véhicule), choisit une période (date d'arrivée / date de départ), et soumet une **demande de réservation**. À ce stade, aucun blocage calendrier n'est créé : la demande est à l'état `EN_ATTENTE`.

### 5.2 Étape 2 — Examen par le partenaire

Le partenaire consulte la demande et dispose de trois options :

1. **Accepter** → passage direct à l'état `CONFIRMÉE`.
2. **Refuser sans alternative** → passage à l'état `REFUSÉE`, cycle clos.
3. **Refuser avec alternative** → passage à l'état `CONTRE-PROPOSÉE`, en attente de réponse du client.

### 5.3 Étape 3 — Confirmation

Dès qu'une réservation atteint l'état `CONFIRMÉE` (directement ou après acceptation d'une contre-proposition), le système :

- bloque automatiquement le calendrier du bien concerné pour la période exacte de la réservation ;
- notifie le client et le partenaire ;
- ajoute la réservation à l'historique des deux parties.

## 6. Contre-propositions

### 6.1 Définition

Une contre-proposition est une **alternative** soumise par le partenaire après refus d'une demande initiale. Elle porte sur :

- un autre bien du portefeuille du partenaire (résidence ou véhicule) ;
- éventuellement, des dates ajustées si nécessaire.

### 6.2 Règles de traitement

- Une contre-proposition ne peut porter que sur un bien **disponible** sur la période proposée (vérification automatique du calendrier avant soumission de la proposition).
- Le client dispose d'un délai (à définir en configuration plateforme) pour répondre à une contre-proposition. En l'absence de réponse dans ce délai, la contre-proposition expire et la demande passe à l'état `EXPIRÉE`.
- Une contre-proposition acceptée déclenche la confirmation immédiate et le blocage automatique du calendrier, comme pour une acceptation directe.
- Une contre-proposition refusée clôt définitivement le cycle de la demande initiale ; le client doit soumettre une nouvelle demande s'il souhaite poursuivre sa recherche.
- Un partenaire ne peut proposer **qu'une seule** contre-proposition par demande refusée (pas de négociation itérative dans le MVP).

## 7. Blocages de calendrier

### 7.1 Types de blocages

| Type | Origine | Applicable à |
|---|---|---|
| Blocage automatique | Réservation confirmée | Résidences et véhicules |
| Blocage manuel — entretien | Action du partenaire | Véhicules |
| Blocage manuel — maintenance | Action du partenaire | Véhicules |
| Blocage manuel — usage personnel | Action du partenaire | Véhicules |
| Blocage manuel — indisponibilité résidence | Action du partenaire (ex : travaux) | Résidences |

### 7.2 Règle d'exclusivité

Sur une période donnée, un bien ne peut avoir **qu'un seul** blocage actif à la fois. Le système doit garantir qu'aucune période ne puisse être doublement réservée ou bloquée en contradiction — cette garantie est une contrainte d'intégrité de premier ordre (voir [`DATABASE.md`](./DATABASE.md) pour la modélisation technique de cette contrainte).

### 7.3 Libération d'un blocage

Un blocage automatique lié à une réservation n'est libéré que si la réservation est **annulée** selon les règles d'annulation en vigueur (à définir précisément en configuration plateforme — délais, conditions). Un blocage manuel est levé explicitement par le partenaire.

## 8. États d'une réservation

```
   EN_ATTENTE
       │
       ├──► CONFIRMÉE ─────────────► (calendrier bloqué)
       │
       ├──► REFUSÉE (cycle clos)
       │
       └──► CONTRE-PROPOSÉE
                │
                ├──► CONFIRMÉE (sur le bien alternatif) ─────► (calendrier bloqué)
                │
                ├──► REFUSÉE (cycle clos)
                │
                └──► EXPIRÉE (cycle clos, délai dépassé)
```

| État | Description |
|---|---|
| `EN_ATTENTE` | Demande soumise, en attente d'examen du partenaire |
| `CONFIRMÉE` | Réservation validée, calendrier bloqué |
| `REFUSÉE` | Demande refusée sans suite |
| `CONTRE-PROPOSÉE` | Alternative soumise, en attente de réponse du client |
| `EXPIRÉE` | Contre-proposition non traitée dans le délai |

## 9. Règles de tarification

- Chaque bien (résidence ou véhicule) dispose d'un **tarif journalier de référence**, défini par le partenaire.
- Le partenaire peut définir des **tarifs différenciés par période** (haute saison, basse saison, événements) — la structure exacte de gestion des périodes tarifaires est détaillée dans [`DATABASE.md`](./DATABASE.md).
- Le montant total d'une réservation est calculé comme :

```
montant_total = Σ (tarif_journalier_applicable × 1) pour chaque journée facturée
```

- Le tarif affiché au client lors de la demande doit être le tarif **définitif et final** — aucun frais caché n'est ajouté après confirmation, conformément au principe de transparence défini dans [`PRODUCT.md`](./PRODUCT.md).

## 10. Cas limites et litiges

| Cas | Règle applicable |
|---|---|
| Arrivée et départ le même jour calendaire | Non autorisé — une réservation doit couvrir au moins une journée complète (12h-12h) |
| Deux demandes simultanées sur la même période et le même bien | Seule la première acceptée par le partenaire est confirmée ; les autres doivent être automatiquement refusées ou re-routées si aucune décision n'a encore été prise |
| Le partenaire ne répond pas à une demande | Un délai maximal de réponse doit être configuré ; au-delà, la demande expire automatiquement (`EXPIRÉE`) |
| Une contre-proposition porte sur un bien qui devient indisponible avant la réponse du client | La contre-proposition est automatiquement invalidée et le client en est notifié |
| Modification de tarif par le partenaire après une demande en attente | Le tarif affiché au moment de la demande est contractuel et ne peut être modifié rétroactivement sur une demande en cours |

## 11. Glossaire métier rapide

Un glossaire métier complet est disponible dans `docs/engineering/glossary.md`. Termes essentiels à ce document :

| Terme | Définition |
|---|---|
| **Journée** | Unité de facturation de 12h00 à 12h00 le lendemain |
| **Demande** | Requête de réservation soumise par un client, non encore validée |
| **Confirmation** | Validation finale d'une réservation, entraînant le blocage automatique du calendrier |
| **Contre-proposition** | Alternative soumise par le partenaire après un refus |
| **Blocage** | Période d'indisponibilité d'un bien, automatique ou manuelle |
