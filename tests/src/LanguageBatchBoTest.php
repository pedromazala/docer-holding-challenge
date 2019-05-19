<?php


namespace Test\Language;

use Language\Config;
use Language\LanguageBatchBo;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class LanguageBatchBoTest extends TestCase
{
    public function test_generate_language_files()
    {
        $languageBatchBo = new LanguageBatchBo();

        ob_start();
        $languageBatchBo->generateLanguageFiles();
        $output = ob_get_clean();

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
        $languageBatchBo = new LanguageBatchBo();

        $base_dir = Config::get('system.paths.root');
        unlink($base_dir . '/cache/portal/en.php');
        unlink($base_dir . '/cache/portal/hu.php');
        rmdir($base_dir . '/cache/portal/');

        ob_start();
        $languageBatchBo->generateLanguageFiles();
        $output = ob_get_clean();

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

    public function test_generate_applet_language_xml_files()
    {
        $languageBatchBo = new LanguageBatchBo();
        $base_dir = Config::get('system.paths.root');

        ob_start();
        $languageBatchBo->generateAppletLanguageXmlFiles();
        $output = ob_get_clean();

        $expected_output = <<<STRING

Getting applet language XMLs..
 Getting > JSM2_MemberApplet (memberapplet) language xmls..
 - Available languages: en
 OK saving {$base_dir}/cache/flash/lang_en.xml was successful.
 < JSM2_MemberApplet (memberapplet) language xml cached.

Applet language XMLs generated.

STRING;
        Assert::assertEquals($expected_output, $output);
    }

    public function test_generate_language_files_removing_cache_flash_files()
    {
        $languageBatchBo = new LanguageBatchBo();

        $base_dir = Config::get('system.paths.root');
        unlink($base_dir . '/cache/flash/lang_en.xml');

        ob_start();
        $languageBatchBo->generateAppletLanguageXmlFiles();
        $output = ob_get_clean();

        $expected_output = <<<STRING

Getting applet language XMLs..
 Getting > JSM2_MemberApplet (memberapplet) language xmls..
 - Available languages: en
 OK saving {$base_dir}/cache/flash/lang_en.xml was successful.
 < JSM2_MemberApplet (memberapplet) language xml cached.

Applet language XMLs generated.

STRING;
        Assert::assertEquals($expected_output, $output);

        Assert::assertFileExists($base_dir . '/cache/flash/lang_en.xml');
    }

    public function test_generate_language_xml_files_without_array_cache()
    {
        $languageBatchBo = new LanguageBatchBo();

        $base_dir = Config::get('system.paths.root');
        unlink($base_dir . '/cache/portal/en.php');
        unlink($base_dir . '/cache/portal/hu.php');
        rmdir($base_dir . '/cache/portal/');

        ob_start();
        $languageBatchBo->generateAppletLanguageXmlFiles();
        $output = ob_get_clean();

        $expected_output = <<<STRING

Getting applet language XMLs..
 Getting > JSM2_MemberApplet (memberapplet) language xmls..
 - Available languages: en
 OK saving {$base_dir}/cache/flash/lang_en.xml was successful.
 < JSM2_MemberApplet (memberapplet) language xml cached.

Applet language XMLs generated.

STRING;
        Assert::assertEquals($expected_output, $output);

        Assert::assertFileExists($base_dir . '/cache/flash/lang_en.xml');
    }

    public function test_generate_language_array_files_without_xml_cache()
    {
        $languageBatchBo = new LanguageBatchBo();

        $base_dir = Config::get('system.paths.root');
        unlink($base_dir . '/cache/flash/lang_en.xml');

        ob_start();
        $languageBatchBo->generateLanguageFiles();
        $output = ob_get_clean();

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
}
