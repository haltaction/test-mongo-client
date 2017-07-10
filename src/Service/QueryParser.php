<?php

namespace MongoClient\Service;

use Exception;

class QueryParser implements QueryParserInterface
{
    /**
     * Parse text by keywords, return keywords and parameters for them.
     *
     * @param array  $keywords
     * @param string $text
     *
     * @return array|null|false
     */
    public function parseQuery(array $keywords, string $text)
    {
        $names = array_keys($keywords);
        $parts = $this->parseMainQuery($names, $text);
        $this->validate($keywords, $parts);

        return $parts;
    }

    /**
     * Check if all required keys exist in parsed query.
     *
     * @param array $keywords
     * @param array $parts
     * @return bool
     * @throws Exception
     */
    public function validate(array $keywords, array $parts)
    {
        foreach ($keywords as $key=>$value) {
            if ($value === 1) {
                if (!array_key_exists($key, $parts)) {
                    throw new Exception("Query is invalid, required key $key missing!");
                }
            }
        }

        return true;
    }

    /**
     * Split text of query by unique keywords and return string of parameters for each keyword.
     *
     * @param array $keywords
     * @param string $text
     * @return array|null
     */
    public function parseMainQuery(array $keywords, string $text)
    {
        if (empty($keyword) && empty($text)) {
            return [];
        }

        $positions = [];
        foreach ($keywords as $keyword) {
            $pos = stripos($text, $keyword);
            if ($pos !== false) {
                $positions[$keyword] = $pos;
            }
        }

        asort($positions, SORT_NUMERIC);

        $parts = [];
        foreach ($positions as $key => $position) {
            $start = $position + strlen($key);
            $next = next($positions);
            if ($next === false) {
                $next = strlen($text);
            }
            $lengthToNext = $next - $start;
            $parts[$key] = substr($text, $start, $lengthToNext);
        }

        return $parts;
    }

    /**
     * Split string on conditions with operators.
     *
     * @param array $keys
     * @param string $text
     * @return array
     */
    public function parseConditions(array $keys, string $text)
    {
        $positions = [];
        $ranges = [];
        foreach ($keys as $key) {
            $pos = stripos($text, $key);
            if (($pos !== false) && !in_array($pos, $ranges)) {
                $positions[$pos] = $key;
                $ranges = array_merge($ranges, range($pos, $pos + strlen($key)));
            }
        }
        asort($positions, SORT_NUMERIC);

        $lastPosition = 0;
        $conditions = [];
        foreach ($positions as $position=>$key) {
            $length = $position - $lastPosition;
            $condition = substr($text, $lastPosition, $length);
            $condition = trim($condition);
            $operator = $key;
            $lastPosition = $position + strlen($key);

            (!$condition) ?: $conditions[] = $condition;
            (!$operator) ?: $conditions[] = $operator;
        }

        if ($lastPosition <= (strlen($text) - 1)) {
            $condition = substr($text, $lastPosition);
            $condition = trim($condition);
            $conditions[] = $condition;
        }

        return $conditions;
    }
}