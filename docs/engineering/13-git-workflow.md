# 13 — Git Workflow

## Table des matières

1. [Portée du document](#1-portée-du-document)
2. [Modèle de branches](#2-modèle-de-branches)
3. [Nommage des branches](#3-nommage-des-branches)
4. [Convention de commits](#4-convention-de-commits)
5. [Pull Requests](#5-pull-requests)
6. [Tags et releases](#6-tags-et-releases)
7. [Gestion des hotfix](#7-gestion-des-hotfix)
8. [Ce qu'il ne faut jamais faire](#8-ce-quil-ne-faut-jamais-faire)

---

## 1. Portée du document

Ce document fixe le workflow Git de LOWLY, du développement d'une fonctionnalité jusqu'à la mise en production (voir [`DEPLOYMENT.md`](../../DEPLOYMENT.md) pour la suite du processus après le merge).

## 2. Modèle de branches

```
main
  │
  ├── develop (optionnelle selon la taille de l'équipe — intégration continue des features)
  │      │
  │      ├── feature/reservation-contre-proposition
  │      ├── feature/dashboard-partenaire
  │      └── fix/calcul-journees-edge-case
  │
  └── hotfix/incident-blocage-calendrier (directement depuis main si urgence production)
```

- `main` reflète toujours l'état déployé (ou déployable) en production.
- Chaque fonctionnalité se développe sur une branche dédiée, jamais directement sur `main`.

## 3. Nommage des branches

| Type | Préfixe | Exemple |
|---|---|---|
| Fonctionnalité | `feature/` | `feature/gestion-photos-annonce` |
| Correction | `fix/` | `fix/expiration-contre-proposition` |
| Correctif urgent production | `hotfix/` | `hotfix/double-reservation-vehicule` |
| Tâche technique / refactor | `chore/` | `chore/extraction-service-calcul-journees` |
| Documentation | `docs/` | `docs/mise-a-jour-api-guide` |

## 4. Convention de commits

Format inspiré des Conventional Commits, adapté au contexte LOWLY :

```
<type>(<domaine>): <résumé impératif>

[corps optionnel expliquant le pourquoi]

[référence ticket/issue]
```

| Type | Usage |
|---|---|
| `feat` | Nouvelle fonctionnalité |
| `fix` | Correction de bug |
| `refactor` | Changement de structure sans changement de comportement |
| `test` | Ajout ou modification de tests |
| `docs` | Documentation uniquement |
| `chore` | Tâche technique (dépendances, configuration) |

Exemple :

```
feat(reservation): ajouter la contre-proposition partenaire

Permet à un partenaire de proposer un bien alternatif après refus,
conformément à BUSINESS_RULES.md §6.

Réf: LOWLY-142
```

## 5. Pull Requests

- Une Pull Request correspond à une unité de travail cohérente et revuable (éviter les Pull Requests qui mélangent plusieurs fonctionnalités indépendantes).
- La description de la Pull Request référence explicitement les documents impactés (ex : « met à jour `BUSINESS_RULES.md` §6 et implémente la contre-proposition »).
- Toute Pull Request doit passer la CI (lint, analyse statique, tests — voir [`DEPLOYMENT.md`](../../DEPLOYMENT.md) §6) avant revue humaine.
- La checklist de revue de code (`14-code-review.md`) est appliquée systématiquement avant approbation.

## 6. Tags et releases

- Chaque mise en production correspond à un tag Git sémantique (`v1.4.0`), suivant la convention SemVer : `MAJEUR.MINEUR.CORRECTIF`.
- Une release **majeure** correspond à un changement de périmètre significatif (ex : ouverture d'une nouvelle catégorie de catalogue, voir [`ROADMAP.md`](../../ROADMAP.md)).
- Une release **mineure** ajoute une fonctionnalité rétrocompatible.
- Une release **corrective** ne contient que des corrections de bugs.

## 7. Gestion des hotfix

```
1. Branche hotfix/ créée directement depuis main
2. Correction minimale et ciblée, sans fonctionnalité additionnelle
3. Tests couvrant explicitement le cas corrigé
4. Revue accélérée mais non supprimée
5. Merge dans main, tag de release corrective
6. Répercussion du correctif dans develop si cette branche existe
```

## 8. Ce qu'il ne faut jamais faire

- Ne jamais pousser directement sur `main` sans passer par une Pull Request revue.
- Ne jamais réécrire l'historique (`force push`) d'une branche partagée par plusieurs contributeurs.
- Ne jamais fusionner une Pull Request dont la CI a échoué, même « temporairement » en attendant un correctif.
- Ne jamais mélanger dans un même commit une modification de règle métier et un renommage massif de fichiers — cela rend la revue et l'historique illisibles.
