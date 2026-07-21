# 15 — Deployment (détail technique)

## Table des matières

1. [Portée du document](#1-portée-du-document)
2. [Détail du pipeline CI/CD](#2-détail-du-pipeline-cicd)
3. [Structure des images Docker](#3-structure-des-images-docker)
4. [Configuration Nginx détaillée](#4-configuration-nginx-détaillée)
5. [Gestion des secrets en CI/CD](#5-gestion-des-secrets-en-cicd)
6. [Monitoring — implémentation](#6-monitoring--implémentation)
7. [Sauvegardes — implémentation](#7-sauvegardes--implémentation)
8. [Rotation des certificats et secrets](#8-rotation-des-certificats-et-secrets)

---

## 1. Portée du document

Ce document complète [`DEPLOYMENT.md`](../../DEPLOYMENT.md) (racine) avec le détail d'implémentation du pipeline et de l'infrastructure.

## 2. Détail du pipeline CI/CD

```yaml
# Exemple simplifié de pipeline (à adapter à l'outil CI retenu)
stages:
  - install
  - quality
  - test
  - build
  - deploy

install:
  script:
    - composer install --no-interaction --prefer-dist
    - npm ci

quality:
  script:
    - ./vendor/bin/pint --test
    - ./vendor/bin/phpstan analyse

test:
  script:
    - php artisan config:cache --env=testing
    - ./vendor/bin/pest --coverage --min=80

build:
  script:
    - npm run build
    - docker build -t lowly-app:${CI_COMMIT_SHA} .

deploy_staging:
  script:
    - docker push lowly-app:${CI_COMMIT_SHA}
    - ssh staging "docker compose pull && docker compose up -d && php artisan migrate --force"
  only:
    - main

deploy_production:
  script:
    - ssh production "docker compose pull && docker compose up -d && php artisan migrate --force"
  when: manual
  only:
    - tags
```

Le déploiement en `production` reste une étape **manuelle** déclenchée après validation en `staging` (voir [`DEPLOYMENT.md`](../../DEPLOYMENT.md) §10), jamais automatique sur simple merge.

## 3. Structure des images Docker

```
docker/
├── php/
│   ├── Dockerfile           (image PHP-FPM 8.4, extensions requises)
│   └── php.ini
├── nginx/
│   └── default.conf
└── postgres/
    └── init.sql              (extensions PostgreSQL requises : btree_gist, etc.)
```

Le `Dockerfile` de production n'installe jamais d'outils de développement (Xdebug, Faker en dépendance de production) — une image `Dockerfile.dev` distincte est utilisée en environnement `local` si nécessaire.

Le paquet système `linux-headers` est requis dans les deux images (`Dockerfile` et `Dockerfile.dev`), avant `pecl install redis`, en plus des dépendances `*-dev` habituelles : sans lui, la compilation de l'extension PECL `redis` échoue sur `php:8.4-fpm-alpine` avec l'erreur `rtnetlink.h is required`.

## 4. Configuration Nginx détaillée

En complément de la configuration de base ([`DEPLOYMENT.md`](../../DEPLOYMENT.md) §4), les en-têtes de sécurité définis dans `10-security-guidelines.md` §10 sont ajoutés au niveau du bloc `server` :

```nginx
add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
add_header X-Content-Type-Options "nosniff" always;
add_header X-Frame-Options "DENY" always;
add_header Referrer-Policy "strict-origin-when-cross-origin" always;
```

## 5. Gestion des secrets en CI/CD

- Les secrets (identifiants de base de données, clé d'application, identifiants SSH de déploiement) sont stockés dans le gestionnaire de secrets natif de l'outil CI, jamais dans le fichier de pipeline lui-même.
- Chaque environnement (`staging`, `production`) dispose de son propre jeu de secrets, sans partage entre environnements.

## 6. Monitoring — implémentation

| Composant | Outil indicatif |
|---|---|
| Sonde de santé applicative | Route `/up` Laravel standard, vérifiée par un check externe |
| Agrégation des logs | Centralisation des logs Laravel (fichier ou service externe) |
| Alerting | Notification (email/Slack) sur erreur 500 répétée, sonde de santé en échec, ou certificat TLS proche de l'expiration |
| Suivi des requêtes lentes PostgreSQL | Activation de `log_min_duration_statement` au-delà d'un seuil configuré |

## 7. Sauvegardes — implémentation

```bash
# Exemple de sauvegarde quotidienne PostgreSQL (exécutée via cron ou tâche planifiée du conteneur)
pg_dump -Fc -h postgres -U lowly lowly > /backups/lowly_$(date +%Y%m%d).dump

# Rétention : suppression des sauvegardes de plus de 30 jours
find /backups -name "lowly_*.dump" -mtime +30 -delete
```

Un test de restauration est exécuté périodiquement sur un environnement isolé (jamais directement sur `staging` ou `production`) pour valider l'intégrité réelle des sauvegardes, conformément à [`DEPLOYMENT.md`](../../DEPLOYMENT.md) §9.

## 8. Rotation des certificats et secrets

- Certificats TLS renouvelés automatiquement (ex : client ACME pour Let's Encrypt), avec alerte en cas d'échec de renouvellement.
- La clé d'application Laravel (`APP_KEY`) n'est jamais régénérée en production sans procédure de migration des données chiffrées existantes (sessions, valeurs chiffrées en base) — toute rotation est planifiée et documentée.
- Les identifiants de base de données sont rotés selon une politique définie par l'équipe, avec mise à jour coordonnée des secrets CI/CD et de la configuration applicative.
