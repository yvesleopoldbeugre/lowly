# docker/php/Dockerfile — image PHP-FPM de production
#
# Voir docs/engineering/15-deployment.md §3 : n'installe jamais d'outils de
# développement (Xdebug, dépendances Composer de dev). La variante locale
# est Dockerfile.dev (voir ce fichier).

FROM php:8.4-fpm-alpine AS base

# Dépendances système nécessaires aux extensions PHP et à Composer.
RUN apk add --no-cache \
        postgresql-dev \
        libzip-dev \
        libpng-dev \
        libjpeg-turbo-dev \
        freetype-dev \
        icu-dev \
        oniguruma-dev \
        linux-headers \
        $PHPIZE_DEPS \
    && docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_pgsql \
        pgsql \
        bcmath \
        intl \
        gd \
        zip \
        opcache \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del $PHPIZE_DEPS

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY docker/php/php.ini /usr/local/etc/php/conf.d/99-lowly.ini
COPY docker/php/opcache-prod.ini /usr/local/etc/php/conf.d/99-opcache-prod.ini

WORKDIR /var/www/html

# Étape dépendances : tire parti du cache Docker tant que composer.json/lock
# ne changent pas.
COPY composer.json composer.lock ./
RUN composer install \
        --no-dev \
        --no-interaction \
        --no-scripts \
        --no-autoloader \
        --prefer-dist

COPY . .

# --no-scripts : le cache de configuration Laravel ne doit pas être généré au
# moment du build (voir docker-entrypoint.sh), pour ne pas figer dans l'image
# des valeurs d'environnement propres à un seul déploiement.
RUN composer dump-autoload --optimize --no-dev --no-scripts \
    && mkdir -p storage/framework/{cache,sessions,testing,views} storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache \
    && chmod +x docker/php/docker-entrypoint.sh

USER www-data

EXPOSE 9000

ENTRYPOINT ["docker/php/docker-entrypoint.sh"]
CMD ["php-fpm"]
