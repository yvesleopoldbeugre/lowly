import Alpine from 'alpinejs';

import { apiFetch } from '../utils/http.js';

document.addEventListener('alpine:init', () => {
    Alpine.data('notificationRead', (notificationId, initiallyRead) => ({
        read: initiallyRead,

        async markRead() {
            const result = await apiFetch(`/api/v1/notifications/${notificationId}/read`, { method: 'PATCH' });

            if (result.ok) {
                this.read = true;
            }
        },
    }));
});
