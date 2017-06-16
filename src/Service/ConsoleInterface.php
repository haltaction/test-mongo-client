<?php

namespace MongoClient\Service;

interface ConsoleInterface
{
    public function handle($arguments, $commandList);
}