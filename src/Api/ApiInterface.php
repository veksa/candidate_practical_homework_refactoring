<?php
namespace Language\Api;

/**
 * Interface ApiInterface
 * 
 * @package Language\Api
 */
interface ApiInterface
{
    /**
     * @param array $get
     * @param array $post
     *
     * @return mixed
     */
    public function call(array $get = [], array $post = []);

    /**
     * @param array $params
     *
     * @return mixed
     */
    public function fetch();
}