<?php

namespace Language\Generation;

use Exception;
use Language\ApiCall;
use Language\Logger\Logger;
use Language\Persistence\Persistence;
use Language\Validator\Validator;

class GenerateLanguageFilePortal implements GenerateLanguageFile
{
    /** @var Logger */
    private $logger;
    /** @var Validator */
    private $validator;
    /** @var Persistence */
    private $persistence;
    /** @var array */
    private $applications;

    public function __construct(Logger $logger, Validator $validator, Persistence $persistence, array $applications)
    {
        $this->logger = $logger;
        $this->applications = $applications;
        $this->persistence = $persistence;
        $this->validator = $validator;
    }

    public function generate(): void
    {
        $this->logger->print(PHP_EOL . "Generating language files" . PHP_EOL);
        foreach ($this->applications as $application => $languages) {
            $this->logger->print("[APPLICATION: " . $application . "]" . PHP_EOL);

            $this->getLanguageFiles($application, $languages);
        }
    }

    /**
     * @param $languages
     * @param $application
     * @throws Exception
     */
    private function getLanguageFiles(string $application, array $languages): void
    {
        foreach ($languages as $language) {
            $this->logger->print("\t[LANGUAGE: " . $language . "]");
            if (!$this->getLanguageFile($application, $language)) {
                throw new Exception('Unable to generate language file!');
            }

            $this->logger->print(" OK" . PHP_EOL);
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
    private function getLanguageFile(string $application, string $language): bool
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
            $this->validator->validate($languageResponse);
        } catch (Exception $e) {
            throw new Exception('Error during getting language file: (' . $application . '/' . $language . ')');
        }

        // If we got correct data we store it.
        $saved = $this->persistence->save("/{$application}/{$language}.php", $languageResponse['data']);

        return $saved;
    }
}
