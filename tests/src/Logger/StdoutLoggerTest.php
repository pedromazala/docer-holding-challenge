<?php


namespace Test\Language\Logger;

use Language\Logger\StdoutLogger;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class StdoutLoggerTest extends TestCase
{
    public function test_should_print_in_stdout()
    {
        $logger = new StdoutLogger();
        ob_start();
        $logger->print('hey');
        $output = ob_get_clean();

        Assert::assertEquals('hey', $output);
    }
}
