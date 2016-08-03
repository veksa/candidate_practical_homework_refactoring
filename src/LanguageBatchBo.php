<?php
namespace Language;

use Language\Cache\CacheItem;
use Language\Cache\FileCache;
use Language\Exceptions\LanguageBatchBoException;
use Language\Logger\CliLogger;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;

/**
 * Business logic related to generating language files.
 */
class LanguageBatchBo
{
    /** @var LoggerInterface */
    private $logger;

    /** @var CacheItemPoolInterface */
    private $cache;

    /**
     * LanguageBatchBo constructor.
     * @param LoggerInterface $logger
     * @param CacheItemPoolInterface $cache
     */
    public function __construct($logger = null, $cache = null)
    {
        $this->logger = $logger;
        if (!$this->logger) {
            $this->logger = new CliLogger($withTimeStamp = false);
        }

        $this->cache = $cache;
        if (!$this->cache) {
            $this->cache = new FileCache;
        }
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param CacheItemPoolInterface $cache
     */
    public function setCache($cache)
    {
        $this->cache = $cache;
    }

    /**
     * Starts the language file generation.
     *
     * @throws LanguageBatchBoException
     */
    public function generateLanguageFiles()
    {
        // The applications where we need to translate.
        $applications = Config::get('system.translated_applications');

        $this->logger->info("\nGenerating language files\n");
        foreach ($applications as $application => $languages) {
            $this->generateApplicationLanguageFile($application, $languages);
        }
    }

    /**
     * Starts the application language file generation.
     *
     * @param $application
     * @param $languages
     *
     * @throws LanguageBatchBoException
     * @throws \Exception
     */
    public function generateApplicationLanguageFile($application, $languages)
    {
        $this->logger->info("[APPLICATION: " . $application . "]\n");
        foreach ($languages as $language) {
            $this->logger->info("\t[LANGUAGE: " . $language . "]");
            if (self::getLanguageFile($application, $language)) {
                $this->logger->info(" OK\n");
            } else {
                throw new LanguageBatchBoException('Unable to generate language file!');
            }
        }
    }

    /**
     * Gets the language file for the given language and stores it.
     *
     * @param string $application The name of the application.
     * @param string $language The identifier of the language.
     *
     * @throws \Exception If there was an error during the download of the language file.
     *
     * @return bool The success of the operation.
     */
    protected function getLanguageFile($application, $language)
    {
        $languageResponse = ApiCall::call(
            'system_api',
            'language_api',
            array(
                'system' => 'LanguageFiles',
                'action' => 'getLanguageFile'
            ),
            array('language' => $language)
        );

        try {
            self::checkForApiErrorResult($languageResponse);
        } catch (\Exception $e) {
            throw new \Exception('Error during getting language file: (' . $application . '/' . $language . ')');
        }

        $this->cache->setFolder(self::getLanguageCachePath($application));
        return $this->cache->save((new CacheItem($language . '.php'))->set($languageResponse['data']));
    }

    /**
     * Gets the directory of the cached language files.
     *
     * @param string $application The application.
     *
     * @return string The directory of the cached language files.
     */
    protected static function getLanguageCachePath($application)
    {
        return Config::get('system.paths.root') . '/cache/' . $application . '/';
    }

    /**
     * Gets the language files for the applet and puts them into the cache.
     *
     * @throws \Exception If there was an error.
     *
     * @return void
     */
    public function generateAppletLanguageXmlFiles()
    {
        // List of the applets [directory => applet_id].
        $applets = array(
            'memberapplet' => 'JSM2_MemberApplet'
        );

        $this->logger->info("\nGetting applet language XMLs..\n");

        foreach ($applets as $appletDirectory => $appletLanguageId) {
            $this->logger->info(" Getting > $appletLanguageId ($appletDirectory) language xmls..\n");
            $languages = self::getAppletLanguages($appletLanguageId);
            if (empty($languages)) {
                throw new \Exception('There is no available languages for the ' . $appletLanguageId . ' applet.');
            } else {
                $this->logger->info(' - Available languages: ' . implode(', ', $languages) . "\n");
            }

            $this->cache->setFolder(Config::get('system.paths.root') . '/cache/flash');
            foreach ($languages as $language) {
                $xmlContent = self::getAppletLanguageFile($appletLanguageId, $language);
                $xmlFile = '/lang_' . $language . '.xml';

                if (!$this->cache->save((new CacheItem($xmlFile))->set($xmlContent))) {
                    throw new LanguageBatchBoException("Error save cache in xml ($xmlFile)");
                }

                $this->logger->info(" OK saving $xmlFile was successful.\n");
            }
            $this->logger->info(" < $appletLanguageId ($appletDirectory) language xml cached.\n");
        }

        $this->logger->info("\nApplet language XMLs generated.\n");
    }

    /**
     * Gets the available languages for the given applet.
     *
     * @param string $applet The applet identifier.
     *
     * @throws \Exception If there was an error.
     *
     * @return array The list of the available applet languages.
     */
    protected static function getAppletLanguages($applet)
    {
        $result = ApiCall::call(
            'system_api',
            'language_api',
            array(
                'system' => 'LanguageFiles',
                'action' => 'getAppletLanguages'
            ),
            array('applet' => $applet)
        );

        try {
            self::checkForApiErrorResult($result);
        } catch (\Exception $e) {
            throw new \Exception('Getting languages for applet (' . $applet . ') was unsuccessful ' . $e->getMessage());
        }

        return $result['data'];
    }


    /**
     * Gets a language xml for an applet.
     *
     * @param string $applet The identifier of the applet.
     * @param string $language The language identifier.
     *
     * @throws \Exception If there was an error.
     *
     * @return string|false The content of the language file or false if weren't able to get it.
     */
    protected static function getAppletLanguageFile($applet, $language)
    {
        $result = ApiCall::call(
            'system_api',
            'language_api',
            array(
                'system' => 'LanguageFiles',
                'action' => 'getAppletLanguageFile'
            ),
            array(
                'applet' => $applet,
                'language' => $language
            )
        );

        try {
            self::checkForApiErrorResult($result);
        } catch (\Exception $e) {
            throw new \Exception('Getting language xml for applet: (' . $applet . ') on language: (' . $language . ') was unsuccessful: ' . $e->getMessage());
        }

        return $result['data'];
    }

    /**
     * Checks the api call result.
     *
     * @param mixed $result The api call result to check.
     *
     * @throws \Exception If the api call was not successful.
     *
     * @return void
     */
    protected static function checkForApiErrorResult($result)
    {
        // Error during the api call.
        if ($result === false || !isset($result['status'])) {
            throw new \Exception('Error during the api call');
        }

        // Wrong response.
        if ($result['status'] != 'OK') {
            throw new \Exception('Wrong response: '
                . (!empty($result['error_type']) ? 'Type(' . $result['error_type'] . ') ' : '')
                . (!empty($result['error_code']) ? 'Code(' . $result['error_code'] . ') ' : '')
                . ((string)$result['data']));
        }

        // Wrong content.
        if ($result['data'] === false) {
            throw new \Exception('Wrong content!');
        }
    }
}
