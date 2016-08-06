<?php
namespace Language\Api;

use Language\ApiCall;
use Language\Exceptions\LanguageBatchBoException;

/**
 * Class AbstractApi
 * @package Language\Api
 */
abstract class AbstractApi implements ApiInterface
{
    /**
     * @var string
     */
    private $target;
    /**
     * @var string
     */
    private $mode;

    /**
     * AbstractApi constructor.
     */
    public function __construct()
    {
        $this->target = 'system_api';
        $this->mode = 'language_api';
    }

    /**
     * @param array $get
     * @param array $post
     * 
     * @return mixed
     * 
     * @throws LanguageBatchBoException
     */
    public function call(array $get = [], array $post = [])
    {
        $result = ApiCall::call(
            $this->target,
            $this->mode,
            $get,
            $post
        );
        $this->checkForApiErrorResult($result);

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
    private function checkForApiErrorResult($result)
    {
        // Error during the api call.
        if ($result === false || !isset($result['status'])) {
            throw new LanguageBatchBoException('Error during the api call');
        }

        // Wrong response.
        if ($result['status'] != 'OK') {
            throw new LanguageBatchBoException('Wrong response: '
                . (!empty($result['error_type']) ? 'Type(' . $result['error_type'] . ') ' : '')
                . (!empty($result['error_code']) ? 'Code(' . $result['error_code'] . ') ' : '')
                . ((string)$result['data']));
        }

        // Wrong content.
        if ($result['data'] === false) {
            throw new LanguageBatchBoException('Wrong content!');
        }
    }
}