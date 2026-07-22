import Alpine from 'alpinejs';

import { apiFetch, firstErrorMessage } from '../utils/http.js';

document.addEventListener('alpine:init', () => {
    Alpine.data('reservationRequest', (reservableType, reservableId, dailyRate) => ({
        start_date: '',
        end_date: '',
        loading: false,
        errors: {},
        generalError: '',

        /**
         * Aperçu client uniquement — le montant définitif et faisant foi est
         * recalculé côté serveur par CreerDemandeReservationAction, jamais
         * pris tel quel depuis ce composant (07-javascript-guidelines.md §9).
         */
        get nightsCount() {
            if (!this.start_date || !this.end_date) return 0;
            const diff = (new Date(this.end_date) - new Date(this.start_date)) / 86400000;
            return diff > 0 ? Math.round(diff) : 0;
        },

        get totalAmount() {
            return this.nightsCount * dailyRate;
        },

        async submit() {
            this.loading = true;
            this.errors = {};
            this.generalError = '';

            const result = await apiFetch('/api/v1/reservations', {
                method: 'POST',
                body: JSON.stringify({
                    reservable_type: reservableType,
                    reservable_id: reservableId,
                    start_date: this.start_date,
                    end_date: this.end_date,
                }),
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

            window.location.href = `/reservations/${result.data.data.id}`;
        },
    }));
});
