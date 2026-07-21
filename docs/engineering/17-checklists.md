# 17 — Checklists

## Table des matières

1. [Portée du document](#1-portée-du-document)
2. [Checklist avant commit](#2-checklist-avant-commit)
3. [Checklist avant Pull Request](#3-checklist-avant-pull-request)
4. [Checklist avant merge](#4-checklist-avant-merge)
5. [Checklist avant release](#5-checklist-avant-release)
6. [Checklist avant mise en production](#6-checklist-avant-mise-en-production)

---

## 1. Portée du document

Ce document regroupe, en un seul endroit, toutes les checklists opérationnelles dispersées dans le reste du handbook — pour un usage rapide au quotidien, sans avoir à naviguer entre plusieurs fichiers.

## 2. Checklist avant commit

- [ ] Le code respecte le style Pint (`./vendor/bin/pint`).
- [ ] Aucun fichier de configuration local (`.env`, clés) n'est inclus dans le commit.
- [ ] Le message de commit suit la convention décrite dans `13-git-workflow.md` §4.
- [ ] Aucun code de debug (`dd()`, `dump()`, `console.log` de test) n'est laissé dans le commit.

## 3. Checklist avant Pull Request

- [ ] La branche est à jour avec `main` (rebase ou merge selon la convention d'équipe).
- [ ] Les tests couvrant la fonctionnalité ont été écrits et passent localement.
- [ ] La documentation impactée est mise à jour dans la même Pull Request (voir `16-documentation.md` §4).
- [ ] La description de la Pull Request explique le contexte métier et référence les documents concernés.

## 4. Checklist avant merge

Reprend intégralement la checklist détaillée de `14-code-review.md` §3 :

- [ ] Conformité métier vérifiée par rapport à [`BUSINESS_RULES.md`](../../BUSINESS_RULES.md).
- [ ] Conformité architecturale vérifiée (domaines, événements, aucune logique métier dans les contrôleurs).
- [ ] Sécurité vérifiée (Policies, FormRequests, absence de secrets).
- [ ] Tests présents et suite complète verte en CI.
- [ ] Documentation à jour.
- [ ] Approbation d'au moins un relecteur.

## 5. Checklist avant release

- [ ] Toutes les Pull Requests prévues pour cette release sont fusionnées dans `main`.
- [ ] La suite de tests complète est verte sur `main`.
- [ ] [`ROADMAP.md`](../../ROADMAP.md) reflète l'état réel d'avancement des phases concernées.
- [ ] Le numéro de version suit la convention SemVer décrite dans `13-git-workflow.md` §6.
- [ ] Les migrations de la release ont été testées en `staging` avec un volume de données représentatif si elles touchent une table volumineuse.

## 6. Checklist avant mise en production

- [ ] Déploiement validé fonctionnellement en `staging` (voir [`DEPLOYMENT.md`](../../DEPLOYMENT.md) §10).
- [ ] Sauvegarde récente et vérifiée de la base de données de production disponible avant migration (voir `15-deployment.md` §7).
- [ ] Plan de rollback identifié pour cette release (voir [`DEPLOYMENT.md`](../../DEPLOYMENT.md) §11).
- [ ] Monitoring actif et vérifié fonctionnel avant le déploiement (sonde de santé, alerting).
- [ ] Fenêtre de déploiement communiquée si un impact utilisateur est anticipé.
- [ ] Vérification post-déploiement effectuée : sonde de santé, logs, parcours critiques testés manuellement une fois en production.
