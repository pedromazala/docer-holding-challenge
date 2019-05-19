<?php

namespace Language\Generation;

use Exception;
use Language\ApiCall;
use Language\Config;
use Language\Logger\Logger;

class GenerateLanguageFileFlash extends AbstractGenerateLanguageFile implements GenerateLanguageFile
{
    /** @var Logger */
    private $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function generate(): void
    {
        // List of the applets [directory => applet_id].
        $applets = array(
            'memberapplet' => 'JSM2_MemberApplet',
        );

        $this->logger->print(PHP_EOL . "Getting applet language XMLs.." . PHP_EOL);

        foreach ($applets as $appletDirectory => $appletLanguageId) {
            $this->logger->print(" Getting > $appletLanguageId ($appletDirectory) language xmls.." . PHP_EOL);
            $languages = $this->getAppletLanguages($appletLanguageId);
            if (empty($languages)) {
                throw new Exception('There is no available languages for the ' . $appletLanguageId . ' applet.');
            }
            $this->logger->print(' - Available languages: ' . implode(', ', $languages) . "" . PHP_EOL);

            $path = Config::get('system.paths.root') . '/cache/flash';
            foreach ($languages as $language) {
                $xmlContent = $this->getAppletLanguageFile($appletLanguageId, $language);
                $xmlFile = $path . '/lang_' . $language . '.xml';
                if (strlen($xmlContent) != file_put_contents($xmlFile, $xmlContent)) {
                    throw new Exception('Unable to save applet: (' . $appletLanguageId . ') language: (' . $language . ') xml (' . $xmlFile . ')!');
                }

                $this->logger->print(" OK saving $xmlFile was successful." . PHP_EOL);
            }
            $this->logger->print(" < $appletLanguageId ($appletDirectory) language xml cached." . PHP_EOL);
        }

        $this->logger->print(PHP_EOL . "Applet language XMLs generated." . PHP_EOL);
    }

    /**
     * Gets the available languages for the given applet.
     *
     * @param string $applet The applet identifier.
     *
     * @return array   The list of the available applet languages.
     * @throws Exception
     */
    private function getAppletLanguages($applet)
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
            $this->validateApiResult($result);
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
    private function getAppletLanguageFile($applet, $language)
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
            $this->validateApiResult($result);
        } catch (Exception $e) {
            throw new Exception('Getting language xml for applet: (' . $applet . ') on language: (' . $language . ') was unsuccessful: ' . $e->getMessage());
        }

        return $result['data'];
    }
}
