<?php

namespace MongoClient;

use MongoClient\DI\ContainerInsertionTrait;

/**
 * Class Application
 * @package MongoClient
 */
class Application
{
    use ContainerInsertionTrait {
        __construct as containerConstruct;
    }

    private $config;

    private $commands;

    /**
     * Application constructor. Set simple container and config arrays.
     *
     * @param $container
     * @param array $configApp
     * @param array $configCommand
     */
    public function __construct($container, array $configApp, array $configCommand)
    {
        $this->containerConstruct($container);
        $this->config = $configApp;
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