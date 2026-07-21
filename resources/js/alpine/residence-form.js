import Alpine from 'alpinejs';

import { apiFetch, firstErrorMessage } from '../utils/http.js';

document.addEventListener('alpine:init', () => {
    Alpine.data('residenceForm', (residenceId, initial) => ({
        residenceId,
        title: initial.title ?? '',
        description: initial.description ?? '',
        address: initial.address ?? '',
        city: initial.city ?? '',
        capacity: initial.capacity ?? 1,
        daily_rate: initial.daily_rate ?? '',
        attributes: {
            wifi: false,
            climatisation: false,
            parking: false,
            cuisine_equipee: false,
            machine_a_laver: false,
            television: false,
            ...(initial.attributes ?? {}),
        },
        loading: false,
        errors: {},
        generalError: '',
        savedMessage: '',

        async submit(submitForValidation = false) {
            this.loading = true;
            this.errors = {};
            this.generalError = '';
            this.savedMessage = '';

            const payload = {
                title: this.title,
                description: this.description,
                address: this.address,
                city: this.city,
                capacity: Number(this.capacity),
                daily_rate: Number(this.daily_rate),
                attributes: this.attributes,
            };

            if (this.residenceId && submitForValidation) {
                payload.submit_for_validation = true;
            }

            const url = this.residenceId
                ? `/api/v1/partner/residences/${this.residenceId}`
                : '/api/v1/partner/residences';

            const result = await apiFetch(url, {
                method: this.residenceId ? 'PATCH' : 'POST',
                body: JSON.stringify(payload),
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

            if (!this.residenceId) {
                window.location.href = `/partner/residences/${result.data.data.id}/edit`;
                return;
            }

            this.savedMessage = submitForValidation ? 'Résidence soumise pour validation.' : 'Résidence enregistrée.';
        },
    }));
});
