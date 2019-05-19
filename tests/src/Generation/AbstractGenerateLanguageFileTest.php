<?php

namespace Test\Language\Generation;


use Language\Generation\AbstractGenerateLanguageFile;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class AbstractGenerateLanguageFileTest extends TestCase
{
    /**
     * @var AbstractGenerateLanguageFile
     */
    private $instanceFromAbstractClass;

    protected function setUp()
    {
        parent::setUp();

        $this->instanceFromAbstractClass = new class extends AbstractGenerateLanguageFile {
            public function generate(): void
            {
                print "do nothing";
            }

            public function exposeValidateApiResult($result): void
            {
                $this->validateApiResult($result);
            }
        };
    }

    public function test_expected_data()
    {
        $data = [
            'status' => 'OK',
            'data' => 'some awesome data',
        ];

        $this->instanceFromAbstractClass->exposeValidateApiResult($data);
        // no one exception was thrown
        Assert::assertTrue(true);
    }

    public function test_error_calling_api_result_false()
    {
        $data = false;

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error during the api call');
        $this->instanceFromAbstractClass->exposeValidateApiResult($data);
    }

    public function test_error_calling_api_result_without_status()
    {
        $data = ['data' => 'some awesome data'];

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error during the api call');
        $this->instanceFromAbstractClass->exposeValidateApiResult($data);
    }

    public function test_result_nok_and_error_data()
    {
        $data = [
            'status' => 'NOK',
            'data' => 'some awful data',
            'error_code' => 'code',
            'error_type' => 'type',
        ];

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Wrong response: Type(type) Code(code) some awful data');
        $this->instanceFromAbstractClass->exposeValidateApiResult($data);
    }

    public function test_data_false()
    {
        $data = [
            'status' => 'OK',
            'data' => false,
        ];

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Wrong content!');
        $this->instanceFromAbstractClass->exposeValidateApiResult($data);
    }
}
