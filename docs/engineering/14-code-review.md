# 14 — Code Review

## Table des matières

1. [Portée du document](#1-portée-du-document)
2. [Objectif de la revue de code](#2-objectif-de-la-revue-de-code)
3. [Checklist complète avant merge](#3-checklist-complète-avant-merge)
4. [Posture du relecteur](#4-posture-du-relecteur)
5. [Posture de l'auteur](#5-posture-de-lauteur)
6. [Niveaux de commentaires](#6-niveaux-de-commentaires)
7. [Délais indicatifs](#7-délais-indicatifs)

---

## 1. Portée du document

Ce document fixe la checklist et les attentes de la revue de code sur LOWLY, appliquée à chaque Pull Request avant fusion sur `main` (voir `13-git-workflow.md`).

## 2. Objectif de la revue de code

La revue de code n'est pas une formalité : c'est le dernier point de contrôle avant que le code ne devienne la référence vivante de comment LOWLY fonctionne. Elle vérifie trois dimensions : la conformité métier, la conformité architecturale, et la qualité générale du code.

## 3. Checklist complète avant merge

### Conformité métier

- [ ] Le comportement implémenté correspond exactement à [`BUSINESS_RULES.md`](../../BUSINESS_RULES.md) (aucune interprétation libre d'une règle ambiguë sans clarification préalable).
- [ ] Si la Pull Request introduit une nouvelle règle métier, [`BUSINESS_RULES.md`](../../BUSINESS_RULES.md) est mis à jour dans la même Pull Request.
- [ ] Les cas limites pertinents (voir [`BUSINESS_RULES.md`](../../BUSINESS_RULES.md) §10) sont couverts.

### Conformité architecturale

- [ ] Aucune logique métier dans un `Controller` (voir `05-laravel-conventions.md` §2).
- [ ] Les domaines communiquent par événements, pas par appel direct (voir `04-architecture.md` §5, §7).
- [ ] Toute nouvelle règle vérifiable en base est portée par une contrainte SQL (voir `08-database-guidelines.md`).
- [ ] Les conventions de nommage sont respectées (voir `05-laravel-conventions.md` §14).

### Sécurité

- [ ] Toute nouvelle route mutante est protégée par une `Policy`.
- [ ] Toute entrée utilisateur est validée par une `FormRequest` complète.
- [ ] Aucun secret n'est introduit dans le code ou la configuration versionnée.

### Tests

- [ ] Les cas métier critiques concernés par la Pull Request sont couverts par des tests (voir [`TESTING.md`](../../TESTING.md) §7).
- [ ] La suite de tests complète passe en CI.
- [ ] La couverture ne régresse pas (voir `12-testing-guidelines.md` §8).

### Qualité générale

- [ ] Le code respecte le style Laravel Pint (vérifié automatiquement en CI, mais vérifié visuellement en cas de doute).
- [ ] Les noms de classes/méthodes/variables expriment clairement l'intention métier.
- [ ] Aucune duplication évidente de logique métier déjà existante ailleurs dans le domaine.

### Documentation

- [ ] Si la Pull Request modifie un comportement d'API, [`API_GUIDE.md`](../../API_GUIDE.md) est mis à jour.
- [ ] Si la Pull Request implique une décision d'architecture significative, une entrée est ajoutée à `18-adr.md`.

## 4. Posture du relecteur

- La revue porte sur le code, jamais sur la personne. Les commentaires sont formulés de façon factuelle et constructive.
- Un relecteur qui ne comprend pas une partie du code doit le signaler explicitement plutôt que de l'approuver par défaut — l'incompréhension d'un relecteur compétent est souvent le signe d'un manque de clarté du code.
- Toute divergence d'interprétation sur une règle métier est tranchée par référence à [`BUSINESS_RULES.md`](../../BUSINESS_RULES.md), pas par argument d'autorité.

## 5. Posture de l'auteur

- La description de la Pull Request explique le **pourquoi**, pas seulement le **quoi** (le diff montre déjà le quoi).
- Une Pull Request volumineuse doit être accompagnée d'une explication de son découpage logique, ou être scindée si possible.
- L'auteur répond à chaque commentaire, même pour signaler un désaccord argumenté — un commentaire ignoré silencieusement n'est jamais acceptable.

## 6. Niveaux de commentaires

| Préfixe suggéré | Signification | Bloquant pour le merge ? |
|---|---|---|
| `blocking:` | Doit être corrigé avant merge | Oui |
| `question:` | Demande de clarification | Selon la réponse |
| `suggestion:` | Amélioration non essentielle | Non |
| `nit:` | Détail mineur de style | Non |

## 7. Délais indicatifs

- Une première réponse à une Pull Request est attendue dans un délai raisonnable défini par l'équipe (ex : sous 1 jour ouvré), pour ne pas bloquer le flux de développement.
- Une Pull Request de correctif urgent (`hotfix/`, voir `13-git-workflow.md` §7) bénéficie d'une revue accélérée, sans jamais sauter la checklist de sécurité et de tests.
