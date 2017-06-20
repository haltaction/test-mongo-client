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
        $parser = $container->getService('query_parser');
        $commandParts = $parser->parseQuery(self::STATEMENTS_LIST, $commandText);

        $commandParts = $this->convert($commandParts);

        return implode(PHP_EOL, $commandParts);
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

        $parser = $this->getContainer()->getService('query_parser');

        if (isset($queryParts['WHERE'])) {
            $whereText = $queryParts['WHERE'];
            $conditions = $parser->parseConditions(self::WHERE_CONDITIONS, $whereText);

            /** parse operations in conditions */
            foreach ($conditions as &$whereCondition) {
                if (!in_array(strtoupper($whereCondition), self::WHERE_CONDITIONS)) {
                    $whereCondition = $parser->parseConditions(self::CONDITION_OPERATIONS, $whereCondition);
                }
            }
        }

        if (isset($queryParts['ORDER BY'])) {
            $selectOrders = explode(',', $queryParts['ORDER BY']);
            foreach ($selectOrders as &$orderCondition) {
                $orderCondition = $parser->parseConditions(self::ORDER_TYPES, $orderCondition);
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