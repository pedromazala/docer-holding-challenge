<?php

namespace Language\Generation;

use Exception;
use Language\ApiCall;
use Language\Logger\Logger;
use Language\Persistence\Persistence;
use Language\Validator\Validator;

class GenerateLanguageFileFlash implements GenerateLanguageFile
{
    /** @var Logger */
    private $logger;
    /** @var Validator */
    private $validator;
    /**@var Persistence */
    private $persistence;
    /** @var array */
    private $applets;

    public function __construct(Logger $logger, Validator $validator, Persistence $persistence, array $applets)
    {
        $this->logger = $logger;
        $this->applets = $applets;
        $this->persistence = $persistence;
        $this->validator = $validator;
    }

    public function generate(): void
    {
        $this->logger->print(PHP_EOL . "Getting applet language XMLs.." . PHP_EOL);

        foreach ($this->applets as $appletDirectory => $appletLanguageId) {
            $this->logger->print(" Getting > $appletLanguageId ($appletDirectory) language xmls.." . PHP_EOL);

            $this->createXmlCacheFilesForApplet($appletLanguageId);

            $this->logger->print(" < $appletLanguageId ($appletDirectory) language xml cached." . PHP_EOL);
        }

        $this->logger->print(PHP_EOL . "Applet language XMLs generated." . PHP_EOL);
    }

    /**
     * @param $appletLanguageId
     * @throws Exception
     */
    private function createXmlCacheFilesForApplet(string $appletLanguageId): void
    {
        $languages = $this->getAppletLanguages($appletLanguageId);
        if (empty($languages)) {
            throw new Exception('There is no available languages for the ' . $appletLanguageId . ' applet.');
        }

        $this->logger->print(' - Available languages: ' . implode(', ', $languages) . "" . PHP_EOL);

        foreach ($languages as $language) {
            $xmlContent = $this->getAppletLanguageFile($appletLanguageId, $language);
            $saved = $this->persistence->save('/lang_' . $language . '.xml', $xmlContent);
            $file = $this->persistence->getLastSavedFilepath();
            if (!$saved) {
                throw new Exception('Unable to save applet: (' . $appletLanguageId . ') language: (' . $language . ') xml (' . $file . ')!');
            }

            $this->logger->print(" OK saving {$file} was successful." . PHP_EOL);
        }
    }


    /**
     * Gets the available languages for the given applet.
     *
     * @param string $applet The applet identifier.
     *
     * @return array   The list of the available applet languages.
     * @throws Exception
     */
    private function getAppletLanguages(string $applet): array
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
            $this->validator->validate($result);
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
     * @return string
     * @throws Exception
     */
    private function getAppletLanguageFile(string $applet, string $language): string
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
            $this->validator->validate($result);
        } catch (Exception $e) {
            throw new Exception('Getting language xml for applet: (' . $applet . ') on language: (' . $language . ') was unsuccessful: ' . $e->getMessage());
        }

        return $result['data'];
    }
}
