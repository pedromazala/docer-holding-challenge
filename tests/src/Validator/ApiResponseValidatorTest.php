<?php

namespace Test\Language\Generation;


use Language\Validator\ApiResponseValidator;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class ApiResponseValidatorTest extends TestCase
{
    public function test_expected_data()
    {
        $data = [
            'status' => 'OK',
            'data' => 'some awesome data',
        ];

        $validator = new ApiResponseValidator();
        $validator->validate($data);
        // no one exception was thrown
        Assert::assertTrue(true);
    }

    public function test_error_calling_api_result_false()
    {
        $data = false;

        $validator = new ApiResponseValidator();
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error during the api call');
        $validator->validate($data);
    }

    public function test_error_calling_api_result_without_status()
    {
        $data = ['data' => 'some awesome data'];

        $validator = new ApiResponseValidator();
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error during the api call');
        $validator->validate($data);
    }

    public function test_result_nok_and_error_data()
    {
        $data = [
            'status' => 'NOK',
            'data' => 'some awful data',
            'error_code' => 'code',
            'error_type' => 'type',
        ];

        $validator = new ApiResponseValidator();
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Wrong response: Type(type) Code(code) some awful data');
        $validator->validate($data);
    }

    public function test_data_false()
    {
        $data = [
            'status' => 'OK',
            'data' => false,
        ];

        $validator = new ApiResponseValidator();
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Wrong content!');
        $validator->validate($data);
    }
}
