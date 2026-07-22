import Alpine from 'alpinejs';

import { apiFetch, firstErrorMessage } from '../utils/http.js';

document.addEventListener('alpine:init', () => {
    Alpine.data('adminPartnerReview', (partnerId) => ({
        loading: false,
        generalError: '',
        showRejectForm: false,
        notes: '',

        async validate() {
            await this.post(`/api/v1/admin/partners/${partnerId}/validate`);
        },

        async reject() {
            await this.post(`/api/v1/admin/partners/${partnerId}/reject`, {
                notes: this.notes || null,
            });
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
