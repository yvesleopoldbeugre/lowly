import Alpine from 'alpinejs';

import { apiFetch, firstErrorMessage } from '../utils/http.js';

document.addEventListener('alpine:init', () => {
    Alpine.data('availabilityCalendar', (blockableType, blockableId) => ({
        showModal: false,
        origin: 'entretien',
        start_date: '',
        end_date: '',
        loading: false,
        errors: {},
        generalError: '',

        async submitBlock() {
            this.loading = true;
            this.errors = {};
            this.generalError = '';

            const result = await apiFetch('/api/v1/partner/availability-blocks', {
                method: 'POST',
                body: JSON.stringify({
                    blockable_type: blockableType,
                    blockable_id: blockableId,
                    start_date: this.start_date,
                    end_date: this.end_date,
                    origin: this.origin,
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

            window.location.reload();
        },

        async deleteBlock(blockId) {
            const result = await apiFetch(`/api/v1/partner/availability-blocks/${blockId}`, { method: 'DELETE' });

            if (!result.ok) {
                window.alert(firstErrorMessage(result));
                return;
            }

            window.location.reload();
        },
    }));
});
