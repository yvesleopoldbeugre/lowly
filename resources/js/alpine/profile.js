import Alpine from 'alpinejs';

import { apiFetch, firstErrorMessage } from '../utils/http.js';

document.addEventListener('alpine:init', () => {
    Alpine.data('updateProfileForm', (initial) => ({
        full_name: initial.full_name ?? '',
        email: initial.email ?? '',
        phone: initial.phone ?? '',
        loading: false,
        saved: false,
        errors: {},
        generalError: '',

        async submit() {
            this.loading = true;
            this.saved = false;
            this.errors = {};
            this.generalError = '';

            const result = await apiFetch('/api/v1/me', {
                method: 'PATCH',
                body: JSON.stringify({ full_name: this.full_name, email: this.email, phone: this.phone }),
            });

            this.loading = false;

            if (!result.ok) {
                if (result.status === 422) {
                    this.errors = Object.fromEntries(
                        Object.entries(result.data.error.details ?? {}).map(([field, messages]) => [field, messages[0]]),
                    );
                } else {
                    this.generalError = firstErrorMessage(result);
                }
                return;
            }

            this.saved = true;
        },
    }));
});
