# PRODUCT.md — Vision Produit LOWLY

## Table des matières

1. [Introduction](#1-introduction)
2. [Positionnement](#2-positionnement)
3. [Ce que LOWLY n'est pas](#3-ce-que-lowly-nest-pas)
4. [Le modèle de mise en relation](#4-le-modèle-de-mise-en-relation)
5. [Les catégories d'offres](#5-les-catégories-doffres)
6. [Les acteurs de la plateforme](#6-les-acteurs-de-la-plateforme)
7. [Personas](#7-personas)
8. [Parcours utilisateurs](#8-parcours-utilisateurs)
9. [Périmètre du MVP](#9-périmètre-du-mvp)
10. [Hors périmètre du MVP](#10-hors-périmètre-du-mvp)
11. [Indicateurs de succès](#11-indicateurs-de-succès)
12. [Vision d'extension](#12-vision-dextension)
13. [Principes produit directeurs](#13-principes-produit-directeurs)

---

## 1. Introduction

LOWLY est une plateforme web de type marketplace qui connecte des **clients** en recherche d'hébergement meublé ou de véhicule de location avec des **partenaires** qui proposent ces biens. La plateforme ne possède, ne gère et n'exploite aucun bien elle-même : elle fournit l'infrastructure technique, la confiance (validation, avis, paiement sécurisé le cas échéant) et le flux transactionnel qui permettent à l'offre et à la demande de se rencontrer.

Ce document définit la vision produit, le positionnement, les personas, les parcours utilisateurs et le périmètre exact du MVP (Minimum Viable Product). Il sert de référence à toute décision de conception ou de développement : toute fonctionnalité envisagée doit être confrontée à ce document avant d'être ajoutée au périmètre.

## 2. Positionnement

LOWLY se positionne comme une **marketplace de mise en relation**, sur le modèle suivant :

```
   CLIENT                    PARTENAIRE
      │                           │
      │        Recherche         │
      ├──────────────────────────►
      │                           │
      │         Demande           │
      ├──────────────────────────►
      │                           │
      │        Validation         │
      ◄──────────────────────────┤
      │                           │
      │       Confirmation        │
      ◄──────────────────────────►
```

La plateforme n'intervient jamais en tant qu'exploitant : elle **facilite**, **valide**, **sécurise** et **trace** les échanges entre les deux parties. Ce positionnement conditionne toutes les décisions produit ultérieures — notamment le fait que chaque réservation nécessite une validation explicite du partenaire (contrairement à une réservation instantanée type Booking).

## 3. Ce que LOWLY n'est pas

Pour éviter toute dérive de périmètre, il est essentiel de rappeler ce que LOWLY ne cherche pas à devenir :

- **Pas un Booking / Airbnb** : LOWLY ne vise pas l'instantanéité de réservation ni le volume massif d'inventaire international ; le modèle repose sur une validation humaine du partenaire.
- **Pas un PMS (Property Management System)** : LOWLY ne gère pas l'exploitation opérationnelle interne du partenaire (ménage, maintenance détaillée, comptabilité complète, etc.). LOWLY fournit uniquement les outils nécessaires à la mise en relation (disponibilités, tarifs, réservations).
- **Pas une agence immobilière** : LOWLY ne s'occupe pas de gestion locative longue durée, de mandats de gestion, ni de transactions immobilières.
- **Pas une agence de location de véhicules** : LOWLY ne possède pas de flotte propre et n'assure pas la logistique physique de remise des clés (ceci reste entre le client et le partenaire).

## 4. Le modèle de mise en relation

Le cœur fonctionnel de LOWLY repose sur un cycle en quatre étapes, identique conceptuellement pour les résidences et les véhicules :

1. **Demande** — le client sélectionne une annonce et soumet une demande de réservation pour une période donnée.
2. **Validation** — le partenaire examine la demande et l'accepte ou la refuse. En cas de refus, il peut proposer une alternative (autre bien, autres dates).
3. **Confirmation** — si le partenaire accepte (directement ou via une contre-proposition acceptée par le client), la réservation est confirmée et bloque automatiquement le calendrier du bien concerné.
4. **Réalisation** — la prestation se déroule ; à l'issue, l'historique de réservation est conservé côté client et partenaire.

Les règles précises de ce cycle (délais, contre-propositions, blocages calendrier) sont détaillées dans [`BUSINESS_RULES.md`](./BUSINESS_RULES.md).

## 5. Les catégories d'offres

### 5.1 Périmètre MVP

| Catégorie | Description |
|---|---|
| **Résidences meublées** | Logements complets ou partiels, meublés et équipés, proposés à la nuitée/journée |
| **Véhicules de location** | Véhicules particuliers proposés à la journée par des partenaires |

### 5.2 Périmètre futur (post-MVP)

| Catégorie | Statut |
|---|---|
| Hôtels | Envisagé |
| Villas | Envisagé |
| Appartements (distinct des résidences meublées génériques) | Envisagé |
| Salles (événementiel, réunion) | Envisagé |
| Bureaux (espaces de travail) | Envisagé |
| Excursions | Envisagé |
| Chauffeurs | Envisagé |
| Autres services à la demande | Envisagé |

Le système est conçu dès le MVP pour que l'ajout d'une nouvelle catégorie d'offre ne nécessite pas de refonte du cœur applicatif — voir le principe d'extensibilité du catalogue dans [`ARCHITECTURE.md`](./ARCHITECTURE.md).

## 6. Les acteurs de la plateforme

### 6.1 Visiteur (Public)

Utilisateur non authentifié. Peut consulter le catalogue, effectuer des recherches, voir le détail d'une annonce, et créer un compte.

### 6.2 Client

Utilisateur authentifié cherchant à réserver une résidence ou un véhicule. Peut rechercher, réserver, consulter son historique, gérer son profil et recevoir des notifications.

### 6.3 Partenaire

Utilisateur authentifié, validé par l'administration, proposant une ou plusieurs résidences et/ou véhicules. Dispose d'un tableau de bord pour gérer ses biens, ses disponibilités, ses tarifs, ses photos et ses réservations.

### 6.4 Administrateur

Utilisateur interne à LOWLY. Valide les partenaires et les annonces, gère les utilisateurs, supervise les statistiques globales et configure les paramètres de la plateforme.

## 7. Personas

### 7.1 Amara — la Cliente

Amara, 34 ans, cadre en déplacement professionnel régulier. Elle cherche un logement meublé pour deux semaines dans une ville qu'elle ne connaît pas. Elle valorise la clarté des photos, la transparence du tarif total (pas de frais cachés) et la réactivité du partenaire. Elle consulte le statut de sa demande depuis son téléphone et attend une notification dès qu'une réponse est donnée.

**Besoins clés** : recherche filtrée fiable, détail d'annonce complet, suivi de statut de réservation, historique de ses séjours passés.

### 7.2 Karim — le Partenaire résidences

Karim possède trois résidences meublées qu'il loue en complément de son activité principale. Il n'a pas le temps de répondre à chaque demande dans la minute mais souhaite garder le contrôle total sur qui séjourne chez lui et à quelles conditions. Il veut éviter les doubles réservations et pouvoir proposer une résidence alternative si la première n'est plus disponible.

**Besoins clés** : tableau de bord centralisé, gestion des disponibilités sans risque d'erreur, possibilité de proposer une alternative, historique clair des demandes.

### 7.3 Fatou — la Partenaire véhicules

Fatou loue trois véhicules. Elle a besoin de bloquer certaines périodes pour l'entretien ou pour son usage personnel, en dehors du cycle de réservation classique. Elle veut aussi ajuster ses tarifs selon la saison.

**Besoins clés** : gestion fine du calendrier (blocages manuels), gestion des tarifs, visibilité sur les réservations à venir.

### 7.4 Yves — l'Administrateur

Yves valide les nouveaux partenaires et leurs annonces avant publication, pour garantir la qualité et la conformité de l'offre. Il doit pouvoir repérer rapidement les comptes ou annonces à risque et consulter des statistiques d'usage globales.

**Besoins clés** : file de validation claire, outils de modération, tableau de bord statistique.

## 8. Parcours utilisateurs

### 8.1 Parcours Client — Réservation d'une résidence

```
1. Le client recherche une résidence (destination, dates, nombre de personnes)
2. Il consulte la liste de résultats filtrés et disponibles
3. Il ouvre le détail d'une annonce (photos, description, tarif, avis)
4. Il soumet une demande de réservation pour ses dates
5. Il reçoit une notification de statut :
     a. Acceptée → réservation confirmée, calendrier bloqué
     b. Refusée avec contre-proposition → il accepte ou refuse la proposition
     c. Refusée sans contre-proposition → la demande est close
6. Il consulte sa réservation confirmée dans son historique
```

### 8.2 Parcours Partenaire — Traitement d'une demande

```
1. Le partenaire reçoit une notification de nouvelle demande
2. Il consulte le détail de la demande (dates, client, bien concerné)
3. Il décide :
     a. Accepter → la réservation est confirmée automatiquement
     b. Refuser → il peut proposer un autre bien disponible
     c. Refuser sans alternative → la demande est close
4. Si contre-proposition, il attend la réponse du client
5. La réservation confirmée apparaît dans son calendrier et son tableau de bord
```

### 8.3 Parcours Administrateur — Validation d'un partenaire

```
1. Un nouveau partenaire s'inscrit et soumet ses informations
2. L'administrateur consulte la demande dans la file de validation
3. Il vérifie les informations et documents fournis
4. Il valide ou rejette le compte partenaire
5. Le partenaire validé peut ensuite soumettre des annonces
6. Chaque annonce soumise suit également un cycle de validation avant publication
```

## 9. Périmètre du MVP

Le MVP couvre strictement les fonctionnalités suivantes, organisées par acteur.

### 9.1 Public

- Consultation des annonces (résidences et véhicules)
- Recherche (par destination/localisation, dates, type de bien, filtres de base)
- Détail d'une annonce (photos, description, tarifs, disponibilité, avis le cas échéant)
- Création de compte
- Connexion

### 9.2 Client

- Recherche
- Réservation (demande, suivi, contre-proposition)
- Historique des réservations
- Profil (informations personnelles, préférences)
- Notifications (statut des demandes, confirmations, messages du partenaire)

### 9.3 Partenaire

- Tableau de bord (vue synthétique de l'activité)
- Gestion des résidences (création, édition, publication)
- Gestion des véhicules (création, édition, publication)
- Gestion des disponibilités (calendrier, blocages manuels)
- Gestion des réservations (acceptation, refus, contre-proposition)
- Gestion des tarifs (par bien, éventuellement par période)
- Gestion des photos (upload, réorganisation, suppression)

### 9.4 Administration

- Validation des partenaires (inscription, vérification, activation)
- Validation des annonces (contrôle qualité avant publication)
- Gestion des utilisateurs (clients, partenaires, administrateurs)
- Statistiques (usage, volumes de réservation, taux de conversion)
- Paramètres (configuration générale de la plateforme)

## 10. Hors périmètre du MVP

Explicitement exclus de la première version, à réévaluer dans les phases suivantes (voir [`ROADMAP.md`](./ROADMAP.md)) :

- Paiement en ligne intégré (le MVP peut fonctionner avec un règlement géré hors plateforme ou une intégration ultérieure)
- Système d'avis et de notation structuré
- Messagerie instantanée temps réel entre client et partenaire
- Application mobile native
- Nouvelles catégories d'offres (hôtels, villas, excursions, chauffeurs, etc.)
- Programme de fidélité ou de parrainage
- Multilingue / multi-devises
- Facturation automatisée et comptabilité partenaire

## 11. Indicateurs de succès

| Indicateur | Objectif |
|---|---|
| Taux d'acceptation des demandes | Mesurer la pertinence de l'offre par rapport à la demande |
| Délai moyen de réponse partenaire | Mesurer la réactivité et l'expérience client |
| Taux de conversion recherche → demande | Mesurer l'efficacité de la recherche et des fiches annonces |
| Taux de contre-proposition acceptée | Mesurer la valeur du mécanisme d'alternative |
| Nombre de partenaires actifs validés | Mesurer la croissance de l'offre |
| Taux de réservations confirmées sans erreur de calendrier | Mesurer la fiabilité du système de disponibilité |

## 12. Vision d'extension

Au-delà du MVP, LOWLY est pensé pour s'étendre progressivement à de nouvelles catégories d'offres sans remettre en cause son architecture. Chaque nouvelle catégorie (hôtel, villa, salle, bureau, excursion, chauffeur, autre service) doit pouvoir être ajoutée comme une nouvelle déclinaison du domaine `Catalogue`, en réutilisant les domaines `Availability`, `Reservation`, `Communication` et `Administration` déjà existants.

Cette vision d'extensibilité est un critère de conception permanent : toute décision technique doit être évaluée à l'aune de sa capacité à supporter cette croissance du catalogue sans réécriture majeure.

## 13. Principes produit directeurs

1. **La confiance avant la vitesse** — la validation humaine du partenaire prime sur l'instantanéité de la réservation.
2. **La transparence pour le client** — tarifs, disponibilités et statuts de demande doivent toujours être clairs et à jour.
3. **Le contrôle pour le partenaire** — le partenaire garde la décision finale sur chaque réservation ; la plateforme ne réserve jamais en son nom sans validation.
4. **L'extensibilité par conception** — chaque choix de modélisation doit anticiper l'arrivée de nouvelles catégories d'offres.
5. **La simplicité du MVP** — chaque fonctionnalité ajoutée au périmètre initial doit être justifiée par un besoin direct des personas identifiés dans ce document.
