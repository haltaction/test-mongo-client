<?php

namespace MongoClient;


class Application
{
    private $config;

    private $commands;

    private $services = [];

    public function __construct(array $configApp, array $configCommand)
    {
        $this->config = $configApp;
        $this->commands = $configCommand;
        $this->createServices();
    }

    public function createServices()
    {
        $services = $this->config['services'];
        if (empty($services)) {
            return;
        }

        foreach ($services as $key=>$servicePath) {
            $this->services[$key] = new $servicePath;
        }
    }

    public function handle($arguments)
    {
        $this->services['console']->handle($arguments, $this->commands);
    }


}