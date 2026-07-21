# 07 — JavaScript Guidelines

## Table des matières

1. [Portée du document](#1-portée-du-document)
2. [Principe général](#2-principe-général)
3. [Organisation des fichiers JS](#3-organisation-des-fichiers-js)
4. [Alpine.js — conventions](#4-alpinejs--conventions)
5. [Le composant Calendrier](#5-le-composant-calendrier)
6. [Le composant Notifications](#6-le-composant-notifications)
7. [Les Charts (statistiques admin)](#7-les-charts-statistiques-admin)
8. [Appels Ajax vers l'API interne](#8-appels-ajax-vers-lapi-interne)
9. [Ce qu'il ne faut jamais faire](#9-ce-quil-ne-faut-jamais-faire)

---

## 1. Portée du document

Ce document couvre l'usage du JavaScript côté client sur LOWLY, strictement limité à l'interactivité légère (Alpine.js) au-dessus du rendu Blade. Rappel du principe fondateur : **aucun framework JavaScript séparé** (React, Vue, Next.js) n'est autorisé — voir [`AGENT.md`](../../AGENT.md).

## 2. Principe général

Le JavaScript de LOWLY existe pour améliorer l'expérience d'une page déjà fonctionnelle en rendu serveur, jamais pour porter la logique métier. Toute règle de calcul (ex : montant total d'une réservation) est calculée côté serveur et simplement affichée côté client ; le JavaScript ne fait que refléter et interagir, il ne décide jamais.

## 3. Organisation des fichiers JS

```
resources/js/
├── app.js                  (point d'entrée, imports globaux)
├── alpine/
│   ├── calendar.js          (composant calendrier de disponibilité)
│   ├── notifications.js     (composant notifications in-app)
│   ├── reservation-form.js  (formulaire de demande de réservation)
│   └── charts.js            (wrapper autour de la librairie de graphiques admin)
└── utils/
    ├── http.js               (wrapper fetch avec en-têtes CSRF)
    └── dates.js              (formatage de dates ISO 8601)
```

Chaque composant Alpine.js vit dans son propre fichier, exporté comme une fonction retournant l'objet de données/comportement Alpine, jamais défini inline dans une grosse chaîne de caractères en Blade sauf pour des cas triviaux (quelques lignes).

## 4. Alpine.js — conventions

- Utilisation de `Alpine.data()` pour tout composant non trivial, afin de garder le balisage Blade lisible :

```javascript
// resources/js/alpine/reservation-form.js
document.addEventListener('alpine:init', () => {
    Alpine.data('reservationForm', (residenceId) => ({
        startDate: null,
        endDate: null,
        nightsCount: 0,
        totalAmount: 0,
        loading: false,

        updateEstimate() {
            if (!this.startDate || !this.endDate) return;
            this.loading = true;
            fetch(`/api/v1/residences/${residenceId}/estimate`, {
                method: 'POST',
                headers: httpHeaders(),
                body: JSON.stringify({ start_date: this.startDate, end_date: this.endDate }),
            })
                .then(response => response.json())
                .then(data => {
                    this.nightsCount = data.nights_count;
                    this.totalAmount = data.total_amount;
                })
                .finally(() => { this.loading = false; });
        },
    }));
});
```

```blade
<form x-data="reservationForm({{ $residence->id }})" x-on:change="updateEstimate">
    ...
    <p x-show="nightsCount > 0">{{ __('reservation.nights_summary') }} : <span x-text="nightsCount"></span></p>
</form>
```

- L'estimation affichée côté client (`nightsCount`, `totalAmount`) est **toujours** recalculée et revalidée côté serveur à la soumission réelle de la demande, conformément à [`SECURITY.md`](../../SECURITY.md) §6.

## 5. Le composant Calendrier

Le calendrier de disponibilité est le composant Alpine.js le plus critique de l'application (domaine `Availability`).

- Il reçoit du serveur, au chargement de la page, la liste des périodes bloquées (via une balise `data-*` ou un `<script type="application/json">` injecté par Blade), jamais par un appel Ajax séparé bloquant l'affichage initial.
- Toute action de blocage manuel (entretien, maintenance, usage personnel — voir [`BUSINESS_RULES.md`](../../BUSINESS_RULES.md) §4.2) déclenche un appel Ajax vers l'API interne, puis une mise à jour optimiste de l'affichage, corrigée si la réponse serveur diffère (ex : conflit détecté entre-temps).
- Le calendrier ne permet jamais de sélectionner visuellement une période déjà bloquée : les jours bloqués sont désactivés (`disabled`) au rendu.

## 6. Le composant Notifications

- Les notifications in-app sont chargées au chargement de la page (rendu serveur initial), puis rafraîchies périodiquement via un polling Ajax léger (pas de WebSocket au MVP).
- Le composant affiche un badge de compteur non lu, mis à jour après chaque marquage comme lu.

## 7. Les Charts (statistiques admin)

- Les graphiques du tableau de bord administrateur utilisent une librairie de graphiques légère chargée uniquement sur les pages concernées (pas de chargement global sur tout le site).
- Les données affichées sont pré-calculées côté serveur (endpoint `/api/v1/admin/statistics`, voir [`API_GUIDE.md`](../../API_GUIDE.md) §12) ; le JavaScript ne fait que les représenter visuellement.

## 8. Appels Ajax vers l'API interne

- Tout appel Ajax passe par le wrapper `utils/http.js`, qui injecte systématiquement l'en-tête CSRF et l'en-tête `Accept: application/json` (voir [`API_GUIDE.md`](../../API_GUIDE.md) §4).
- Toute erreur retournée par l'API (format décrit dans [`API_GUIDE.md`](../../API_GUIDE.md) §7) est interceptée et affichée à l'utilisateur de façon compréhensible, jamais laissée en erreur console silencieuse.

## 9. Ce qu'il ne faut jamais faire

- Ne jamais recalculer une règle métier (nombre de journées, montant total, disponibilité) uniquement côté client sans revalidation serveur.
- Ne jamais introduire de dépendance à un framework JS de composants (React, Vue, Svelte) même pour un widget isolé.
- Ne jamais manipuler le DOM directement en dehors du cycle réactif d'Alpine.js (pas de `document.querySelector` dispersé dans le code applicatif).
- Ne jamais stocker de donnée sensible (jeton, information partenaire non publique) dans le `localStorage` du navigateur.
