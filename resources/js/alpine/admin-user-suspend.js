import Alpine from 'alpinejs';

import { apiFetch, firstErrorMessage } from '../utils/http.js';

document.addEventListener('alpine:init', () => {
    Alpine.data('adminUserSuspend', (userId) => ({
        loading: false,

        async suspend() {
            this.loading = true;

            const result = await apiFetch(`/api/v1/admin/users/${userId}/suspend`, { method: 'PATCH' });

            this.loading = false;

            if (!result.ok) {
                window.alert(firstErrorMessage(result));
                return;
            }

            window.location.reload();
        },
    }));
});
