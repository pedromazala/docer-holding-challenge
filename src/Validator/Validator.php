<?php


namespace Language\Validator;


use Exception;

interface Validator
{
    /**
     * @param $result
     * @throws Exception
     */
    public function validate($result): void;
}
