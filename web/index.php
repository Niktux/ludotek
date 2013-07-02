<?php

require '../src/bootstrap.php';

$app = new Ludo\Application(__DIR__ . '/../config/db.yml');

$app->enableDebug()
    ->enableProfiling();

$app->mount('/games', new Ludo\Controllers\GameControllerProvider());
$app->mount('/authors', new Ludo\Controllers\AuthorControllerProvider());

$app->run();