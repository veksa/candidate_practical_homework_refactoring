<?php
namespace Language\Tests;

use Language\LanguageBatchBo;
use Language\Config;

class LanguageBatchBoTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return LanguageBatchBo
     */
    public function testBootstrap()
    {
        $languageBatchBo = new LanguageBatchBo();

        return $languageBatchBo;
    }

    /**
     * @param LanguageBatchBo $languageBatchBo
     *
     * @depends testBootstrap
     */
    public function testGenerateLanguageFiles(LanguageBatchBo $languageBatchBo)
    {
        $languageBatchBo->generateLanguageFiles();

        $path = Config::get('system.paths.root') . '/cache';

        $applications = Config::get('system.translated_applications');
        foreach ($applications as $application => $languages) {
            foreach ($languages as $language) {
                $filePath = $path . '/' . $application . '/' . $language . '.php';
                $this->assertFileExists($filePath);
                $fileContents = file_get_contents($filePath);
                $this->assertNotEmpty($fileContents);

                $this->addToAssertionCount(2);
            }
        }

        $this->assertGreaterThan(0, $this->getNumAssertions());
    }

    /**
     * @param LanguageBatchBo $languageBatchBo
     *
     * @depends testBootstrap
     */
    public function testGenerateAppletLanguageXmlFiles(LanguageBatchBo $languageBatchBo)
    {
        $path = Config::get('system.paths.root') . '/cache/flash';
        $languageBatchBo->getCache()->setFolder($path);

        $languageBatchBo->generateAppletLanguageXmlFiles();

        foreach ($languageBatchBo->getApplets() as $appletDirectory => $appletLanguageId) {
            $languages = $languageBatchBo->getAppletLanguages($appletLanguageId);
            foreach ($languages as $language) {
                $filePath = $path . '/lang_' . $language . '.xml';
                $this->assertFileExists($filePath);
                $fileContents = file_get_contents($filePath);
                $this->assertNotEmpty($fileContents);

                $this->addToAssertionCount(2);
            }
        }

        $this->assertGreaterThan(0, $this->getNumAssertions());
    }
}