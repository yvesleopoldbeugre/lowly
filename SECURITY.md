# SECURITY.md — Politique de Sécurité LOWLY

## Table des matières

1. [Portée du document](#1-portée-du-document)
2. [Modèle de menaces](#2-modèle-de-menaces)
3. [Gestion des identités et des accès](#3-gestion-des-identités-et-des-accès)
4. [Rôles et permissions](#4-rôles-et-permissions)
5. [OWASP Top 10 — application à LOWLY](#5-owasp-top-10--application-à-lowly)
6. [Validation des données](#6-validation-des-données)
7. [Protection CSRF](#7-protection-csrf)
8. [Protection des données sensibles](#8-protection-des-données-sensibles)
9. [Journalisation et audit](#9-journalisation-et-audit)
10. [Gestion des fichiers uploadés](#10-gestion-des-fichiers-uploadés)
11. [Sécurité de l'infrastructure](#11-sécurité-de-linfrastructure)
12. [Procédure en cas d'incident](#12-procédure-en-cas-dincident)

---

## 1. Portée du document

Ce document définit les principes de sécurité applicative et organisationnelle de LOWLY. Le détail opérationnel (configuration précise, exemples de code, en-têtes HTTP) est dans `docs/engineering/10-security-guidelines.md`. Ce document-ci pose les **principes** ; le handbook pose la **mise en œuvre**.

## 2. Modèle de menaces

LOWLY traite des données sensibles à plusieurs niveaux :

- données personnelles des clients et partenaires (identité, coordonnées) ;
- documents justificatifs des partenaires (pièces d'identité, documents légaux) ;
- informations de réservation (dates, montants) ;
- à terme, données de paiement (hors MVP, mais anticipées architecturalement).

Les principales surfaces de risque identifiées :

| Surface | Risque principal |
|---|---|
| Inscription / connexion | Usurpation de compte, brute-force |
| Upload de photos/documents | Injection de fichiers malveillants |
| Formulaires de réservation | Manipulation de tarifs/dates côté client |
| Back-office administration | Élévation de privilèges, accès non autorisé |
| API interne | Fuite de données via endpoints mal protégés |

## 3. Gestion des identités et des accès

- Les mots de passe sont hachés avec un algorithme robuste (Bcrypt/Argon2, standard Laravel), jamais stockés en clair.
- L'authentification à deux facteurs est envisagée en post-MVP pour les comptes partenaires et administrateurs (non requise au MVP mais l'architecture doit permettre son ajout sans refonte).
- Les sessions expirent après une durée d'inactivité configurée.
- Toute tentative de connexion échouée est journalisée et soumise à une limitation de fréquence (voir §5.2 et [`API_GUIDE.md`](./API_GUIDE.md) §13).

## 4. Rôles et permissions

Trois rôles au MVP : `client`, `partner`, `admin`. Chaque action sensible est protégée par une `Policy` Laravel dédiée au domaine concerné (voir [`ARCHITECTURE.md`](./ARCHITECTURE.md) §7).

| Rôle | Peut | Ne peut pas |
|---|---|---|
| `client` | Réserver, consulter son historique, gérer son profil | Accéder au tableau de bord partenaire ou à l'administration |
| `partner` | Gérer ses propres biens, réservations, tarifs, photos | Accéder aux biens ou réservations d'un autre partenaire |
| `admin` | Valider partenaires/annonces, gérer les utilisateurs, consulter les statistiques | Modifier directement le contenu d'une annonce à la place du partenaire (sauf rejet/suspension) |

**Règle d'or** : toute requête portant sur une ressource appartenant à un partenaire doit vérifier que l'utilisateur authentifié est bien le propriétaire de cette ressource, via une `Policy`, avant toute lecture ou écriture.

## 5. OWASP Top 10 — application à LOWLY

| Catégorie OWASP | Mesure appliquée |
|---|---|
| Contrôle d'accès défaillant | `Policies` Laravel systématiques sur chaque action de domaine |
| Défaillances cryptographiques | Hachage des mots de passe, TLS en production (voir [`DEPLOYMENT.md`](./DEPLOYMENT.md)) |
| Injection | Utilisation exclusive de l'ORM Eloquent / requêtes préparées, jamais de SQL concaténé |
| Conception non sécurisée | Revue de conception obligatoire avant développement (voir [`ENGINEERING.md`](./ENGINEERING.md)) |
| Mauvaise configuration de sécurité | Configuration durcie par environnement, secrets hors dépôt Git |
| Composants vulnérables | Suivi des dépendances Composer/npm, mise à jour régulière |
| Identification et authentification défaillantes | Limitation de fréquence, hachage robuste, expiration de session |
| Intégrité des données et du logiciel | CI exécutant tests et analyse statique avant tout déploiement |
| Journalisation et surveillance insuffisantes | Journalisation des actions sensibles (voir §9) |
| Falsification de requête côté serveur (SSRF) | Pas d'appel sortant piloté par une entrée utilisateur non validée |

## 6. Validation des données

- Toute donnée entrante transite par une `Form Request` Laravel dédiée avant d'atteindre la logique métier (voir [`ARCHITECTURE.md`](./ARCHITECTURE.md) §7).
- Les règles de validation reflètent strictement les règles métier de [`BUSINESS_RULES.md`](./BUSINESS_RULES.md) (ex : dates de réservation cohérentes, montants positifs).
- Aucune confiance n'est accordée à une donnée calculée côté client (tarif, nombre de journées) : elle est systématiquement recalculée côté serveur avant confirmation.

## 7. Protection CSRF

- Toutes les routes web (formulaires Blade) utilisent le jeton CSRF standard de Laravel.
- Les appels Ajax internes (Alpine.js) transmettent le jeton via l'en-tête `X-CSRF-TOKEN` (voir [`API_GUIDE.md`](./API_GUIDE.md) §4).
- Aucune route de mutation (POST/PATCH/DELETE) n'est exemptée de la protection CSRF sans justification documentée.

## 8. Protection des données sensibles

| Donnée | Mesure |
|---|---|
| Documents justificatifs partenaires | Stockage sur disque à accès restreint, jamais servi par une URL publique directe |
| Coordonnées personnelles | Accès limité aux seules parties concernées (client, partenaire de la réservation, administration) |
| Mots de passe | Hachage, jamais journalisés, jamais retournés par l'API |
| Données de paiement (post-MVP) | Déportées vers un prestataire certifié PCI-DSS ; LOWLY ne stocke jamais de numéro de carte |

## 9. Journalisation et audit

- Toute action administrative sensible (validation/rejet de partenaire, validation/rejet d'annonce, suspension d'utilisateur) est enregistrée dans `admin_actions` (voir [`DATABASE.md`](./DATABASE.md) §10.1), avec l'identité de l'administrateur et l'horodatage.
- Tout changement d'état d'une réservation est enregistré dans `reservation_status_history` (voir [`DATABASE.md`](./DATABASE.md) §8.2), garantissant une traçabilité complète du cycle de vie de chaque réservation.
- Les journaux applicatifs ne doivent jamais contenir de mot de passe, jeton de session ou document justificatif.

## 10. Gestion des fichiers uploadés

- Les photos et documents uploadés sont validés sur leur type MIME réel (pas uniquement l'extension) et leur taille maximale.
- Les fichiers sont renommés à l'upload (pas de conservation du nom de fichier original dans le chemin de stockage), pour éviter toute injection de chemin.
- Les documents justificatifs partenaires ne sont jamais exposés sur une URL publique directe ; leur accès passe systématiquement par un contrôleur vérifiant l'autorisation.

## 11. Sécurité de l'infrastructure

- Communication chiffrée en TLS de bout en bout en environnement `staging` et `production` (voir [`DEPLOYMENT.md`](./DEPLOYMENT.md)).
- Secrets d'environnement (`.env`) jamais commités dans le dépôt Git ; gestion via un mécanisme de secrets dédié en CI/CD.
- Séparation stricte des environnements `local`, `staging`, `production` (bases de données, clés, identifiants distincts).

## 12. Procédure en cas d'incident

1. Identification et confinement de l'incident (isolation du composant concerné si nécessaire).
2. Évaluation de l'impact (données concernées, utilisateurs affectés).
3. Correction du vecteur de faille identifié.
4. Notification des parties concernées si des données personnelles sont impliquées, conformément aux obligations légales applicables.
5. Post-mortem documenté et intégré aux décisions d'architecture (`docs/engineering/18-adr.md`) si la correction implique un changement structurel.
