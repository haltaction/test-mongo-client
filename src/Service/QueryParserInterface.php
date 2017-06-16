<?php

namespace MongoClient\Service;

interface QueryParserInterface
{
    public function parseQuery(array $keywords, string $text);
}