import Alpine from 'alpinejs';

import { apiFetch, firstErrorMessage } from '../utils/http.js';

document.addEventListener('alpine:init', () => {
    Alpine.data('adminListingReview', (type, id) => ({
        loading: false,
        generalError: '',
        showRejectForm: false,
        reason: '',
        errors: {},

        async validate() {
            await this.post(`/api/v1/admin/listings/${type}/${id}/validate`);
        },

        async reject() {
            this.errors = {};

            if (!this.reason.trim()) {
                this.errors.reason = 'Le motif est obligatoire.';
                return;
            }

            await this.post(`/api/v1/admin/listings/${type}/${id}/reject`, { reason: this.reason });
        },

        async post(url, body) {
            this.loading = true;
            this.generalError = '';

            const result = await apiFetch(url, {
                method: 'POST',
                ...(body ? { body: JSON.stringify(body) } : {}),
            });

            this.loading = false;

            if (!result.ok) {
                this.generalError = firstErrorMessage(result);
                return;
            }

            window.location.reload();
        },
    }));
});
