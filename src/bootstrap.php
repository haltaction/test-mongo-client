<?php

require_once __DIR__.'/../vendor/autoload.php';

$configApp = include(__DIR__.'/../config/app.php');
$configCommand = include(__DIR__.'/../config/commands.php');

$app = new MongoClient\Application($configApp, $configCommand);
$app->handle($argv);