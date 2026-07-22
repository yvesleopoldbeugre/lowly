import Alpine from 'alpinejs';

import { apiFetch, firstErrorMessage } from '../utils/http.js';

/**
 * Composant partagé résidences/véhicules — upload et suppression de photos.
 * `uploadUrl` est la racine .../photos (résidence ou véhicule).
 */
document.addEventListener('alpine:init', () => {
    Alpine.data('photoManager', (initialPhotos, uploadUrl) => ({
        photos: initialPhotos,
        uploading: false,
        error: '',

        async upload(event) {
            const file = event.target.files[0];
            if (!file) return;

            this.uploading = true;
            this.error = '';

            const body = new FormData();
            body.append('photo', file);

            const result = await apiFetch(uploadUrl, { method: 'POST', body });

            this.uploading = false;
            event.target.value = '';

            if (!result.ok) {
                this.error = firstErrorMessage(result);
                return;
            }

            this.photos.push(result.data.data);
        },

        async remove(photoId) {
            const result = await apiFetch(`${uploadUrl}/${photoId}`, { method: 'DELETE' });

            if (!result.ok) {
                this.error = firstErrorMessage(result);
                return;
            }

            this.photos = this.photos.filter((photo) => photo.id !== photoId);
        },
    }));
});
