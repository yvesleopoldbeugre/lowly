# UX_UI.md — Maquettes UX/UI LOWLY

## Table des matières

1. [Objectif de ce document](#1-objectif-de-ce-document)
2. [Design system](#2-design-system)
3. [Plan du site (sitemap)](#3-plan-du-site-sitemap)
4. [Écrans — Public](#4-écrans--public)
5. [Écrans — Client](#5-écrans--client)
6. [Écrans — Partenaire](#6-écrans--partenaire)
7. [Écrans — Administration](#7-écrans--administration)
8. [Maquettes HTML/Tailwind haute-fidélité](#8-maquettes-htmltailwind-haute-fidélité)
9. [Traçabilité avec la documentation existante](#9-traçabilité-avec-la-documentation-existante)

---

## 1. Objectif de ce document

`UX_UI.md` matérialise la dernière étape de conception avant développement (Besoin → Analyse métier → Diagrammes UML → Base de données → Architecture → API → **UX/UI** → Développement → Tests → Validation, voir [`ENGINEERING.md`](./ENGINEERING.md) §2).

Il couvre l'intégralité des écrans du périmètre MVP défini dans [`PRODUCT.md`](./PRODUCT.md) §9, sous deux formes :

- des **wireframes basse fidélité** (ASCII), pour l'ensemble des écrans, qui fixent la structure et le contenu de chaque page sans détail visuel ;
- des **maquettes haute-fidélité HTML/Tailwind cliquables**, pour les écrans les plus critiques du parcours (voir §8), directement transposables en composants Blade lors du développement.

Aucune règle métier n'est introduite ici : le contenu, les statuts et les libellés affichés doivent rester strictement conformes à [`BUSINESS_RULES.md`](./BUSINESS_RULES.md) et [`PRODUCT.md`](./PRODUCT.md).

## 2. Design system

Le design system complet (tokens de couleur, typographie, composants Blade, conventions Tailwind, accessibilité) est défini dans [`docs/engineering/06-blade-tailwind-guidelines.md`](./docs/engineering/06-blade-tailwind-guidelines.md) et n'est pas dupliqué ici. Rappel des tokens de couleur utilisés dans toutes les maquettes de ce document :

| Token | Usage |
|---|---|
| `primary` | Actions principales (réserver, confirmer, publier) |
| `secondary` | Actions secondaires (annuler, retour) |
| `success` | États positifs (`CONFIRMÉE`) |
| `warning` | États intermédiaires (`EN_ATTENTE`, `CONTRE-PROPOSÉE`) |
| `danger` | États négatifs (`REFUSÉE`, `EXPIRÉE`) |
| `neutral-*` | Textes, fonds, bordures |

Les maquettes haute-fidélité (§8) définissent ces tokens via un `tailwind.config` local (CDN Play) reproduisant à l'identique la palette destinée à `tailwind.config.js` en production.

## 3. Plan du site (sitemap)

```
                                   Visiteur (non connecté)
                                            │
                        ┌───────────────────┼───────────────────┐
                        ▼                   ▼                   ▼
                    Accueil            Recherche           Connexion /
                 (annonces mises   (résultats filtrés)      Inscription
                    en avant)             │                      │
                        │                 ▼                      │
                        └──────► Détail d'une annonce             │
                                          │                       │
                                          ▼                       ▼
                                  Demande de réservation ──► authentification requise
                                          │
                    ┌─────────────────────┼─────────────────────┐
                    ▼                     ▼                     ▼
              Espace CLIENT         Espace PARTENAIRE      Espace ADMIN
                    │                     │                     │
        ┌───────────┼───────────┐ ┌───────┼─────────────┐ ┌─────┼─────────────┐
        ▼           ▼           ▼ ▼       ▼             ▼ ▼     ▼             ▼
   Historique   Suivi de    Profil / Tableau  Gestion biens/  Validation  Gestion   Statistiques
   réservations réservation Notif.  de bord   dispo/tarifs/   partenaires utilisateurs /Paramètres
                                              photos/résas     & annonces
```

## 4. Écrans — Public

### 4.1 Accueil / consultation des annonces

```
┌──────────────────────────────────────────────────────────────┐
│ LOGO LOWLY        Résidences  Véhicules        [Connexion] [S'inscrire] │
├──────────────────────────────────────────────────────────────┤
│  Barre de recherche : [Destination] [Dates] [Type de bien] [Rechercher] │
├──────────────────────────────────────────────────────────────┤
│  Annonces mises en avant                                       │
│  ┌────────────┐ ┌────────────┐ ┌────────────┐ ┌────────────┐  │
│  │ Photo       │ │ Photo       │ │ Photo       │ │ Photo       │  │
│  │ Titre       │ │ Titre       │ │ Titre       │ │ Titre       │  │
│  │ Ville       │ │ Ville       │ │ Ville       │ │ Ville       │  │
│  │ Prix/jour   │ │ Prix/jour   │ │ Prix/jour   │ │ Prix/jour   │  │
│  └────────────┘ └────────────┘ └────────────┘ └────────────┘  │
├──────────────────────────────────────────────────────────────┤
│  Pied de page (liens, mentions légales)                        │
└──────────────────────────────────────────────────────────────┘
```

Maquette haute-fidélité : voir §8.1.

### 4.2 Recherche (résultats filtrés)

```
┌──────────────────────────────────────────────────────────────┐
│  En-tête + barre de recherche (repliée, éditable)               │
├───────────────┬──────────────────────────────────────────────┤
│  Filtres        │  Résultats (grille de cartes d'annonce)         │
│  - Type de bien │  ┌────────────┐ ┌────────────┐                │
│  - Prix (min/max)│  │ Carte       │ │ Carte       │                │
│  - Capacité      │  └────────────┘ └────────────┘                │
│  - Ville         │  ┌────────────┐ ┌────────────┐                │
│                  │  │ Carte       │ │ Carte       │                │
│                  │  └────────────┘ └────────────┘                │
│                  │  Pagination                                    │
└───────────────┴──────────────────────────────────────────────┘
```

Aucun résultat disponible sur la période demandée → message explicite invitant à élargir les dates (cohérent avec le blocage calendrier automatique de [`BUSINESS_RULES.md`](./BUSINESS_RULES.md) §3.4).

### 4.3 Détail d'une annonce

```
┌──────────────────────────────────────────────────────────────┐
│  Galerie photos (carrousel)                                    │
├───────────────────────────────────┬────────────────────────────┤
│  Titre, ville, description          │  Encadré réservation        │
│  Équipements / caractéristiques     │  [Date arrivée] [Date départ]│
│  Avis (post-MVP, non affiché ici)   │  Récapitulatif : n journées   │
│                                      │  × tarif/jour = total         │
│                                      │  [Demander à réserver]        │
└───────────────────────────────────┴────────────────────────────┘
```

Le calcul du récapitulatif suit exactement [`BUSINESS_RULES.md`](./BUSINESS_RULES.md) §3.2-§3.3 (12h-12h, jour de départ non facturé). Maquette haute-fidélité : voir §8.2.

### 4.4 Création de compte

```
┌────────────────────────────────────┐
│  Créer un compte                      │
│  [Nom complet]                        │
│  [Email]                              │
│  [Mot de passe]                       │
│  [Confirmer le mot de passe]          │
│  ( ) Je souhaite devenir partenaire   │
│  [Créer mon compte]                   │
│  Déjà un compte ? [Se connecter]      │
└────────────────────────────────────┘
```

La case « devenir partenaire » n'active pas immédiatement le rôle `partner` : elle déclenche le parcours de soumission d'un profil `Partner` (état `en_attente`, voir [`DATABASE.md`](./DATABASE.md) §5.1), distinct de l'inscription simple.

### 4.5 Connexion

```
┌────────────────────────────────────┐
│  Se connecter                          │
│  [Email]                              │
│  [Mot de passe]                       │
│  [ ] Se souvenir de moi                │
│  [Se connecter]                       │
│  Mot de passe oublié ? / [Créer un compte] │
└────────────────────────────────────┘
```

Maquette haute-fidélité (inscription + connexion) : voir §8.3.

## 5. Écrans — Client

### 5.1 Demande de réservation (formulaire de soumission)

```
┌──────────────────────────────────────────────────────────────┐
│  Résumé du bien (photo, titre, dates sélectionnées)             │
│  Récapitulatif tarifaire (n journées × tarif = total, définitif)│
│  [Confirmer ma demande de réservation]                          │
│  Note : le partenaire dispose d'un délai pour répondre.          │
└──────────────────────────────────────────────────────────────┘
```

### 5.2 Suivi d'une demande / réponse à une contre-proposition

```
┌──────────────────────────────────────────────────────────────┐
│  Statut : [EN_ATTENTE | CONFIRMÉE | CONTRE-PROPOSÉE | REFUSÉE]   │
│  (badge coloré — voir docs/engineering/06 §3 et §9.1)             │
├──────────────────────────────────────────────────────────────┤
│  Si CONTRE-PROPOSÉE :                                            │
│  Bien alternatif proposé (photo, titre, dates)                   │
│  [Accepter la proposition]   [Refuser la proposition]            │
├──────────────────────────────────────────────────────────────┤
│  Historique des échanges (horodaté)                              │
└──────────────────────────────────────────────────────────────┘
```

Maquette haute-fidélité (demande + suivi) : voir §8.4.

### 5.3 Historique des réservations

```
┌──────────────────────────────────────────────────────────────┐
│  Onglets : [Toutes] [À venir] [Passées] [Annulées]               │
├──────────────────────────────────────────────────────────────┤
│  Liste (carte empilée en mobile, tableau en desktop — voir       │
│  docs/engineering/06 §7) : bien, dates, statut, montant           │
└──────────────────────────────────────────────────────────────┘
```

### 5.4 Profil

```
┌────────────────────────────────────┐
│  Informations personnelles            │
│  [Nom] [Email] [Téléphone]            │
│  [Enregistrer]                        │
│  ─────────────                        │
│  Sécurité : [Changer le mot de passe] │
└────────────────────────────────────┘
```

### 5.5 Notifications

```
┌──────────────────────────────────────────────────────────────┐
│  Liste chronologique, non lues mises en évidence                 │
│  ● Réservation confirmée — Résidence Les Palmiers — il y a 2h    │
│  ○ Contre-proposition reçue — Villa du Lac — hier                │
└──────────────────────────────────────────────────────────────┘
```

## 6. Écrans — Partenaire

### 6.1 Tableau de bord

```
┌──────────────────────────────────────────────────────────────┐
│  Cartes de synthèse : demandes en attente | réservations à venir │
│  | biens publiés | taux d'acceptation                             │
├──────────────────────────────────────────────────────────────┤
│  Dernières demandes reçues (aperçu, lien vers gestion réservations)│
└──────────────────────────────────────────────────────────────┘
```

Maquette haute-fidélité : voir §8.5.

### 6.2 Gestion des résidences / véhicules (liste + création/édition)

```
┌──────────────────────────────────────────────────────────────┐
│  [+ Ajouter une résidence]                     [Filtrer par statut]│
├──────────────────────────────────────────────────────────────┤
│  Liste : photo, titre, ville, statut (badge), tarif, [Éditer]     │
└──────────────────────────────────────────────────────────────┘

┌──────────────────────────────────────────────────────────────┐
│  Formulaire création/édition :                                  │
│  [Titre] [Description] [Adresse] [Ville] [Capacité] [Tarif/jour] │
│  Équipements (cases à cocher, stockées en `attributes` jsonb)     │
│  [Enregistrer en brouillon]   [Soumettre pour validation]         │
└──────────────────────────────────────────────────────────────┘
```

### 6.3 Gestion des disponibilités

```
┌──────────────────────────────────────────────────────────────┐
│  Sélecteur de bien : [Résidence ▾]                                │
│  Calendrier mensuel : jours bloqués (rouge), disponibles (blanc) │
│  [+ Ajouter un blocage manuel] → motif (entretien/maintenance/    │
│  usage personnel), période                                        │
└──────────────────────────────────────────────────────────────┘
```

Chaque cellule bloquée affiche l'origine du blocage (réservation confirmée ou blocage manuel), conformément à [`DATABASE.md`](./DATABASE.md) §7. Maquette haute-fidélité : voir §8.6.

### 6.4 Gestion des réservations (traitement d'une demande)

```
┌──────────────────────────────────────────────────────────────┐
│  Liste des demandes (statut, client, bien, dates, montant)        │
├──────────────────────────────────────────────────────────────┤
│  Détail d'une demande EN_ATTENTE :                                │
│  [Accepter]   [Refuser]   [Refuser avec contre-proposition]       │
│  Si contre-proposition : sélection d'un bien alternatif disponible│
└──────────────────────────────────────────────────────────────┘
```

### 6.5 Gestion des tarifs

```
┌────────────────────────────────────┐
│  Tarif journalier de référence : [__]│
│  Tarifs par période (optionnel) :     │
│  [Du __] [Au __] [Tarif __] [+ Ajouter]│
└────────────────────────────────────┘
```

### 6.6 Gestion des photos

```
┌────────────────────────────────────┐
│  Zone de dépôt (drag & drop)          │
│  Galerie réordonnable (glisser-déposer)│
│  [🗑 Supprimer] sur chaque vignette    │
└────────────────────────────────────┘
```

## 7. Écrans — Administration

### 7.1 Validation des partenaires

```
┌──────────────────────────────────────────────────────────────┐
│  File d'attente : nom, date d'inscription, documents fournis      │
│  [Consulter le dossier] → [Valider]   [Rejeter]                  │
└──────────────────────────────────────────────────────────────┘
```

Maquette haute-fidélité (validation partenaires + annonces) : voir §8.7.

### 7.2 Validation des annonces

```
┌──────────────────────────────────────────────────────────────┐
│  File d'attente : photo, titre, partenaire, date de soumission   │
│  [Consulter] → [Valider]   [Rejeter (motif obligatoire)]         │
└──────────────────────────────────────────────────────────────┘
```

### 7.3 Gestion des utilisateurs

```
┌──────────────────────────────────────────────────────────────┐
│  Recherche + filtres (rôle, statut)                               │
│  Liste : nom, email, rôle, statut, [Voir] [Suspendre]             │
└──────────────────────────────────────────────────────────────┘
```

### 7.4 Statistiques

```
┌──────────────────────────────────────────────────────────────┐
│  Indicateurs (voir PRODUCT.md §11) :                              │
│  Taux d'acceptation | Délai moyen de réponse | Taux de conversion│
│  Taux de contre-proposition acceptée | Partenaires actifs         │
│  Graphiques d'évolution (réservations / mois)                     │
└──────────────────────────────────────────────────────────────┘
```

### 7.5 Paramètres

```
┌────────────────────────────────────┐
│  Délai de réponse partenaire (h) : [__]│
│  Délai d'expiration contre-proposition (h) : [__]│
│  [Enregistrer]                        │
└────────────────────────────────────┘
```

Correspond aux entrées de la table `platform_settings` (voir [`DATABASE.md`](./DATABASE.md) §10.2), par exemple `reservation_response_delay_hours` et `counter_offer_response_delay_hours`.

## 8. Maquettes HTML/Tailwind haute-fidélité

Les maquettes suivantes sont des prototypes HTML statiques, autonomes (Tailwind CDN + Alpine.js CDN), à ouvrir directement dans un navigateur. Elles respectent scrupuleusement les tokens et composants de [`docs/engineering/06-blade-tailwind-guidelines.md`](./docs/engineering/06-blade-tailwind-guidelines.md) et servent de base directe aux futurs composants Blade (`resources/views/components/...`).

| # | Écran | Fichier |
|---|---|---|
| 8.1 | Accueil + recherche (Public) | [`docs/ux/mockups/01-accueil-recherche.html`](./docs/ux/mockups/01-accueil-recherche.html) |
| 8.2 | Détail d'une annonce (Public) | [`docs/ux/mockups/02-detail-annonce.html`](./docs/ux/mockups/02-detail-annonce.html) |
| 8.3 | Connexion / Inscription (Public) | [`docs/ux/mockups/03-connexion-inscription.html`](./docs/ux/mockups/03-connexion-inscription.html) |
| 8.4 | Demande + suivi de réservation (Client) | [`docs/ux/mockups/04-reservation-client.html`](./docs/ux/mockups/04-reservation-client.html) |
| 8.5 | Tableau de bord (Partenaire) | [`docs/ux/mockups/05-dashboard-partenaire.html`](./docs/ux/mockups/05-dashboard-partenaire.html) |
| 8.6 | Gestion des disponibilités (Partenaire) | [`docs/ux/mockups/06-disponibilites.html`](./docs/ux/mockups/06-disponibilites.html) |
| 8.7 | Validation des partenaires/annonces (Admin) | [`docs/ux/mockups/07-validation-admin.html`](./docs/ux/mockups/07-validation-admin.html) |

Ces sept écrans couvrent au moins un parcours représentatif par acteur (Visiteur, Client, Partenaire, Administrateur) et les moments les plus sensibles du cycle de réservation (§5 de [`BUSINESS_RULES.md`](./BUSINESS_RULES.md)). Les écrans restants du MVP (§4 à §7 ci-dessus) suivent la même structure et les mêmes composants ; ils seront maquettés au fil du développement plutôt que dupliqués ici sans valeur ajoutée.

## 9. Traçabilité avec la documentation existante

| Section | Source(s) métier | Source(s) technique(s) |
|---|---|---|
| Sitemap et inventaire des écrans (§3-§7) | [`PRODUCT.md`](./PRODUCT.md) §6, §8, §9 | — |
| Statuts et libellés affichés | [`BUSINESS_RULES.md`](./BUSINESS_RULES.md) §8 | [`DATABASE.md`](./DATABASE.md) §8.1, §8.3 |
| Design system, tokens, composants | — | `docs/engineering/06-blade-tailwind-guidelines.md` |
| Calendrier de disponibilité (§6.3) | [`BUSINESS_RULES.md`](./BUSINESS_RULES.md) §7 | [`DATABASE.md`](./DATABASE.md) §7 |
| Paramètres plateforme (§7.5) | — | [`DATABASE.md`](./DATABASE.md) §10.2 |

Toute évolution d'un écran doit rester cohérente avec ce document avant modification du code Blade correspondant, conformément au principe « aucun développement sans conception préalable » ([`ENGINEERING.md`](./ENGINEERING.md) §2).
