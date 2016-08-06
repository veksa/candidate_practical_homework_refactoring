<?php
namespace Language\Api;

use Language\Exceptions\ApiException;

/**
 * Class AppletLanguages
 * @package Language\Api
 */
class AppletLanguages extends AbstractApi
{
    /**
     * @var string
     */
    private $applet;

    public function __construct($applet)
    {
        parent::__construct();

        $this->applet = $applet;
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
     * @param $params
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

        return $this->call(
            [
                'system' => 'LanguageFiles',
                'action' => 'getAppletLanguages'
            ],
            [
                'applet' => $this->applet
            ]
        );
    }
}