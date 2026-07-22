import Alpine from 'alpinejs';

import { apiFetch, firstErrorMessage } from '../utils/http.js';

document.addEventListener('alpine:init', () => {
    Alpine.data('partnerReservationResponse', (reservationId) => ({
        loading: false,
        generalError: '',
        showCounterOfferForm: false,
        proposed_reservable_type: 'residence',
        proposed_reservable_id: '',
        start_date: '',
        end_date: '',
        errors: {},

        async accept() {
            await this.post(`/api/v1/partner/reservations/${reservationId}/accept`);
        },

        async reject() {
            await this.post(`/api/v1/partner/reservations/${reservationId}/reject`);
        },

        async submitCounterOffer() {
            this.errors = {};
            await this.post(`/api/v1/partner/reservations/${reservationId}/counter-offer`, {
                proposed_reservable_type: this.proposed_reservable_type,
                proposed_reservable_id: this.proposed_reservable_id,
                ...(this.start_date ? { start_date: this.start_date } : {}),
                ...(this.end_date ? { end_date: this.end_date } : {}),
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
                if (result.status === 422) {
                    this.errors = Object.fromEntries(
                        Object.entries(result.data.error.details ?? {}).map(([field, messages]) => [field, messages[0]]),
                    );
                } else {
                    this.generalError = firstErrorMessage(result);
                }
                return;
            }

            window.location.reload();
        },
    }));
});
