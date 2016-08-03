<?php
namespace Language\Exceptions;

use Exception;

class CacheException extends Exception implements \Psr\Cache\CacheException
{
}