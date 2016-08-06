<?php
namespace Language;

use Language\Api\ApiInvoker;
use Language\Api\AppletLanguageFile;
use Language\Api\AppletLanguages;
use Language\Api\LanguageFile;
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

    /** @var ApiInvoker */
    private $api;

    /** @var array */
    private $applets;

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

        $this->api = new ApiInvoker;

        $this->applets = [
            'memberapplet' => 'JSM2_MemberApplet'
        ];
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
            $this->cache->setFolder(Config::get('system.paths.root') . '/cache/' . $application . '/');
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
            try {
                $this->generateLanguageFile($language);
                $this->logger->info("\tOK");
            } catch (LanguageBatchBoException $e) {
                $this->logger->error("\tERROR: " . $e->getMessage());
            }
        }
    }

    /**
     * Gets the language file for the given language and stores it.
     *
     * @param string $language The identifier of the language.
     *
     * @return bool The success of the operation.
     *
     * @throws LanguageBatchBoException If there was an error during the download of the language file.
     */
    protected function generateLanguageFile($language)
    {
        $response = $this->api->run(new LanguageFile($language));
        return $this->cache->save((new CacheItem($language . '.php'))->set($response));
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
        $this->logger->info("\nGetting applet language XMLs..\n");
        foreach ($this->applets as $appletDirectory => $appletLanguageId) {
            $this->logger->info(" Getting > $appletLanguageId ($appletDirectory) language xmls..\n");
            $languages = $this->api->run(new AppletLanguages($appletLanguageId));
            if (empty($languages)) {
                throw new \Exception('There is no available languages for the ' . $appletLanguageId . ' applet.');
            } else {
                $this->logger->info(' - Available languages: ' . implode(', ', $languages) . "\n");
            }

            $this->cache->setFolder(Config::get('system.paths.root') . '/cache/flash');
            foreach ($languages as $language) {
                $this->logger->info("\t[LANGUAGE: " . $language . "]");

                try {
                    $this->generateLanguageXmlFile($language, $appletLanguageId);
                    $this->logger->info("\tOK");
                } catch (LanguageBatchBoException $e) {
                    $this->logger->error("\tERROR: " . $e->getMessage());
                }
            }
            $this->logger->info(" < $appletLanguageId ($appletDirectory) language xml cached.\n");
        }

        $this->logger->info("\nApplet language XMLs generated.\n");
    }

    /**
     * @param $language
     * @param $appletLanguageId
     *
     * @return bool
     */
    protected function generateLanguageXmlFile($language, $appletLanguageId)
    {
        $response = $this->api->run(new AppletLanguageFile($language, $appletLanguageId));
        return $this->cache->save((new CacheItem('/lang_' . $language . '.xml'))->set($response));
    }
}
