<?php

require_once __DIR__.'/../vendor/autoload.php';

$configApp = include(__DIR__.'/../config/app.php');
$configServices = include(__DIR__.'/../config/services.php');
$configCommand = include(__DIR__.'/../config/commands.php');

$container = new MongoClient\DI\Container($configServices);
$app = new MongoClient\Application($container, $configApp, $configCommand);
$app->handle($argv);