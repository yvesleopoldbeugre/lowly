import Alpine from 'alpinejs';

import { apiFetch, firstErrorMessage } from '../utils/http.js';

document.addEventListener('alpine:init', () => {
    Alpine.data('vehicleForm', (vehicleId, initial) => ({
        vehicleId,
        brand: initial.brand ?? '',
        model: initial.model ?? '',
        year: initial.year ?? '',
        plate_number: initial.plate_number ?? '',
        daily_rate: initial.daily_rate ?? '',
        attributes: {
            climatisation: false,
            boite: 'manuelle',
            places: 4,
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
                brand: this.brand,
                model: this.model,
                year: this.year ? Number(this.year) : null,
                plate_number: this.plate_number || null,
                daily_rate: Number(this.daily_rate),
                attributes: this.attributes,
            };

            if (this.vehicleId && submitForValidation) {
                payload.submit_for_validation = true;
            }

            const url = this.vehicleId
                ? `/api/v1/partner/vehicles/${this.vehicleId}`
                : '/api/v1/partner/vehicles';

            const result = await apiFetch(url, {
                method: this.vehicleId ? 'PATCH' : 'POST',
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

            if (!this.vehicleId) {
                window.location.href = `/partner/vehicles/${result.data.data.id}/edit`;
                return;
            }

            this.savedMessage = submitForValidation ? 'Véhicule soumis pour validation.' : 'Véhicule enregistré.';
        },
    }));
});
