# 06 — Blade & Tailwind Guidelines

## Table des matières

1. [Portée du document](#1-portée-du-document)
2. [Design system](#2-design-system)
3. [Palette et tokens](#3-palette-et-tokens)
4. [Layouts](#4-layouts)
5. [Composants Blade](#5-composants-blade)
6. [Conventions Tailwind](#6-conventions-tailwind)
7. [Responsive](#7-responsive)
8. [Accessibilité](#8-accessibilité)
9. [Exemples de composants clés](#9-exemples-de-composants-clés)

---

## 1. Portée du document

Ce document fixe les conventions de rendu front-end de LOWLY, exclusivement Blade + Tailwind CSS 4 + Alpine.js (aucun framework JS séparé, voir [`AGENT.md`](../../AGENT.md)).

## 2. Design system

LOWLY s'appuie sur un design system minimal et cohérent, structuré autour de trois niveaux :

```
Tokens (couleurs, espacements, typographie)
      │
      ▼
Composants Blade atomiques (bouton, badge, champ de formulaire)
      │
      ▼
Composants Blade composites (carte d'annonce, calendrier, formulaire de réservation)
```

Aucun style « one-off » ne doit être écrit pour un écran unique si un composant existant peut être réutilisé ou légèrement étendu.

## 3. Palette et tokens

Les tokens de couleur et typographie sont centralisés, jamais redéfinis en dur dans une vue Blade. Le projet utilise Tailwind CSS 4 (voir `ADR-005`, `18-adr.md`), qui n'a pas de `tailwind.config.js` : les tokens sont définis via la directive `@theme` dans `resources/css/app.css`, seul mécanisme de configuration réel en v4.

| Token | Usage |
|---|---|
| `primary` | Actions principales (réserver, confirmer) |
| `secondary` | Actions secondaires (annuler, retour) |
| `success` | États positifs (réservation confirmée) |
| `warning` | États intermédiaires (en attente, contre-proposition) |
| `danger` | États négatifs (réservation refusée, blocage) |
| `neutral-*` | Textes, fonds, bordures |

Les statuts de réservation (voir [`BUSINESS_RULES.md`](../../BUSINESS_RULES.md) §8) doivent toujours utiliser la même couleur associée dans toute l'application (ex : `warning` pour `EN_ATTENTE` et `CONTRE-PROPOSÉE`, `success` pour `CONFIRMÉE`, `danger` pour `REFUSÉE`/`EXPIRÉE`).

## 4. Layouts

```
resources/views/
├── layouts/
│   ├── app.blade.php          (layout authentifié commun client/partenaire)
│   ├── guest.blade.php        (layout public)
│   └── admin.blade.php        (layout back-office)
├── components/
│   ├── ui/                    (composants atomiques : bouton, badge, input)
│   └── domain/                (composants composites liés à un domaine métier)
└── pages/
    ├── public/
    ├── client/
    ├── partner/
    └── admin/
```

Chaque layout définit les sections communes (navigation, notifications, pied de page) via `@yield` / `<x-slot>` ; aucune page ne redéfinit sa propre structure de navigation.

## 5. Composants Blade

- Un composant par élément d'interface réutilisable, sous forme de classe (`App\View\Components\...`) si logique associée, ou anonyme (fichier Blade seul) si purement présentationnel.
- Nommage en `kebab-case` : `<x-ui.button>`, `<x-domain.reservation-status-badge :status="$reservation->status" />`.
- Un composant ne contient jamais d'accès direct à Eloquent : les données lui sont toujours passées en paramètres depuis la vue ou le contrôleur.

## 6. Conventions Tailwind

- Utilisation exclusive des classes utilitaires Tailwind ; pas de CSS custom sauvage dans des balises `<style>` sauf cas exceptionnel documenté.
- Pas de valeurs arbitraires Tailwind (`w-[137px]`) sauf nécessité réelle documentée en commentaire ; préférer l'échelle standard (`w-32`, `w-36`).
- Les combinaisons de classes répétées plus de deux fois dans le projet doivent être extraites en composant Blade plutôt que copiées-collées.
- Ordre conventionnel des classes dans un attribut `class` : disposition (`flex`, `grid`) → dimensionnement (`w-`, `h-`) → espacement (`p-`, `m-`) → typographie → couleur → état (`hover:`, `focus:`) → responsive (`md:`, `lg:`).

## 7. Responsive

- Approche mobile-first systématique : les classes sans préfixe s'appliquent au mobile, les préfixes `sm:`, `md:`, `lg:`, `xl:` ajustent progressivement.
- Les tableaux de données (ex : liste des réservations partenaire) doivent avoir une variante carte empilée en mobile, jamais un tableau HTML simplement réduit et illisible.
- Le calendrier de disponibilité (composant central du domaine `Availability`) doit rester utilisable au doigt sur mobile (cibles tactiles suffisamment grandes).

## 8. Accessibilité

- Toute image (photo d'annonce) porte un attribut `alt` descriptif, jamais vide sauf image strictement décorative.
- Tout élément interactif (bouton, lien) est accessible au clavier (`focus-visible` stylé, jamais supprimé).
- Les couleurs de statut (§3) sont toujours accompagnées d'un libellé textuel, jamais de la couleur seule pour porter l'information (contrainte WCAG).
- Les formulaires (demande de réservation, création d'annonce) associent chaque champ à un `<label>` explicite.

## 9. Exemples de composants clés

### 9.1 Badge de statut de réservation

```blade
{{-- resources/views/components/domain/reservation-status-badge.blade.php --}}
@props(['status'])

@php
$styles = [
    'en_attente' => 'bg-warning-100 text-warning-800',
    'confirmee' => 'bg-success-100 text-success-800',
    'refusee' => 'bg-danger-100 text-danger-800',
    'contre_proposee' => 'bg-warning-100 text-warning-800',
    'expiree' => 'bg-neutral-100 text-neutral-600',
];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full px-3 py-1 text-sm font-medium ' . $styles[$status]]) }}>
    {{ __('reservation.status.' . $status) }}
</span>
```

### 9.2 Carte d'annonce (résidence ou véhicule)

```blade
{{-- resources/views/components/domain/listing-card.blade.php --}}
@props(['listing'])

<article class="flex flex-col rounded-lg border border-neutral-200 overflow-hidden">
    <img src="{{ $listing->photos->first()?->url }}" alt="{{ $listing->title }}" class="h-48 w-full object-cover">
    <div class="flex flex-col gap-2 p-4">
        <h3 class="text-lg font-semibold text-neutral-900">{{ $listing->title }}</h3>
        <p class="text-sm text-neutral-600">{{ $listing->city }}</p>
        <p class="text-primary-700 font-semibold">{{ $listing->daily_rate }} / jour</p>
    </div>
</article>
```

Ces exemples illustrent le principe : logique minimale dans la vue, données déjà préparées par le contrôleur/`Resource`, styles exclusivement via classes Tailwind utilitaires.
