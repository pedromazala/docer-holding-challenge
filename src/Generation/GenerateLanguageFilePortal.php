<?php

namespace Language\Generation;

use Exception;
use Language\ApiCall;
use Language\Config;
use Language\Logger\Logger;

class GenerateLanguageFilePortal extends AbstractGenerateLanguageFile implements GenerateLanguageFile
{
    /** @var Logger */
    private $logger;
    /** @var array */
    private $applications;

    public function __construct(Logger $logger, array $applications)
    {
        $this->logger = $logger;
        $this->applications = $applications;
    }

    public function generate(): void
    {
        $this->logger->print(PHP_EOL . "Generating language files" . PHP_EOL);
        foreach ($this->applications as $application => $languages) {
            $this->logger->print("[APPLICATION: " . $application . "]" . PHP_EOL);
            foreach ($languages as $language) {
                $this->logger->print("\t[LANGUAGE: " . $language . "]");
                if (!$this->getLanguageFile($application, $language)) {
                    throw new Exception('Unable to generate language file!');
                }

                $this->logger->print(" OK" . PHP_EOL);
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
     * @throws Exception
     */
    private function getLanguageFile($application, $language): bool
    {
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
            $this->validateApiResult($languageResponse);
        } catch (Exception $e) {
            throw new Exception('Error during getting language file: (' . $application . '/' . $language . ')');
        }

        // If we got correct data we store it.
        $destination = $this->getLanguageCachePath($application) . $language . '.php';
        // If there is no folder yet, we'll create it.
        if (!is_dir(dirname($destination))) {
            mkdir(dirname($destination), 0755, true);
        }

        $result = file_put_contents($destination, $languageResponse['data']);

        return (bool)$result;
    }

    private function getLanguageCachePath($application)
    {
        return Config::get('system.paths.root') . '/cache/' . $application . '/';
    }
}
