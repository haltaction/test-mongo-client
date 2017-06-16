<?php

namespace MongoClient\DI;

use Exception;

class Container
{
    private $services = [];

    /**
     * Container constructor. Create all services at first load.
     *
     * @param array $servicesList
     */
    public function __construct(array $servicesList)
    {
        $this->createServices($servicesList);
    }

    /**
     * Create objects of services and set them in services property.
     *
     * @param array $servicesList
     */
    public function createServices(array $servicesList)
    {
        if (empty($servicesList)) {
            return;
        }

        foreach ($servicesList as $key => $servicePath) {
            $this->services[$key] = new $servicePath($this);
        }
    }

    /**
     * Return created object by key name.
     *
     * @param $name
     *
     * @return object
     * @throws Exception
     */
    public function getService(string $name)
    {
        if (!isset($this->services[$name])) {
            throw new Exception("Service '$name' is not defined!");
        }

        return $this->services[$name];
    }
}