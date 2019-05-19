<?php

namespace Language;

use Exception;
use Language\Generation\GenerateLanguageFileFlash;
use Language\Generation\GenerateLanguageFilePortal;
use Language\Logger\Logger;
use Language\Logger\StdoutLogger;

/**
 * Business logic related to generating language files.
 */
class LanguageBatchBo
{
    /**
     * @var Logger
     */
    private static $logger;

    public static function setLogger(?Logger $logger)
    {
        self::$logger = $logger;
    }

    public static function getLogger(): Logger
    {
        self::initLogger();
        return self::$logger;
    }

    /**
     * Starts the language file generation.
     *
     * @return void
     * @throws Exception
     */
    public static function generateLanguageFiles()
    {
        self::initLogger();
        $generateLanguageFilePortal = new GenerateLanguageFilePortal(
            self::$logger,
            Config::get('system.translated_applications')
        );

        $generateLanguageFilePortal->generate();
    }

    /**
     * Gets the language files for the applet and puts them into the cache.
     *
     * @return void
     * @throws Exception   If there was an error.
     *
     */
    public static function generateAppletLanguageXmlFiles()
    {
        self::initLogger();
        $generateLanguageFileFlash = new GenerateLanguageFileFlash(self::$logger);

        $generateLanguageFileFlash->generate();
    }

    private static function initLogger(): void
    {
        if (is_null(self::$logger)) {
            self::$logger = new StdoutLogger();
        }
    }
}
