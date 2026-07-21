#!/bin/sh
# docker/php/docker-entrypoint.sh — voir docs/engineering/15-deployment.md §3.
#
# Le cache de configuration Laravel (config:cache) embarque les valeurs
# d'environnement résolues : il ne doit donc jamais être construit au moment
# du `docker build` (l'image serait alors liée à un seul environnement),
# mais au démarrage du conteneur, une fois le vrai `.env` d'exécution en
# place. Les migrations, elles, restent une étape manuelle du déploiement
# (voir DEPLOYMENT.md §5 et §10), jamais automatique ici.
set -e

php artisan package:discover --ansi
php artisan config:cache
php artisan route:cache
php artisan view:cache

exec "$@"
