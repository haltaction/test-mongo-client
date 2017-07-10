<?php

namespace MongoClient\DB;

use Exception;
use MongoClient\DI\ContainerInsertionTrait;
use MongoDB\Driver\Manager;
use MongoDB\Driver\Query;

class MongoManager
{
    use ContainerInsertionTrait;

    private $connectionConfig;

    /**
     * Load config and create uri for mongo connection.
     *
     * @return string
     * @throws Exception
     */
    protected function createConnectionURI()
    {
        $dbConfig = $this->container->getService('config_keeper')->getConfig();
        if (isset($dbConfig['database']) && isset($dbConfig['database']['DATABASE'])) {
            $this->connectionConfig = $dbConfig['database'];

            return 'mongodb://' . $this->connectionConfig['HOST'] . ':' . $this->connectionConfig['PORT'];
        }

        throw new Exception('Config part \'database\' not setted up!');
    }

    /**
     * Setup, execute query and return array result.
     *
     * @param array $query
     * @return mixed
     */
    public function execute(array $query)
    {
        $filter = $query['find'];
        $options = [
            'limit' => (integer) $query['limit'],
            'skip' => (integer) $query['skip'],
            'sort' => $query['sort'],
        ];
        if (!empty($query['fields'])) {
            $options = array_merge($options, ['projection' => $query['fields']]);
        }


        $manager = new Manager($this->createConnectionURI());
        $mongoQuery = new Query($filter, $options);
        $namespace = $this->connectionConfig['DATABASE'] . '.' . $query['collection'];
        $cursor = $manager->executeQuery($namespace, $mongoQuery);

        return $cursor->toArray();
    }
}