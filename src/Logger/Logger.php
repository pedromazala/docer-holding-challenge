<?php


namespace Language\Logger;


interface Logger
{
    /**
     * Print the wanted log
     *
     * @param string $line
     * @return void
     */
    public function print(string $line);
}
