<?php
namespace Language\Api;

use Language\Exceptions\ApiException;

/**
 * Class AppletLanguages
 * @package Language\Api
 */
class AppletLanguageFile extends AbstractApi
{
    /**
     * @var string
     */
    private $applet;

    /**
     * @var string
     */
    private $language;

    public function __construct($applet, $language)
    {
        parent::__construct();

        $this->applet = $applet;
        $this->language = $language;
    }

    /**
     * @param $applet
     */
    public function setApplet($applet)
    {
        $this->applet = $applet;
    }

    /**
     * @return string
     */
    public function getApplet()
    {
        return $this->applet;
    }

    /**
     * @param $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param $language
     * @param $applet
     *
     * @return mixed
     *
     * @throws ApiException
     */
    public function fetch()
    {
        if (empty($this->applet)) {
            throw new ApiException('Wrong parameter "applet"');
        }

        if (empty($this->language)) {
            throw new ApiException('Wrong parameter "language"');
        }

        return $this->call(
            [
                'system' => 'LanguageFiles',
                'action' => 'getAppletLanguageFile'
            ],
            [
                'applet' => $this->applet,
                'language' => $this->language
            ]
        );
    }
}