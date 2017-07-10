<?php

namespace MongoClient\Command;

class HelpCommand implements CommandInterface
{
    /**
     * Return string of help information.
     *
     * @param array $arguments
     *
     * @return string
     */
    public function run(array $arguments)
    {
        return <<<EOT
For run Select command use 'bin/mongo-client select' without any arguments.
After run input query in SQL syntax, like:
    SELECT [<Projections>] [FROM <Target>]
    [WHERE <Condition>*]
    [ORDER BY <Fields>* [ASC|DESC] *]
    [SKIP <SkipRecords>]
    [LIMIT <MaxRecords>];
End of command setted by symbol ;
Projections can be: *, field, field.subfield
Target: collection name
Condition: [A] AND [B] OR [C], can use AND, OR for logical combination, and =, <>, >, >=, <, <= operators for comparing fields with values
SkipRecords: number of skipped records
MaxRecords: number of maximum amount of records
EOT;
    }
}