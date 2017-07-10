<?php

namespace MongoClient;

use MongoClient\DI\ContainerInsertionTrait;

/**
 * Class Application.
 */
class Application
{
    use ContainerInsertionTrait {
        __construct as containerConstruct;
    }

    private $commands;

    /**
     * Application constructor. Set simple container and config arrays.
     *
     * @param $container /MongoClient/DI/Container
     * @param array $configApp
     * @param array $configCommand
     */
    public function __construct($container, array $configApp, array $configCommand)
    {
        $this->containerConstruct($container);
        $this->getContainer()->getService('config_keeper')->loadConfig($configApp);
        $this->commands = $configCommand;
    }

    /**
     * Handle command.
     *
     * @param $arguments
     */
    public function handle($arguments)
    {
        $container = $this->getContainer();
        $container->getService('console')->handle($arguments, $this->commands);
    }
}