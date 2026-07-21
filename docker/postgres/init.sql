-- docker/postgres/init.sql — voir DATABASE.md §13 et docs/engineering/15-deployment.md §3.
--
-- Exécuté automatiquement par l'image officielle postgres à la première
-- initialisation du volume de données (mécanisme /docker-entrypoint-initdb.d).
-- La migration Laravel 2026_07_21_090000_enable_postgres_extensions.php
-- applique la même commande de façon idempotente : ce script sert de
-- garde-fou au niveau infrastructure, indépendant du cycle de migration
-- applicatif.

CREATE EXTENSION IF NOT EXISTS btree_gist;

-- Base dédiée aux tests automatisés (phpunit.xml, TESTING.md §5) : isolée de la
-- base de développement `lowly` pour ne jamais risquer d'écraser des données
-- locales lors d'une suite de tests. Ce script ne s'exécute qu'une fois, à la
-- première initialisation du volume Postgres — pas besoin d'idempotence ici.
CREATE DATABASE lowly_testing;
