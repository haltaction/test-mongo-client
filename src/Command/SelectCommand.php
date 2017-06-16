<?php

namespace MongoClient\Command;

use MongoClient\DI\ContainerInsertionTrait;

class SelectCommand implements CommandInterface
{
    use ContainerInsertionTrait;

    const STATEMENTS_LIST = [
        'SELECT',
        'FROM',
        'WHERE',
        'ORDER BY',
        'SKIP',
        'LIMIT',
    ];

    public function run(array $arguments)
    {
        $commandText = $this->readInput();
        $container = $this->getContainer();
        $parser = $container->getService('query_parser');
        $parser->parseQuery(self::STATEMENTS_LIST, $commandText);
        // validate text
        // parse text to mongo query
        // execute query

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
            if(stripos($line, ';') !== false) {
                fwrite($handle, PHP_EOL . $text . PHP_EOL);
                fclose($handle);
                break;
//                exit('bye' . PHP_EOL);
            }
        }

        return $text;
    }

}