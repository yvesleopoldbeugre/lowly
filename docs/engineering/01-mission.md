# 01 — Mission, Vision, Valeurs

## Table des matières

1. [Mission](#1-mission)
2. [Vision](#2-vision)
3. [Valeurs](#3-valeurs)
4. [Objectifs](#4-objectifs)
5. [Positionnement](#5-positionnement)
6. [Ce que la mission implique au quotidien](#6-ce-que-la-mission-implique-au-quotidien)

---

## 1. Mission

La mission de LOWLY est de **connecter, en toute confiance, des clients à la recherche d'un hébergement meublé ou d'un véhicule de location avec des partenaires qui proposent ces biens** — sans jamais se substituer à eux dans la gestion de leur activité.

Concrètement, LOWLY existe pour :

- réduire la friction entre un client qui cherche un bien disponible et fiable et un partenaire qui souhaite le louer ;
- donner au partenaire les outils numériques pour gérer son offre (disponibilités, tarifs, réservations) sans complexité technique ;
- garantir, par un processus de validation humaine à chaque étape, qu'aucune réservation n'échappe au contrôle du partenaire.

## 2. Vision

À terme, LOWLY vise à devenir la plateforme de référence de mise en relation pour l'hébergement meublé et la location de véhicules dans ses marchés cibles, puis à étendre progressivement ce même modèle de confiance à d'autres catégories de services réservables : hôtels, villas, appartements, salles, bureaux, excursions, chauffeurs et autres services à la demande (voir [`ROADMAP.md`](../../ROADMAP.md)).

La vision de LOWLY n'est pas la croissance à tout prix du volume de transactions, mais la construction d'une plateforme où **chaque partie — client comme partenaire — garde le contrôle et la confiance** dans le processus de réservation.

## 3. Valeurs

| Valeur | Ce qu'elle signifie concrètement |
|---|---|
| **Confiance** | Chaque partenaire et chaque annonce sont validés avant publication ; chaque réservation est validée par le partenaire avant confirmation |
| **Transparence** | Les tarifs affichés au client sont définitifs ; aucun frais caché après confirmation |
| **Contrôle** | Le partenaire garde toujours la décision finale sur chaque réservation |
| **Simplicité** | Le produit ne fait que ce qui est nécessaire pour connecter l'offre et la demande, rien de plus |
| **Extensibilité** | Chaque décision de conception anticipe l'ajout futur de nouvelles catégories d'offres |

## 4. Objectifs

Les objectifs du produit, dans l'ordre de priorité pour la phase MVP :

1. Permettre à un client de trouver et réserver une résidence ou un véhicule disponible, sans ambiguïté sur le tarif ou les conditions.
2. Permettre à un partenaire de gérer son offre (biens, disponibilités, tarifs, photos) et de traiter les demandes de réservation avec un minimum d'effort.
3. Permettre à l'administration de garantir la qualité de l'offre (validation des partenaires et des annonces) et de superviser l'activité globale.
4. Poser une architecture qui ne nécessite pas de refonte pour accueillir de nouvelles catégories d'offres.

Les indicateurs de succès associés à ces objectifs sont détaillés dans [`PRODUCT.md`](../../PRODUCT.md) §11.

## 5. Positionnement

LOWLY est une **marketplace de mise en relation**, pas un exploitant. Ce positionnement est un principe fondateur, non négociable :

```
Client  →  Recherche  →  Demande  →  Validation  →  Confirmation  →  Partenaire
```

LOWLY n'est :

- ni un Booking ou un Airbnb (pas de réservation instantanée sans validation humaine) ;
- ni un PMS — Property Management System (pas de gestion opérationnelle interne du partenaire) ;
- ni une agence immobilière (pas de gestion locative longue durée ni de mandat) ;
- ni une agence de location de véhicules (pas de flotte propre, pas de logistique physique assurée par LOWLY).

Détail complet du positionnement : [`PRODUCT.md`](../../PRODUCT.md) §2-3.

## 6. Ce que la mission implique au quotidien

Pour toute personne contribuant au code ou à la conception de LOWLY, cette mission se traduit par des règles concrètes :

- ne jamais implémenter de réservation instantanée sans étape de validation partenaire, même si cela semblait améliorer l'expérience client à court terme ;
- ne jamais ajouter de fonctionnalité qui rapprocherait LOWLY d'un rôle d'exploitant (par exemple, gérer directement l'état des lieux ou l'entretien d'un bien) ;
- toujours vérifier qu'une nouvelle fonctionnalité de catalogue peut, en principe, être généralisée aux futures catégories d'offres (voir [`ARCHITECTURE.md`](../../ARCHITECTURE.md) §13) ;
- toujours privilégier la clarté et la transparence de l'information présentée au client (tarif, disponibilité, statut) sur toute autre considération d'esthétique ou de vitesse de développement.
