<?php
namespace Language\Api;

use Language\Exceptions\ApiException;

/**
 * Class LanguageFile
 * @package Language\Api
 */
class LanguageFile extends AbstractApi
{
    /**
     * @var string
     */
    private $language;

    public function __construct($language)
    {
        parent::__construct();
        
        $this->language = $language;
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
     * @param $params
     *
     * @return mixed
     *
     * @throws ApiException
     */
    public function fetch()
    {
        if (empty($this->language)) {
            throw new ApiException('Wrong parameter "language"');
        }

        return $this->call(
            [
                'system' => 'LanguageFiles',
                'action' => 'getLanguageFile'
            ],
            [
                'language' => $this->language
            ]
        );
    }
}