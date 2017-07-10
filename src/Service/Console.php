<?php

namespace MongoClient\Service;

use Exception;
use MongoClient\DI\ContainerInsertionTrait;

/**
 * Class Console.
 */
class Console implements ConsoleInterface
{
    use ContainerInsertionTrait;

    const COMMAND_NAMESPACE = 'MongoClient\\Command\\';

    /**
     * Handle received command.
     *
     * @param $arguments
     * @param $commandList
     */
    public function handle($arguments, $commandList)
    {
        $commandName = $this->filterCommandName($arguments);
        try {
            $className = $this->findCommand($commandName, $commandList);
            $command = $this->createCommand($className);
            $commandResult = $command->run($arguments);
            $this->showResponse($commandResult);
        } catch (Exception $e) {
            $this->showResponse($e->getMessage(), 'error');
        }
    }

    /**
     * Create instance for command.
     *
     * @param $className
     *
     * @return mixed
     */
    public function createCommand($className)
    {
        // todo move class creation from here
        $className = self::COMMAND_NAMESPACE.$className;

        return new $className($this->getContainer());
    }

    /**
     * Return name of console command from array of arguments.
     *
     * @param $arguments
     *
     * @return array
     */
    public function filterCommandName($arguments)
    {
        if (isset($arguments[1])) {
            return $arguments[1];
        }

        return '';
    }

    /**
     * Find command name in list and return class name for it.
     *
     * @param $commandName
     * @param $commandList
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function findCommand($commandName, $commandList)
    {
        $commandName = strtolower($commandName);
        if (!in_array($commandName, array_keys($commandList))) {
            throw new Exception('Command not found! Try \'bin/mongo-client help\' for more information.', 404);
        }

        return $commandList[$commandName];
    }

    /**
     * Show result of command in terminal.
     *
     * @param $text
     * @param string $type
     */
    public function showResponse($text, $type = 'text')
    {
        $colors = [
            'text'  => 0,
            'error' => 31,
            'info'  => 34
        ];
        $text = "\033[$colors[$type]m" . $text . "\033[0m";

        exit($text.PHP_EOL);
    }
}