import Alpine from 'alpinejs';

import { apiFetch, firstErrorMessage } from '../utils/http.js';

/**
 * Déconnexion — utilisée par le bouton présent dans les deux layouts
 * (guest et app), pas seulement les pages d'authentification.
 */
window.lowlyLogout = async function lowlyLogout() {
    await apiFetch('/api/v1/auth/logout', { method: 'POST' });
    window.location.href = '/';
};

function redirectAfterAuth(user) {
    window.location.href = user.attributes.role === 'partner' ? '/partner/dashboard' : '/';
}

/** @param {Record<string, string[]>} details */
function fieldErrors(details) {
    return Object.fromEntries(Object.entries(details ?? {}).map(([field, messages]) => [field, messages[0]]));
}

document.addEventListener('alpine:init', () => {
    Alpine.data('authTabs', (initialTab) => ({
        tab: initialTab,
        setTab(tab) {
            this.tab = tab;
            window.history.replaceState({}, '', tab === 'connexion' ? '/login' : '/register');
        },
    }));

    Alpine.data('loginForm', () => ({
        email: '',
        password: '',
        remember: false,
        loading: false,
        errors: {},
        generalError: '',

        async submit() {
            this.loading = true;
            this.errors = {};
            this.generalError = '';

            const result = await apiFetch('/api/v1/auth/login', {
                method: 'POST',
                body: JSON.stringify({ email: this.email, password: this.password, remember: this.remember }),
            });

            this.loading = false;

            if (!result.ok) {
                if (result.status === 422) {
                    this.errors = fieldErrors(result.data.error.details);
                } else {
                    this.generalError = firstErrorMessage(result);
                }
                return;
            }

            redirectAfterAuth(result.data.data);
        },
    }));

    Alpine.data('registerForm', () => ({
        full_name: '',
        email: '',
        password: '',
        password_confirmation: '',
        wants_partner: false,
        loading: false,
        errors: {},
        generalError: '',

        async submit() {
            this.loading = true;
            this.errors = {};
            this.generalError = '';

            const result = await apiFetch('/api/v1/auth/register', {
                method: 'POST',
                body: JSON.stringify({
                    full_name: this.full_name,
                    email: this.email,
                    password: this.password,
                    password_confirmation: this.password_confirmation,
                    wants_partner: this.wants_partner,
                }),
            });

            this.loading = false;

            if (!result.ok) {
                if (result.status === 422) {
                    this.errors = fieldErrors(result.data.error.details);
                } else {
                    this.generalError = firstErrorMessage(result);
                }
                return;
            }

            redirectAfterAuth(result.data.data);
        },
    }));
});
