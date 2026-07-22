import Alpine from 'alpinejs';

import { apiFetch, firstErrorMessage } from '../utils/http.js';

document.addEventListener('alpine:init', () => {
    Alpine.data('adminSettingsForm', (key, initialHours) => ({
        hours: initialHours,
        loading: false,
        generalError: '',
        saved: false,

        async save() {
            this.loading = true;
            this.generalError = '';
            this.saved = false;

            const result = await apiFetch('/api/v1/admin/settings', {
                method: 'PATCH',
                body: JSON.stringify({ key, value: { hours: Number(this.hours) } }),
            });

            this.loading = false;

            if (!result.ok) {
                this.generalError = firstErrorMessage(result);
                return;
            }

            this.saved = true;
        },
    }));
});
