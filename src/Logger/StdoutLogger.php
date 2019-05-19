<?php


namespace Language\Logger;


class StdoutLogger implements Logger
{
    public function print(string $line)
    {
        echo $line;
    }
}
