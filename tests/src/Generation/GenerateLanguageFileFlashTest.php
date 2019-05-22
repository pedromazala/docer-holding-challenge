<?php

namespace Test\Language\Generation;

use Language\Generation\GenerateLanguageFileFlash;
use Language\Persistence\FilePersistence;
use Language\Validator\ApiResponseValidator;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Test\Support\Language\Logger\TestLogger;
use Test\Support\Language\Persistence\FakeErrorPersistence;
use Test\Support\Language\Validator\FakeApiValidator;

class GenerateLanguageFileFlashTest extends TestCase
{
    /** @var GenerateLanguageFileFlash */
    private $object;
    /** @var string */
    private $root_path;

    protected function setUp()
    {
        parent::setUp();

        $logger = new TestLogger();
        $validator = new ApiResponseValidator();
        $this->root_path = __DIR__ . '/../../../cache/flash';
        $persistence = new FilePersistence($this->root_path);
        $applets = [
            'memberapplet' => 'JSM2_MemberApplet',
        ];
        $this->object = new GenerateLanguageFileFlash($logger, $validator, $persistence, $applets);
    }

    public function test_generate_applet_language_xml_files()
    {
        $generateLanguageFileFlash = $this->object;
        $base_dir = $this->root_path;

        $generateLanguageFileFlash->generate();
        $output = TestLogger::$output;

        $expected_output = <<<STRING

Getting applet language XMLs..
 Getting > JSM2_MemberApplet (memberapplet) language xmls..
 - Available languages: en
 OK saving {$base_dir}/lang_en.xml was successful.
 < JSM2_MemberApplet (memberapplet) language xml cached.

Applet language XMLs generated.

STRING;
        Assert::assertEquals($expected_output, $output);
    }

    public function test_generate_applet_language_xml_files_removing_cache()
    {
        $generateLanguageFileFlash = $this->object;
        $base_dir = $this->root_path;
        unlink($base_dir . '/lang_en.xml');

        $generateLanguageFileFlash->generate();
        $output = TestLogger::$output;

        $expected_output = <<<STRING

Getting applet language XMLs..
 Getting > JSM2_MemberApplet (memberapplet) language xmls..
 - Available languages: en
 OK saving {$base_dir}/lang_en.xml was successful.
 < JSM2_MemberApplet (memberapplet) language xml cached.

Applet language XMLs generated.

STRING;
        Assert::assertEquals($expected_output, $output);

        Assert::assertFileExists($base_dir . '/lang_en.xml');
    }

    public function test_generate_applet_language_xml_files_validating_api_call()
    {
        $logger = new TestLogger();
        $validator = new FakeApiValidator(0);
        $persistence = new FilePersistence($this->root_path);
        $applets = [
            'memberapplet' => 'JSM2_MemberApplet',
        ];
        $generateLanguageFileFlash = new GenerateLanguageFileFlash($logger, $validator, $persistence, $applets);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Getting languages for applet (JSM2_MemberApplet) was unsuccessful fake api validator');
        $generateLanguageFileFlash->generate();
    }

    public function test_generate_applet_language_xml_files_validating_api_call_with_second_validation()
    {
        $logger = new TestLogger();
        $validator = new FakeApiValidator(1);
        $persistence = new FilePersistence($this->root_path);
        $applets = [
            'memberapplet' => 'JSM2_MemberApplet',
        ];
        $generateLanguageFileFlash = new GenerateLanguageFileFlash($logger, $validator, $persistence, $applets);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Getting language xml for applet: (JSM2_MemberApplet) on language: (en) was unsuccessful: fake api validator');
        $generateLanguageFileFlash->generate();
    }

    public function test_generate_applet_language_xml_files_problem_saving_file()
    {
        $logger = new TestLogger();
        $validator = new ApiResponseValidator();
        $persistence = new FakeErrorPersistence($this->root_path);
        $applets = [
            'memberapplet' => 'JSM2_MemberApplet',
        ];
        $generateLanguageFileFlash = new GenerateLanguageFileFlash($logger, $validator, $persistence, $applets);

        $this->expectException(\Exception::class);
        $base_path = $this->root_path;
        $this->expectExceptionMessage("Unable to save applet: (JSM2_MemberApplet) language: (en) xml ({$base_path}/lang_en.xml)!");
        $generateLanguageFileFlash->generate();
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->object->generate();
        TestLogger::clear();
    }
}
