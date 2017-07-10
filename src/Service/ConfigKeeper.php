<?php

namespace MongoClient\Service;

class ConfigKeeper
{
    private $config;

    /**
     * @param array $config
     */
    public function loadConfig(array $config)
    {
        $this->config = $config;
    }

    /**
     * @return array $config
     */
    public function getConfig()
    {
        return $this->config;
    }
}