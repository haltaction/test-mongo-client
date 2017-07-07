<?php

namespace MongoClient\Command;

use MongoClient\DI\ContainerInsertionTrait;

class SelectCommand implements CommandInterface
{
    use ContainerInsertionTrait;

    /**
     * Array of statements of command. Value 1 - if statement is required, 0 if not.
     */
    const STATEMENTS_LIST = [
        'SELECT'    => 1,
        'FROM'      => 1,
        'WHERE'     => 0,
        'ORDER BY'  => 0,
        'SKIP'      => 0,
        'LIMIT'     => 0
    ];

    const WHERE_CONDITIONS = [
        'AND',
        'OR'
    ];

    const CONDITION_OPERATIONS = [
        '<>',
        '>=',
        '<=',
        '=',
        '>',
        '<',
    ];

    const ORDER_TYPES = [
        'ASC',
        'DESC'
    ];

    private $parser;

    /**
     * Run parsing input by this command.
     *
     * @param array $arguments
     * @return string
     */
    public function run(array $arguments)
    {
        $commandText = $this->readInput();
        $container = $this->getContainer();
        $this->parser = $container->getService('query_parser');
        $commandParts = $this->parser->parseQuery(self::STATEMENTS_LIST, $commandText);

        $commandParts = $this->convert($commandParts);
        $query =  $this->createMongoQuery($commandParts);

        return $query;
    }

    /**
     * Return array with arrays of parts for every command statement.
     *
     * @param array $queryParts
     * @return array
     */
    public function convert(array $queryParts)
    {
        $selectFields = explode(',', $queryParts['SELECT']);
        array_walk($selectFields, 'trim');

        $selectFrom = trim($queryParts['FROM']);

        if (isset($queryParts['WHERE'])) {
            $whereText = $queryParts['WHERE'];
            $conditions = $this->parser->parseConditions(self::WHERE_CONDITIONS, $whereText);

            /** parse operations in conditions */
            foreach ($conditions as &$whereCondition) {
                if (!in_array(strtoupper($whereCondition), self::WHERE_CONDITIONS)) {
                    $whereCondition = $this->parser->parseConditions(self::CONDITION_OPERATIONS, $whereCondition);
                }
            }
        }

        if (isset($queryParts['ORDER BY'])) {
            $selectOrders = explode(',', $queryParts['ORDER BY']);
            foreach ($selectOrders as &$orderCondition) {
                $orderCondition = $this->parser->parseConditions(self::ORDER_TYPES, $orderCondition);
            }
        }

        if (isset($queryParts['SKIP'])) {
            $selectSkip = trim($queryParts['SKIP']);
        }

        if (isset($queryParts['LIMIT'])) {
            $selectLimit = trim($queryParts['LIMIT']);
        }

        $result = [
            'SELECT'    => $selectFields,
            'FROM'      => $selectFrom,
            'WHERE'     => $conditions,
            'ORDER BY'  => $selectOrders,
            'SKIP'      => $selectSkip,
            'LIMIT'     => $selectLimit
        ];

        return $result;
    }

    /**
     * Create json query from parsed array.
     *
     * @param array $conditions
     * @return string
     */
    public function createMongoQuery(array $conditions)
    {

        $query = 'db.' . $conditions['FROM'] . '.find(';
        $query .= $this->convertWhere($conditions['WHERE']);

        // select
        $query .= ',{ "' . implode('": 1,"', $conditions['SELECT']) . '": 1})';
        // sort
        $query .= '.sort(' . $this->convertOrder($conditions['ORDER BY']) . ')';
        $query .= '.skip(' . $conditions['SKIP'] . ')';
        $query .= '.skip(' . $conditions['LIMIT'] . ')';
        $query .= ';';

        return $query;
    }

    /**
     * Convert array of order
     *
     * @param array $orderArray
     * @return string
     */
    public function convertOrder(array $orderArray)
    {
        $sort = [];
        foreach ($orderArray as $order) {
            if (!isset($order[1]) || strtoupper($order[1]) === 'ASC') {
                $sort[$order[0]] = 1;
            } else {
                $sort[$order[0]] = -1;
            }
        }
        $sortString = json_encode((object) $sort);

        return $sortString;
    }

    /**
     * Convert array with conditions to json string.
     * Note: convert only linear logical conditions, without grouping by round brackets, etc.
     *
     * @param array $whereConditions
     * @return string
     * @throws \Exception
     */
    protected function convertWhere(array $whereConditions)
    {
        $operatorsConvert = [
            '<>' => '$ne',
            '>=' => '$gte',
            '<=' => '$lte',
            '='  => '$eq',
            '>'  => '$gt',
            '<'  => '$lt',
        ];

        // duplicate last logical operation after last condition. For common structure of parsing.
        if (count($whereConditions) > 3) {
            $whereConditions[] = $whereConditions[count($whereConditions) - 2];
        }

        $сonditions = [];
        $conditionsLength = count($whereConditions);

        for ($i = 0; $i < $conditionsLength; $i++) {
            $current = $whereConditions[$i];

            // convert operators logic
            if (!is_string($current) && is_array($current)) {
                if (count($current) < 3) {
                    throw new \Exception('Invalid condition operation with values: "' . implode('", "', $current) . '"');
                }

                $operation = [];
                $operation[$current[0]] = [strtr($current[1], $operatorsConvert) => $current[2]];
                $whereConditions[$i] = $operation;

                continue;
            }
            $previous = $whereConditions[$i - 1];

            if (strtoupper($current) === 'AND') {
                $сonditions['$and'][] = $previous;
            }

            // 'and' has higher priority than 'or'
            if (strtoupper($current) === 'OR') {
                if (!empty($сonditions['$and'])) {
                    $сonditions['$and'][] = $previous;
                    $сonditions['$or']['$and'] = $сonditions['$and'];
                    $сonditions['$and'] = [];
                } else {
                    $сonditions['$or'][] = $previous;
                }
            }
        }

        // convert to object for json
        $conditionsObject = new \stdClass();
        foreach ($сonditions as $key=>$value) {
            if (empty($value)) {
                continue;
            }
            $conditionsObject->{$key} = [$value];
        }

        $conditionsString = json_encode($conditionsObject);

        return $conditionsString;
    }

    /**
     * Read and return input until ";" find.
     * @return string
     */
    public function readInput()
    {
        $text = '';
        $handle = fopen ("php://stdin","r+");

        while (false !== ($line = fgets($handle))) {
            $text .= trim($line).' ';
            if (stripos($line, ';') !== false) {
                // todo remove write
                fwrite($handle, PHP_EOL . $text . PHP_EOL);
                fclose($handle);
                break;
            }
        }
        $text = rtrim($text, "; ");

        return $text;
    }

}