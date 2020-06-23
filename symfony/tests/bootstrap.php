<?php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

if (file_exists(dirname(__DIR__).'/config/bootstrap.php')) {
    require dirname(__DIR__).'/config/bootstrap.php';
} elseif (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}

// Clean database, dropping schema, execute migrations and fixtures.
passthru(sprintf('php bin/console d:d:c --if-not-exists --env=test -q'));
passthru(sprintf('php bin/console d:s:d --force --full-database -q --env=test'));
passthru(sprintf('php bin/console d:mi:mi -n --env=test -q'));
passthru(sprintf('php bin/console h:f:l -n --env=test -q'));
