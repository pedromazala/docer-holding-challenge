<?php

namespace Language;

use Exception;

/**
 * Business logic related to generating language files.
 */
class LanguageBatchBo
{
    /**
     * Contains the applications which ones require translations.
     *
     * @var array
     */
    protected static $applications = array();

    /**
     * Starts the language file generation.
     *
     * @return void
     * @throws Exception
     */
    public static function generateLanguageFiles()
    {
        // The applications where we need to translate.
        self::$applications = Config::get('system.translated_applications');

        echo PHP_EOL . "Generating language files" . PHP_EOL;
        foreach (self::$applications as $application => $languages) {
            echo "[APPLICATION: " . $application . "]" . PHP_EOL;
            foreach ($languages as $language) {
                echo "\t[LANGUAGE: " . $language . "]";
                if (!self::getLanguageFile($application, $language)) {
                    throw new Exception('Unable to generate language file!');
                }

                echo " OK" . PHP_EOL;
            }
        }
    }

    /**
     * Gets the language file for the given language and stores it.
     *
     * @param string $application The name of the application.
     * @param string $language The identifier of the language.
     *
     * @return bool   The success of the operation.
     * @throws CurlException   If there was an error during the download of the language file.
     * @throws Exception
     */
    protected static function getLanguageFile($application, $language)
    {
        $result = false;
        $languageResponse = ApiCall::call(
            'system_api',
            'language_api',
            array(
                'system' => 'LanguageFiles',
                'action' => 'getLanguageFile',
            ),
            array('language' => $language)
        );

        try {
            self::checkForApiErrorResult($languageResponse);
        } catch (Exception $e) {
            throw new Exception('Error during getting language file: (' . $application . '/' . $language . ')');
        }

        // If we got correct data we store it.
        $destination = self::getLanguageCachePath($application) . $language . '.php';
        // If there is no folder yet, we'll create it.
        if (!is_dir(dirname($destination))) {
            mkdir(dirname($destination), 0755, true);
        }

        $result = file_put_contents($destination, $languageResponse['data']);

        return (bool)$result;
    }

    /**
     * Gets the directory of the cached language files.
     *
     * @param string $application The application.
     *
     * @return string   The directory of the cached language files.
     */
    protected static function getLanguageCachePath($application)
    {
        return Config::get('system.paths.root') . '/cache/' . $application . '/';
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
        // List of the applets [directory => applet_id].
        $applets = array(
            'memberapplet' => 'JSM2_MemberApplet',
        );

        echo PHP_EOL . "Getting applet language XMLs.." . PHP_EOL;

        foreach ($applets as $appletDirectory => $appletLanguageId) {
            echo " Getting > $appletLanguageId ($appletDirectory) language xmls.." . PHP_EOL;
            $languages = self::getAppletLanguages($appletLanguageId);
            if (empty($languages)) {
                throw new Exception('There is no available languages for the ' . $appletLanguageId . ' applet.');
            }
            echo ' - Available languages: ' . implode(', ', $languages) . "" . PHP_EOL;

            $path = Config::get('system.paths.root') . '/cache/flash';
            foreach ($languages as $language) {
                $xmlContent = self::getAppletLanguageFile($appletLanguageId, $language);
                $xmlFile = $path . '/lang_' . $language . '.xml';
                if (strlen($xmlContent) != file_put_contents($xmlFile, $xmlContent)) {
                    throw new Exception('Unable to save applet: (' . $appletLanguageId . ') language: (' . $language . ') xml (' . $xmlFile . ')!');
                }

                echo " OK saving $xmlFile was successful." . PHP_EOL;
            }
            echo " < $appletLanguageId ($appletDirectory) language xml cached." . PHP_EOL;
        }

        echo PHP_EOL . "Applet language XMLs generated." . PHP_EOL;
    }

    /**
     * Gets the available languages for the given applet.
     *
     * @param string $applet The applet identifier.
     *
     * @return array   The list of the available applet languages.
     * @throws Exception
     */
    protected static function getAppletLanguages($applet)
    {
        $result = ApiCall::call(
            'system_api',
            'language_api',
            array(
                'system' => 'LanguageFiles',
                'action' => 'getAppletLanguages',
            ),
            array('applet' => $applet)
        );

        try {
            self::checkForApiErrorResult($result);
        } catch (Exception $e) {
            throw new Exception('Getting languages for applet (' . $applet . ') was unsuccessful ' . $e->getMessage());
        }

        return $result['data'];
    }


    /**
     * Gets a language xml for an applet.
     *
     * @param string $applet The identifier of the applet.
     * @param string $language The language identifier.
     *
     * @return string|false   The content of the language file or false if weren't able to get it.
     * @throws Exception
     */
    protected static function getAppletLanguageFile($applet, $language)
    {
        $result = ApiCall::call(
            'system_api',
            'language_api',
            array(
                'system' => 'LanguageFiles',
                'action' => 'getAppletLanguageFile',
            ),
            array(
                'applet' => $applet,
                'language' => $language,
            )
        );

        try {
            self::checkForApiErrorResult($result);
        } catch (Exception $e) {
            throw new Exception('Getting language xml for applet: (' . $applet . ') on language: (' . $language . ') was unsuccessful: ' . $e->getMessage());
        }

        return $result['data'];
    }

    /**
     * Checks the api call result.
     *
     * @param mixed $result The api call result to check.
     *
     * @return void
     * @throws Exception   If the api call was not successful.
     *
     */
    protected static function checkForApiErrorResult($result)
    {
        // Error during the api call.
        if ($result === false || !isset($result['status'])) {
            throw new Exception('Error during the api call');
        }
        // Wrong response.
        if ($result['status'] != 'OK') {
            throw new Exception('Wrong response: '
                . (!empty($result['error_type']) ? 'Type(' . $result['error_type'] . ') ' : '')
                . (!empty($result['error_code']) ? 'Code(' . $result['error_code'] . ') ' : '')
                . ((string)$result['data']));
        }
        // Wrong content.
        if ($result['data'] === false) {
            throw new Exception('Wrong content!');
        }
    }
}
