<?php

namespace Test\Language\Generation;

use Language\Generation\GenerateLanguageFilePortal;
use Language\Validator\ApiResponseValidator;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Test\Support\Language\Logger\TestLogger;
use Test\Support\Language\Validator\FakeApiValidator;

class GenerateLanguageFilePortalTest extends TestCase
{
    /** @var GenerateLanguageFilePortal */
    private $object;
    /** @var string */
    private $root_path;

    protected function setUp()
    {
        parent::setUp();

        $logger = new TestLogger();
        $validator = new ApiResponseValidator();
        $this->root_path = __DIR__ . '/../../..';
        $applications = ['portal' => ['en', 'hu']];
        $this->object = new GenerateLanguageFilePortal($logger, $validator, $this->root_path, $applications);
    }

    public function test_generate_language_files()
    {
        $generateLanguageFilePortal = $this->object;

        $generateLanguageFilePortal->generate();
        $output = TestLogger::$output;

        $expected_output = <<<STRING

Generating language files
[APPLICATION: portal]
	[LANGUAGE: en] OK
	[LANGUAGE: hu] OK

STRING;
        Assert::assertEquals($expected_output, $output);
    }

    public function test_generate_language_files_removing_cache_directory_portal()
    {
        $generateLanguageFilePortal = $this->object;

        $base_dir = $this->root_path;
        unlink($base_dir . '/cache/portal/en.php');
        unlink($base_dir . '/cache/portal/hu.php');
        rmdir($base_dir . '/cache/portal/');

        $generateLanguageFilePortal->generate();
        $output = TestLogger::$output;

        $expected_output = <<<STRING

Generating language files
[APPLICATION: portal]
	[LANGUAGE: en] OK
	[LANGUAGE: hu] OK

STRING;
        Assert::assertEquals($expected_output, $output);

        Assert::assertFileExists($base_dir . '/cache/portal/en.php');
        Assert::assertFileExists($base_dir . '/cache/portal/hu.php');
    }

    public function test_generate_language_files_validating_api_call()
    {
        $logger = new TestLogger();
        $validator = new FakeApiValidator(0);
        $this->root_path = __DIR__ . '/../../..';
        $applications = ['portal' => ['en']];
        $generateLanguageFilePortal = new GenerateLanguageFilePortal($logger, $validator, $this->root_path, $applications);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error during getting language file: (portal/en)');
        $generateLanguageFilePortal->generate();
    }

    protected function tearDown()
    {
        parent::tearDown();
        TestLogger::clear();
    }
}
