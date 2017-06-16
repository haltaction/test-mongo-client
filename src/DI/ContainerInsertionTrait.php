<?php

namespace MongoClient\DI;

trait ContainerInsertionTrait
{
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function getContainer()
    {
        return $this->container;
    }
}