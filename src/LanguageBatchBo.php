<?php

namespace Language;

use Exception;
use Language\Generation\GenerateLanguageFileFlash;
use Language\Generation\GenerateLanguageFilePortal;
use Language\Logger\StdoutLogger;
use Language\Persistence\FilePersistence;
use Language\Validator\ApiResponseValidator;

/**
 * Business logic related to generating language files.
 */
class LanguageBatchBo
{
    /**
     * Starts the language file generation.
     *
     * @return void
     * @throws Exception
     */
    public static function generateLanguageFiles()
    {
        $logger = new StdoutLogger();
        $validator = new ApiResponseValidator();
        $root_path = Config::get('system.paths.root');
        $persistence = new FilePersistence($root_path . '/cache');
        $applications = Config::get('system.translated_applications');

        $generateLanguageFilePortal = new GenerateLanguageFilePortal($logger, $validator, $persistence, $applications);
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
        $logger = new StdoutLogger();
        $validator = new ApiResponseValidator();
        // List of the applets [directory => applet_id].
        $root_path = Config::get('system.paths.root');
        $persistence = new FilePersistence($root_path . '/cache/flash');
        $applets = [
            'memberapplet' => 'JSM2_MemberApplet',
        ];


        $generateLanguageFileFlash = new GenerateLanguageFileFlash($logger, $validator, $persistence, $applets);
        $generateLanguageFileFlash->generate();
    }
}
