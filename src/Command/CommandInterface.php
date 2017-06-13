<?php

namespace MongoClient\Command;

interface CommandInterface
{
    public function run(array $arguments);
}