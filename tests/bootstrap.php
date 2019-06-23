<?php

require_once __DIR__ .'/../vendor/autoload.php';

exec('./bin/console doctrine:migrations:up-to-date', $result, $isntUpToDate);

if($isntUpToDate) {
    echo shell_exec("./bin/console doctrine:database:drop --force");
    echo shell_exec("./bin/console doctrine:database:create");
    echo shell_exec("./bin/console doctrine:migrations:migrate -n");
}
