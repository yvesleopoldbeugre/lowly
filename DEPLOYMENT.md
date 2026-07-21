# DEPLOYMENT.md — Déploiement et Infrastructure LOWLY

## Table des matières

1. [Vue d'ensemble de l'infrastructure](#1-vue-densemble-de-linfrastructure)
2. [Environnements](#2-environnements)
3. [Conteneurisation Docker](#3-conteneurisation-docker)
4. [Nginx](#4-nginx)
5. [Base de données et migrations en production](#5-base-de-données-et-migrations-en-production)
6. [CI/CD](#6-cicd)
7. [SSL / TLS](#7-ssl--tls)
8. [Monitoring](#8-monitoring)
9. [Sauvegardes (Backups)](#9-sauvegardes-backups)
10. [Procédure de mise en production](#10-procédure-de-mise-en-production)
11. [Procédure de rollback](#11-procédure-de-rollback)

---

## 1. Vue d'ensemble de l'infrastructure

```
                     ┌─────────────────┐
                     │      Internet      │
                     └────────┬────────┘
                              │  HTTPS (TLS)
                     ┌────────▼────────┐
                     │       Nginx        │  (reverse proxy, TLS termination)
                     └────────┬────────┘
                              │
                     ┌────────▼────────┐
                     │  Application       │
                     │  Laravel (PHP-FPM) │
                     └───┬────────┬────┘
                         │        │
              ┌──────────▼──┐  ┌──▼───────────┐
              │  PostgreSQL 17 │  │    Redis        │
              └────────────────┘  └─────────────────┘
```

Chaque composant tourne dans son propre conteneur Docker, orchestré par Docker Compose.

## 2. Environnements

| Environnement | Objectif | Données |
|---|---|---|
| `local` | Développement sur poste développeur | Données de seed fictives |
| `staging` | Validation fonctionnelle avant production | Données anonymisées ou fictives réalistes |
| `production` | Environnement client final | Données réelles |

Chaque environnement dispose de sa propre configuration (`.env`), de ses propres identifiants de base de données, et ne partage jamais de secrets avec un autre environnement.

## 3. Conteneurisation Docker

Services Docker Compose type :

```yaml
services:
  app:
    build: ./docker/php
    volumes:
      - ./:/var/www/html
    depends_on:
      - postgres
      - redis

  nginx:
    image: nginx:stable
    ports:
      - "8000:80"
    volumes:
      - ./docker/nginx:/etc/nginx/conf.d
    depends_on:
      - app

  postgres:
    image: postgres:17
    environment:
      POSTGRES_DB: lowly
      POSTGRES_USER: lowly
      POSTGRES_PASSWORD: ${DB_PASSWORD}
    volumes:
      - lowly-db-data:/var/lib/postgresql/data

  redis:
    image: redis:7

volumes:
  lowly-db-data:
```

Principes :

- l'image de production ne contient jamais d'outils de développement (Xdebug, etc.) ;
- les volumes de données (`postgres`) sont persistants et sauvegardés (voir §9) ;
- aucune donnée de configuration sensible n'est intégrée en dur dans l'image Docker : elle est injectée par variables d'environnement au démarrage du conteneur.

### 3.1 Implémentation réelle

La topologie ci-dessus est implémentée dans [`docker-compose.yml`](./docker-compose.yml) (`docker/php/Dockerfile`, `docker/nginx/default.conf`, `docker/postgres/init.sql`), avec deux services supplémentaires non représentés dans le schéma simplifié ci-dessus mais requis par [`ARCHITECTURE.md`](./ARCHITECTURE.md) §10 :

- `queue` — exécute `php artisan queue:work`, pour les jobs asynchrones (notifications, traitement des photos, expiration des contre-propositions) ;
- `scheduler` — exécute `php artisan schedule:run` en boucle chaque minute, notamment pour l'expiration automatique des contre-propositions ([`BUSINESS_RULES.md`](./BUSINESS_RULES.md) §6.2).

### 3.2 Développement local

En environnement `local`, [`docker-compose.override.yml`](./docker-compose.override.yml) est chargé automatiquement en complément de `docker-compose.yml` (aucune option `-f` requise) : les services PHP basculent sur `docker/php/Dockerfile.dev` (Xdebug, code monté en volume pour rechargement immédiat), et un service `node` supplémentaire sert les assets via le serveur de développement Vite. En `staging`/`production`, ne déployer que `docker-compose.yml` pour ignorer cet override.

```bash
# Démarrage local
docker compose up -d
docker compose exec app composer install
docker compose exec app php artisan migrate --seed
```

## 4. Nginx

Nginx assure la terminaison TLS et le reverse proxy vers PHP-FPM. Configuration type simplifiée :

```nginx
server {
    listen 443 ssl;
    server_name lowly.example.com;

    ssl_certificate     /etc/ssl/certs/lowly.crt;
    ssl_certificate_key /etc/ssl/private/lowly.key;

    root /var/www/html/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass app:9000;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }

    location ~ /\.(?!well-known) {
        deny all;
    }
}
```

## 5. Base de données et migrations en production

- Les migrations sont exécutées de manière contrôlée lors du déploiement (`php artisan migrate --force`), jamais automatiquement au démarrage du conteneur applicatif sans supervision.
- Toute migration structurante (ajout de contrainte, changement de type) sur une table volumineuse doit être testée au préalable en `staging` avec un volume de données représentatif.
- Les seeders de démonstration (voir [`DATABASE.md`](./DATABASE.md) §14) ne sont **jamais** exécutés en `production`.

## 6. CI/CD

Pipeline standard déclenché à chaque Pull Request et à chaque fusion sur la branche principale :

```
1. Installation des dépendances (Composer, npm)
2. Lint (Laravel Pint)
3. Analyse statique (PHPStan/Larastan)
4. Exécution de la suite de tests (Pest/PHPUnit)
5. Build des assets (npm run build)
6. Build de l'image Docker
7. (Sur branche principale uniquement) Déploiement automatique en staging
8. (Sur tag de release) Déploiement en production après validation manuelle
```

Le détail exact de la configuration CI (fichier, étapes précises, secrets) est documenté dans `docs/engineering/15-deployment.md`.

## 7. SSL / TLS

- Certificats gérés via une autorité de certification automatisée (ex : Let's Encrypt) en `staging` et `production`.
- Renouvellement automatique des certificats, supervisé par une alerte en cas d'échec.
- Redirection systématique de tout trafic HTTP vers HTTPS.

## 8. Monitoring

| Aspect surveillé | Mécanisme |
|---|---|
| Disponibilité de l'application | Sonde de santé HTTP (`/up` ou équivalent) |
| Erreurs applicatives | Agrégation des logs Laravel, alerte sur erreurs 500 |
| Performance des requêtes base de données | Suivi des requêtes lentes PostgreSQL |
| Files d'attente Redis | Suivi de la taille des files et des jobs échoués |
| Certificats TLS | Alerte avant expiration |

## 9. Sauvegardes (Backups)

- Sauvegarde automatique quotidienne de la base de données PostgreSQL, avec rétention définie (ex : 30 jours glissants).
- Sauvegarde régulière des fichiers de stockage (photos, documents partenaires).
- Test périodique de restauration des sauvegardes, pour garantir leur validité réelle (une sauvegarde non testée n'est pas une sauvegarde fiable).

## 10. Procédure de mise en production

```
1. Merge validé sur la branche principale (voir docs/engineering/13-git-workflow.md)
2. Déploiement automatique en staging
3. Validation fonctionnelle manuelle en staging
4. Création d'un tag de release
5. Déploiement en production via le pipeline CI/CD
6. Exécution contrôlée des migrations
7. Vérification post-déploiement (sonde de santé, logs, parcours critiques)
```

## 11. Procédure de rollback

En cas d'anomalie critique détectée après mise en production :

```
1. Décision de rollback (responsable technique)
2. Redéploiement de la version précédente de l'image applicative
3. Si la migration en cours est réversible : exécution de la migration down
4. Si la migration n'est pas réversible sans perte de données : correction ciblée
   plutôt que rollback de schéma (documenté au cas par cas)
5. Vérification post-rollback
6. Post-mortem documenté
```
