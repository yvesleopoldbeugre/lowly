import Alpine from 'alpinejs';

import { apiFetch, firstErrorMessage } from '../utils/http.js';

document.addEventListener('alpine:init', () => {
    Alpine.data('counterOfferResponse', (reservationId, counterOfferId) => ({
        loading: false,
        generalError: '',

        async respond(action) {
            this.loading = true;
            this.generalError = '';

            const result = await apiFetch(
                `/api/v1/reservations/${reservationId}/counter-offers/${counterOfferId}/${action}`,
                { method: 'POST' },
            );

            this.loading = false;

            if (!result.ok) {
                this.generalError = firstErrorMessage(result);
                return;
            }

            window.location.reload();
        },
    }));
});
