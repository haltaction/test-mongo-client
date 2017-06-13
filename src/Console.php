<?php

namespace MongoClient;


use Exception;

class Console
{
    const COMMAND_NAMESPACE = 'MongoClient\\Command\\';

    public function handle($arguments, $commandList)
    {
        $commandName = $this->filterCommandName($arguments);
        try {
            // todo move class creation
            $className = $this->findCommand($commandName, $commandList);
            $className = self::COMMAND_NAMESPACE.$className;
            $command = new $className;
            $commandResult = $command->run($arguments);
            $this->showResponse($commandResult);
        } catch (Exception $e) {
            $this->showResponse($e->getMessage(), 'error');
        }
    }

    /**
     * Return name of console command from array of arguments.
     *
     * @param $arguments
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
     * @return mixed
     * @throws Exception
     */
    public function findCommand($commandName, $commandList)
    {
        $commandName = strtolower($commandName);
        if (!in_array($commandName, array_keys($commandList))) {
            throw new Exception("Command not found!", 404);
        }

        return $commandList[$commandName];
    }

    public function showResponse($text, $type = 'text')
    {
        exit($text.PHP_EOL);
    }

}