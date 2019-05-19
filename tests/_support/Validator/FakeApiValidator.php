<?php

namespace Test\Support\Language\Validator;

use Exception;
use Language\Validator\Validator;

class FakeApiValidator implements Validator
{
    /** @var int */
    private $calls_before_exception;

    public function __construct(int $calls_before_exception)
    {
        $this->calls_before_exception = $calls_before_exception;
    }

    public function validate($result): void
    {
        $this->calls_before_exception--;
        if ($this->calls_before_exception < 0) {
            throw new Exception('fake api validator');
        }
    }
}
