/**
 * Wrapper fetch conforme à docs/engineering/07-javascript-guidelines.md §8 :
 * injecte systématiquement le jeton CSRF et l'en-tête Accept, et normalise
 * les erreurs de l'API interne (API_GUIDE.md §7) en objet exploitable par
 * les composants Alpine.
 */

function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
}

/**
 * Émet une requête vers l'API interne (`/api/v1/...`) et retourne
 * `{ ok, status, data }`. `data` est le corps JSON déjà décodé (succès ou
 * `{ error: { code, message, details } }` en cas d'échec) ; ne lève jamais
 * d'exception pour un statut HTTP non-2xx, afin que l'appelant gère
 * l'affichage d'erreur explicitement.
 */
export async function apiFetch(url, options = {}) {
    const isFormData = options.body instanceof FormData;

    const response = await fetch(url, {
        ...options,
        headers: {
            Accept: 'application/json',
            'X-CSRF-TOKEN': csrfToken(),
            ...(isFormData ? {} : { 'Content-Type': 'application/json' }),
            ...options.headers,
        },
    });

    const data = response.status === 204 ? null : await response.json().catch(() => null);

    return { ok: response.ok, status: response.status, data };
}

/**
 * Message d'erreur lisible à partir d'une réponse `apiFetch` en échec.
 * Priorité aux erreurs de validation par champ (422), sinon le message
 * générique de l'enveloppe d'erreur.
 */
export function firstErrorMessage(result) {
    const details = result.data?.error?.details;

    if (details && typeof details === 'object') {
        const firstField = Object.values(details)[0];
        if (Array.isArray(firstField) && firstField.length > 0) {
            return firstField[0];
        }
    }

    return result.data?.error?.message ?? 'Une erreur est survenue. Veuillez réessayer.';
}
