<?php


namespace MongoClient\Command;


class SelectCommand implements CommandInterface
{
    public function run(array $arguments)
    {
        $text = '';
        $handle = fopen ("php://stdin","r+");

        fwrite($handle, getenv('DB_CONNECTION').PHP_EOL);

        while (false !== ($line = fgets($handle))) {
            $text .= $line;
            if(stripos($line, ';') !== false) {
                fwrite($handle, PHP_EOL . $text . PHP_EOL);
                fclose($handle);
                exit('bye' . PHP_EOL);
            }
        }
        return $text;
    }

}