<?php

/**
 * Bootstrap de test dédié — voir phpunit.xml.
 *
 * Le conteneur Docker expose APP_ENV/CACHE_STORE/DB_* comme variables
 * d'environnement OS réelles (docker-compose.yml `env_file`, override
 * APP_ENV=local). Ces valeurs de développement doivent être remplacées
 * avant même que Laravel ne démarre, sinon la suite de tests s'exécute
 * silencieusement contre la base et le cache de développement — voir
 * docs/engineering/12-testing-guidelines.md §9 et TESTING.md §5.
 */
$overrides = [
    'APP_ENV' => 'testing',
    'CACHE_STORE' => 'array',
    'DB_CONNECTION' => 'pgsql',
    'DB_DATABASE' => 'lowly_testing',
    'DB_URL' => '',
    'SESSION_DRIVER' => 'array',
    'QUEUE_CONNECTION' => 'sync',
    'MAIL_MAILER' => 'array',
    'BROADCAST_CONNECTION' => 'null',
    'BCRYPT_ROUNDS' => '4',
];

foreach ($overrides as $key => $value) {
    putenv("{$key}={$value}");
    $_ENV[$key] = $value;
    $_SERVER[$key] = $value;
}

require __DIR__.'/../vendor/autoload.php';
