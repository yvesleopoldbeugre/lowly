import { apiFetch } from '../utils/http.js';

/**
 * Déconnexion — utilisée par le bouton présent dans les deux layouts
 * (guest et app), pas seulement les pages d'authentification.
 */
window.lowlyLogout = async function lowlyLogout() {
    await apiFetch('/api/v1/auth/logout', { method: 'POST' });
    window.location.href = '/';
};
