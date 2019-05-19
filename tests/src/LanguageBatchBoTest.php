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
        $base_dir = Config::get('system.paths.root');
        $string_length = self::getStringLength($base_dir);

        ob_start();
        $languageBatchBo->generateLanguageFiles();
        $output = ob_get_clean();

        $expected_output = <<<STRING

Generating language files
[APPLICATION: portal]
	[LANGUAGE: en]{$base_dir}/src/LanguageBatchBo.php:75:
string({$string_length}) "{$base_dir}/cache/portal/en.php"
 OK
	[LANGUAGE: hu]{$base_dir}/src/LanguageBatchBo.php:75:
string({$string_length}) "{$base_dir}/cache/portal/hu.php"
 OK

STRING;
        Assert::assertEquals($expected_output, $output);
    }

    public function test_generate_language_files_removing_cache_directory_portal()
    {
        $languageBatchBo = new LanguageBatchBo();
        $base_dir = Config::get('system.paths.root');
        $string_length = self::getStringLength($base_dir);
        unlink($base_dir . '/cache/portal/en.php');
        unlink($base_dir . '/cache/portal/hu.php');
        rmdir($base_dir . '/cache/portal/');

        ob_start();
        $languageBatchBo->generateLanguageFiles();
        $output = ob_get_clean();

        $expected_output = <<<STRING

Generating language files
[APPLICATION: portal]
	[LANGUAGE: en]{$base_dir}/src/LanguageBatchBo.php:75:
string({$string_length}) "{$base_dir}/cache/portal/en.php"
 OK
	[LANGUAGE: hu]{$base_dir}/src/LanguageBatchBo.php:75:
string({$string_length}) "{$base_dir}/cache/portal/hu.php"
 OK

STRING;
        Assert::assertEquals($expected_output, $output);
    }

    public function test_generate_language_files_removing_cache_directory_portal_and_flash_files()
    {
        $languageBatchBo = new LanguageBatchBo();
        $base_dir = Config::get('system.paths.root');
        $string_length = self::getStringLength($base_dir);
        unlink($base_dir . '/cache/portal/en.php');
        unlink($base_dir . '/cache/portal/hu.php');
        rmdir($base_dir . '/cache/portal/');
        unlink($base_dir . '/cache/flash/lang_en.xml');

        ob_start();
        $languageBatchBo->generateLanguageFiles();
        $output = ob_get_clean();

        $expected_output = <<<STRING

Generating language files
[APPLICATION: portal]
	[LANGUAGE: en]{$base_dir}/src/LanguageBatchBo.php:75:
string({$string_length}) "{$base_dir}/cache/portal/en.php"
 OK
	[LANGUAGE: hu]{$base_dir}/src/LanguageBatchBo.php:75:
string({$string_length}) "{$base_dir}/cache/portal/hu.php"
 OK

STRING;
        Assert::assertEquals($expected_output, $output);
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

    /**
     * Return string length of tested paths (Directory length + 20)
     *
     * @param string $base_dir
     * @return int
     */
    private static function getStringLength(string $base_dir)
    {
        return strlen($base_dir) + 20;
    }
}
