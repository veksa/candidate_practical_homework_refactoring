<?php
namespace Language\Api;

use Language\Exceptions\ApiException;

/**
 * Class ApiInvoker
 * @package Language\Api
 */
class ApiInvoker
{
    /**
     * @var ApiInterface
     */
    protected $api;

    /**
     * @param ApiInterface $api
     */
    public function setApi(ApiInterface $api)
    {
        $this->api = $api;
    }

    /**
     * @return ApiInterface
     */
    public function getApi()
    {
        return $this->api;
    }

    /**
     *
     */
    public function run(ApiInterface $api = null)
    {
        if ($api) {
            $this->setApi($api);
        }

        if (!$api) {
            throw new ApiException('undefined api method');
        }

        return $this->api->fetch();
    }
}