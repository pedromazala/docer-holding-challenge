<?php


namespace Test\Support\Language;

use Language\Logger\Logger;

class TestLogger implements Logger
{
    static $output = '';

    public function print(string $line)
    {
        self::$output .= $line;
    }

    public static function clear()
    {
        self::$output = '';
    }
}
