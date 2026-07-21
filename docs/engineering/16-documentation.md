# 16 — Documentation

## Table des matières

1. [Portée du document](#1-portée-du-document)
2. [Principe général de documentation](#2-principe-général-de-documentation)
3. [Hiérarchie des documents](#3-hiérarchie-des-documents)
4. [Quand mettre à jour quel document](#4-quand-mettre-à-jour-quel-document)
5. [ADR — quand en écrire un](#5-adr--quand-en-écrire-un)
6. [Documentation du README](#6-documentation-du-readme)
7. [Documentation de l'API](#7-documentation-de-lapi)
8. [Diagrammes](#8-diagrammes)
9. [PHPDoc](#9-phpdoc)

---

## 1. Portée du document

Ce document explique comment documenter LOWLY : quel document mettre à jour pour quel type de changement, comment écrire un ADR, comment maintenir les diagrammes et le PHPDoc.

## 2. Principe général de documentation

La documentation de LOWLY n'est pas un exercice ponctuel réalisé une fois puis abandonné : elle vit avec le code. **Aucune Pull Request qui change un comportement documenté ne peut être fusionnée sans mise à jour du document correspondant.** Une documentation obsolète est considérée comme un défaut au même titre qu'un bug de code.

## 3. Hiérarchie des documents

```
Documents racine (référence opposable)
  PRODUCT.md, BUSINESS_RULES.md, ARCHITECTURE.md, DATABASE.md, API_GUIDE.md,
  ENGINEERING.md, SECURITY.md, TESTING.md, DEPLOYMENT.md, ROADMAP.md
        │
        ▼
Engineering Handbook (mise en œuvre quotidienne)
  docs/engineering/01-*.md à 18-adr.md, glossary.md
        │
        ▼
Commentaires PHPDoc et README locaux (détail d'implémentation ponctuel)
```

En cas de contradiction entre deux niveaux, le niveau supérieur (document racine) prévaut toujours.

## 4. Quand mettre à jour quel document

| Type de changement | Document(s) à mettre à jour |
|---|---|
| Nouvelle règle métier ou modification d'une règle existante | [`BUSINESS_RULES.md`](../../BUSINESS_RULES.md) |
| Nouvel endpoint ou changement de contrat d'API | [`API_GUIDE.md`](../../API_GUIDE.md), `09-api-guidelines.md` si convention générale affectée |
| Nouvelle table ou changement de schéma | [`DATABASE.md`](../../DATABASE.md) |
| Nouveau domaine ou changement de dépendance entre domaines | [`ARCHITECTURE.md`](../../ARCHITECTURE.md), `04-architecture.md` |
| Changement de périmètre produit (ajout/retrait de fonctionnalité) | [`PRODUCT.md`](../../PRODUCT.md), [`ROADMAP.md`](../../ROADMAP.md) |
| Décision d'architecture significative (choix technique, arbitrage) | `18-adr.md` |
| Changement de convention de code | Le fichier `0X-*.md` correspondant du handbook |

## 5. ADR — quand en écrire un

Une entrée ADR (`18-adr.md`) est requise dès qu'une décision remplit au moins un de ces critères :

- elle engage le projet sur une durée longue et serait coûteuse à inverser (choix de framework, de base de données, de modèle d'architecture) ;
- elle a été débattue entre plusieurs options sérieuses, et la raison du choix final n'est pas évidente en lisant simplement le code ;
- elle contraint les décisions futures (ex : le choix du monolithe modulaire contraint la façon d'ajouter de nouveaux domaines).

Une décision purement locale et réversible (nom d'une variable, choix d'une librairie utilitaire mineure) ne nécessite pas d'ADR.

## 6. Documentation du README

Le [`README.md`](../../README.md) racine reste volontairement synthétique : présentation, stack, démarrage rapide, liens vers le reste de la documentation. Il ne doit jamais accumuler de détail qui appartient à un document plus spécifique — un détail d'installation avancé va dans `15-deployment.md`, pas dans le README.

## 7. Documentation de l'API

Toute évolution de la liste d'endpoints doit être répercutée dans [`API_GUIDE.md`](../../API_GUIDE.md) au moment même de la Pull Request qui l'introduit (voir `09-api-guidelines.md` §9). Un endpoint interne temporaire et non stable doit être explicitement marqué comme tel s'il apparaît dans la documentation, pour éviter toute confusion sur sa stabilité.

## 8. Diagrammes

- Les diagrammes d'architecture (C4, flux) sont maintenus en ASCII directement dans les documents Markdown, pour rester versionnés et diffables avec le code, plutôt que dans un outil externe déconnecté du dépôt.
- Tout nouveau flux applicatif significatif (nouveau cycle métier comparable au cycle de réservation) doit être accompagné d'un diagramme de séquence ASCII dans [`ARCHITECTURE.md`](../../ARCHITECTURE.md) ou `04-architecture.md`.

## 9. PHPDoc

- Le PHPDoc n'est pas systématique sur chaque méthode : il est requis lorsque la signature seule ne suffit pas à comprendre l'intention (comportement non évident, effet de bord, exception spécifique levée).
- Un PHPDoc ne doit jamais se contenter de répéter la signature de méthode en langage naturel (`@param string $id L'identifiant` n'apporte rien) ; il documente le **pourquoi** ou une contrainte non visible dans le type.

```php
/**
 * Confirme la réservation et bloque le calendrier de manière atomique.
 *
 * @throws PeriodeIndisponibleException si le calendrier a été bloqué entre
 *         la validation initiale de la demande et cet appel (cas de concurrence).
 */
public function executer(Reservation $reservation): Reservation
{
    // ...
}
```
